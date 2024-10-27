<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn, $MEMBER_ID;

  header('Content-Type: application/json');
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $storeId = $_POST['id']??null;
    $stmt = bindPrepare($conn, "
      INSERT INTO histories (member_id, action, target_store)
      VALUES (?, '瀏覽', ?)
    ", "ii", $MEMBER_ID, $storeId);
    echo json_encode(['success' => $stmt->execute()]);
    $stmt->close();
  }