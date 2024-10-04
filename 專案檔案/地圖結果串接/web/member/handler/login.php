<?php  
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = null;
    $member_name = null;
    $hashedPassword = null;
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = bindPrepare($conn, 
    " SELECT `id`, `name`, `password` FROM members 
      WHERE `email` = ?
    ", "s", $email);
    $stmt->execute();
    $stmt->bind_result($member_id, $member_name, $hashedPassword);
    $stmt->fetch();
    $stmt->close();
    if ($member_id&&$member_name&&$hashedPassword) {
      if (password_verify($password, $hashedPassword)) {
        $token = generateToken(40);
        date_default_timezone_set("Asia/Taipei");
        $expires_at = date("Y-m-d H:i:s", time() + $TOKEN_EXPIRATION_TIME); // 設置有效期
        $stmt = bindPrepare($conn,
        "INSERT INTO tokens(`member_id`, `token`, `expiration_time`)
          VALUE (?,?,?)
        ", "iss", $member_id, $token, $expires_at);
        $stmt->execute();
        $_SESSION['member_id'] = $member_id;
        $_SESSION['token'] = $token;
        echo json_encode(['success' => true, 'id' => $member_id]);
      } else {
        echo json_encode(['success' => false, 'message' => '帳號或密碼不正確']);
      }
    } else {
      echo json_encode(['success' => false, 'message' => '該帳號尚未註冊會員']);
    }
  }