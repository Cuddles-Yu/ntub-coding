<?php  
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn;

  header('Content-Type: application/json');
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $memberId = null;
    $memberName = null;
    $hashedPassword = null;
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = $_POST['remember']==='1'?true:false;

    $stmt = bindPrepare($conn, 
    " SELECT `id`, `name`, `password` FROM members 
      WHERE `email` = ?
    ", "s", $email);
    $stmt->execute();
    $stmt->bind_result($memberId, $memberName, $hashedPassword);
    $stmt->fetch();
    $stmt->close();
    if ($memberId&&$memberName&&$hashedPassword) {
      if (password_verify($password, $hashedPassword)) {
        $token = generateToken(40);
        date_default_timezone_set("Asia/Taipei");
        $expiryTime = time() + $TOKEN_EXPIRATION_TIME;
        $expiryFormat = date("Y-m-d H:i:s", $expiryTime); // 設置有效期
        $stmt = bindPrepare($conn,
        "INSERT INTO tokens(`member_id`, `token`, `expiration_time`)
          VALUE (?,?,?)
        ", "iss", $memberId, $token, $expiryFormat);
        $stmt->execute();
        $_SESSION['member_id'] = $memberId;
        $_SESSION['token'] = $token;
        if ($remember) {
          setcookie('remember', '1', $expiryTime, "/", "", false, true);
        } else{
          setcookie('remember', '0', 0, "/", "", false, true);
        }
        echo json_encode(['success' => true, 'id' => $memberId, 'name' => $memberName]);
      } else {
        echo json_encode(['success' => false, 'message' => '帳號或密碼不正確']);
      }
    } else {
      echo json_encode(['success' => false, 'message' => '該帳號尚未註冊會員']);
    }
  }