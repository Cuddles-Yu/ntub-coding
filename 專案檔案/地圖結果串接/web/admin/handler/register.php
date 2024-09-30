<?php  
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];

    ### 檢查帳號是否已存在 ###
    $stmt = $conn->prepare("SELECT COUNT(*) FROM administrators WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    
    if ($count > 0) {
      echo json_encode(['success' => false, 'message' => '已存在相同名稱的管理員帳號']);
    } else {
      ### 創建新管理者帳號 ###
      $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
      $authKey = generateToken(40);
      $stmt = $conn->prepare(query: 
      " INSERT INTO administrators 
        VALUE ('$name','$hashedPassword','$authKey',DEFAULT)
      ");
      $stmt->bind_param("ss", $name, $hashedPassword);
      if ($stmt->execute()) {
        echo json_encode(['success' => true, 'token' => $authKey]);
      } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
      }
      $stmt->close();
    }    
  }
