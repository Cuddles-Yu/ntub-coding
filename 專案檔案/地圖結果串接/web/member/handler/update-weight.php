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
    $atmosphere = intval($_POST['atmosphere']);
    $product = intval($_POST['product']);
    $service = intval($_POST['service']);
    $price = intval($_POST['price']);

    ### 更新偏好設定 ###
    $stmt = bindPrepare($conn, "
      UPDATE preferences
      SET atmosphere_weight=?, product_weight=?, service_weight=?, price_weight=?
      WHERE member_id = ?
    ", "iiiii",
    $atmosphere, $product, $service, $price, $MEMBER_ID
    );
    if ($stmt->execute()) {
      echo json_encode(['success' => true, 'message' => '會員權重修改成功']);
    } else {
      echo json_encode(['success' => false, 'message' => $conn->error]);
    }    
    $stmt->close();
  }