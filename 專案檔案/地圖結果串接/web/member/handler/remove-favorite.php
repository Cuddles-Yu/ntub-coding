<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn, $MEMBER_ID;

  header('Content-Type: application/json');
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (is_null($MEMBER_ID)) {
      echo json_encode(['success' => false, 'message' => '尚未登入帳號']);
      exit();
    }
    $storeId = $_POST['id']??null;

    ### 更新偏好設定 ###
    $stmt = bindPrepare($conn, "
      DELETE FROM favorites
      WHERE member_id = ? and store_id = ?
    ", "ii", $MEMBER_ID, $storeId);
    if ($stmt->execute()) {
      echo json_encode(['success' => true, 'message' => '已刪除所選收藏']);
    } else {
      echo json_encode(['success' => false, 'message' => $conn->error]);
    }
    $stmt->close();
  }