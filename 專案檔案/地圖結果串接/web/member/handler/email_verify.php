<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn;

  header('Content-Type: application/json');
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $count = null;
    $email = $_POST['email'];

    ### 檢查帳號是否已存在 ###
    $stmt = bindPrepare($conn,
    " SELECT COUNT(*) FROM members WHERE email = ?
    ", "s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
      echo json_encode(['success' => false, 'message' => '該帳號已被註冊，請更換其他帳號']);
    } else {
      echo json_encode(['success' => true, 'message' => '該帳號尚未被註冊']);
    }
  }