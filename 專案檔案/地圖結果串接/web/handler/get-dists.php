<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn;

  header('Content-Type: application/json');
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $CITY = $_POST['city'];
    $stmt = bindPrepare($conn, "
      SELECT DISTINCT dist FROM locations
      WHERE city = ?
    ", "s", $CITY);
    $stmt->execute();
    $result = $stmt->get_result();
    $districts = [];
    while ($row = $result->fetch_assoc()) {
      $districts[] = $row['dist'];
    }
    $stmt->close();
    echo json_encode($districts);
  }