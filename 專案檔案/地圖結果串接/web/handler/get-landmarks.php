<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn;

  header('Content-Type: application/json');
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST['category'];
    $stmt = bindPrepare($conn, "
      SELECT DISTINCT name, longitude, latitude FROM landmarks
      WHERE category = ?
    ", "s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    $landmarks = [];
    while ($row = $result->fetch_assoc()) {
      $landmarks[] = $row;
    }
    $stmt->close();
    echo json_encode($landmarks);
  }