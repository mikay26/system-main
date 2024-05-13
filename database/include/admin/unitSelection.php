<?php
include('../../config/dbcon.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

  if ($conn->connect_error) {
    http_response_code(500);
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
  } else {
    $stmt = $conn->prepare("SELECT id, unitname, unitno, unitstatus, unitposition, unitprice, unittype FROM units WHERE status = 1");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    header("Content-Type: application/json");
    echo json_encode($result);

    $stmt->close();
    $conn->close();
  }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);

  if (isset($input['action']) && $input['action'] === 'remove' && isset($input['id'])) {
    $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

    if ($conn->connect_error) {
      http_response_code(500);
      header("Content-Type: application/json");
      echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    } else {
      $stmt = $conn->prepare("UPDATE units SET status = 0 WHERE id = ?");
      $stmt->bind_param('i', $input['id']);
      $stmt->execute();

      header("Content-Type: application/json");
      echo json_encode(['message' => 'Account removed successfully']);

      $stmt->close();
      $conn->close();
    }
  } else {
    http_response_code(400);
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Invalid request']);
  }
} else {
  http_response_code(400);
  header("Content-Type: application/json");
  echo json_encode(['error' => 'Invalid request']);
}
