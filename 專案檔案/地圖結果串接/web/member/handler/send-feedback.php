<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn, $MEMBER_ID;

  header('Content-Type: application/json');
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email']??null;
    $suggestions = $_POST['suggestions']??null;
    $thoughts = $_POST['thoughts']??null;
    if ($MEMBER_ID) {
      $stmt = bindPrepare($conn, "
        INSERT INTO feedbacks (member_id, email, advice, content)
        VALUES (?, ?, ?, ?)
      ", "siss", $MEMBER_ID, $email, $suggestions, $thoughts);
    } else {
      $stmt = bindPrepare($conn, "
        INSERT INTO feedbacks (email, advice, content)
        VALUES (?, ?, ?)
      ", "sss", $email, $suggestions, $thoughts);
    }
    if ($stmt->execute()) {
      echo json_encode(['success' => true, 'message' => '我們已收到您的建議']);
    } else {
      echo json_encode(['success' => false, 'message' => '發生錯誤，請稍後再試']);
    }
    $stmt->close();
  }