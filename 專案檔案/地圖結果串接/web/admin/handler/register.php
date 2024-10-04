<?php  
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $count = null;
    $name = $_POST['name'];

    ### 檢查帳號是否已存在 ###
    $stmt = bindPrepare($conn, 
    " SELECT COUNT(*) FROM administrators WHERE name = ?
    ", "s", $name);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    
    if ($count > 0) {
      echo json_encode(['success' => false, 'message' => '已存在相同名稱的管理員帳號']);
      exit;
    } 

    ### 創建新管理者帳號 ###
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $authKey = generateToken(40);
    $stmt = bindPrepare($conn,
    " INSERT INTO administrators(`name`,`password`,`auth_key`)
      VALUE (?,?,?)
    ", "sss", $name, $hashedPassword, $authKey);
    if ($stmt->execute()) {
      echo json_encode(['success' => true, 'token' => $authKey]);
    } else {
      echo json_encode(['success' => false, 'message' => $conn->error]);
    }
    $stmt->close();
  }