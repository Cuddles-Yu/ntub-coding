<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn;

  if (isset($_POST['name']) && isset($_POST['password'])) {
    $name = $_POST['name'];
    $password = $_POST['password'];
    $authKey = '';
    $hashedPassword = '';
    ### 驗證帳號密碼 ###
    $stmt = $conn->prepare(query: 
    " SELECT password, auth_key FROM administrators 
      WHERE name = ?
    ");
    $stmt->bind_param("s", $name);  // 綁定使用者的名稱
    $stmt->execute();
    $stmt->bind_result($hashedPassword, $authKey);
    $stmt->fetch();
    $stmt->close();

    if ($hashedPassword && password_verify($password, $hashedPassword)) {
        echo json_encode(['success' => true, 'token' => $authKey]); // 假設 auth_key 作為 token 返回
    } else {
        echo json_encode(['success' => false, 'message' => '帳號或密碼不正確']);
    }
  }
