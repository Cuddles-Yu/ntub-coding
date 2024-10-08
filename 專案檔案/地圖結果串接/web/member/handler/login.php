<?php  
  header("Access-Control-Allow-Origin: https://commentspace.ascdc.tw");
  header("Access-Control-Allow-Credentials: true");
  header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type");
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
    if (!is_null($memberId)) {
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
          setcookie('remember', '1', $expiryTime, "/", "commentspace.ascdc.tw", false, true);
          // header('Set-Cookie: remember=1; Path=/; SameSite=None; Secure; HttpOnly');
        } else{
          setcookie('remember', '0', 0, "/", "commentspace.ascdc.tw", false, true);
          // header('Set-Cookie: remember=0; Path=/; SameSite=None; Secure; HttpOnly');
        }
        echo json_encode(['success' => true, 'id' => $memberId, 'name' => $memberName]);
      } else {
        echo json_encode(['success' => false, 'message' => '帳號或密碼不正確']);
      }
    } else {
      echo json_encode(['success' => false, 'message' => '該帳號尚未被註冊']);
    }
  }