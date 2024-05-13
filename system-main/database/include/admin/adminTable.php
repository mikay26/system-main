<?php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header("Access-Control-Allow-Origin: http://localhost:9000");
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type");
  http_response_code(200);
  exit;
}
header("Access-Control-Allow-Origin: http://localhost:9000");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once('../../config/dbcon.php');

$database = new Database();

if ($database->dbState()) {

  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conn = $database->dbConn();

    $stmt = $conn->prepare("SELECT id, givenname, middlename, surname, email, username, birthdate, gender FROM admins WHERE status = 1");
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
  } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['action']) && $input['action'] === 'remove' && isset($input['id'])) {
      $conn = $database->dbConn();
      $stmt = $conn->prepare("UPDATE admins SET status = 0 WHERE id = :id");
      $stmt->bindParam(':id', $input['id']);
      $stmt->execute();

      echo json_encode(['message' => 'Account removed successfully']);
    } else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request']);
    }
  } else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
  }
} else {
  http_response_code(500);
  echo json_encode(['error' => $database->errMsg()]);
}
