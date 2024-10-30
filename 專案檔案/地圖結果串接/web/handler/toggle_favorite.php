<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn, $MEMBER_ID;

  header('Content-Type: application/json');
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (is_null($MEMBER_ID)) {
      echo json_encode(['success' => false, 'message' => '您需要先登入會員才能進行餐廳收藏']);
      exit();
    }
    $storeId = $_POST['storeId'];
    $currentState = $_POST['currentState'];

    // 檢查 storeId 是否存在
    $stmt = bindPrepare($conn, "
      SELECT COUNT(*) FROM stores
      WHERE id = ?
    ", "i", $storeId);
    $stmt->execute();
    $stmt->bind_result($storeCount);
    $stmt->fetch();
    $stmt->close();

    if ($storeCount == 0) {
      echo json_encode(['success' => false, 'message' => '餐廳不存在']);
      exit();
    }

    $count = 0;
    $stmt = bindPrepare($conn, "
      SELECT COUNT(*) FROM favorites
      WHERE member_id = ? AND store_id = ?
    ", "ii", $MEMBER_ID, $storeId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count>0) {
      if ($currentState == '0') {
        echo json_encode(['success' => true, 'isFavorite' => true, 'refresh' => true, 'message' => '該餐廳已加入至收藏']);
        exit();
      }
      $stmt = bindPrepare($conn, "
        DELETE FROM favorites
        WHERE member_id = ? AND store_id = ?
      ", "ii", $MEMBER_ID, $storeId);
      if ($stmt->execute()) {
        echo json_encode(['success' => true, 'isFavorite' => false, 'message' => '已將該餐廳從收藏中移除']);
      } else {
        echo json_encode(['success' => false, 'refresh' => false, 'message' => $conn->error]);
      }
      $stmt->close();
    } else {
      if ($currentState == '1') {
        echo json_encode(['success' => true, 'isFavorite' => false, 'refresh' => true, 'message' => '該餐廳已從收藏中移除']);
        exit();
      }
      $stmt = bindPrepare($conn, "
        INSERT INTO favorites (member_id, store_id)
        VALUES (?, ?)
      ", "ii", $MEMBER_ID, $storeId);
      if ($stmt->execute()) {
        echo json_encode(['success' => true, 'isFavorite' => true, 'refresh' => false, 'message' => '已將該餐廳加入收藏']);
      } else {
        echo json_encode(['success' => false, 'refresh' => false, 'message' => $conn->error]);
      }
      $stmt->close();
    }
  }