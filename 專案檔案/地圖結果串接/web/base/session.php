<?php
  $TOKEN_EXPIRATION_TIME = 3 *(60*60*24*30)+ 0 *(60*60*24)+ 0 *(60*60)+ 0 *60+ 0; //月日時分秒
  ini_set('session.gc_maxlifetime', $TOKEN_EXPIRATION_TIME);
  ini_set('session.cookie_lifetime', $TOKEN_EXPIRATION_TIME);
  session_start();

  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';

  function checkSessionToken() {
    global $conn;
    if (isset($_SESSION['token']) && isset($_SESSION['member_id'])) {
      $expiration_time = null;
      $token = $_SESSION['token'];
      $member_id = $_SESSION['member_id'];
      $stmt = bindPrepare($conn, 
        "SELECT expiration_time FROM tokens 
        WHERE member_id = ? AND token = ?
      ", "is", $member_id, $token);
      $stmt->execute();
      $stmt->bind_result($expiration_time);
      $success = $stmt->fetch();      
      $stmt->close();
      if ($success) {
        date_default_timezone_set("Asia/Taipei");
        $current_time = new DateTime("now");
        $token_expiry = DateTime::createFromFormat('Y-m-d H:i:s', $expiration_time);
        if ($current_time < $token_expiry) {
          return json_encode(['success' => true, 'message' => '驗證成功，已自動登入', 'member_id' => $member_id]);
        } else {
          return json_encode(['success' => false, 'showMessage' => '連線已過期，請重新登入']);
        }
      } else {
        return json_encode(['success' => false, 'message' => '您的連線資訊無效，請重新登入']);
      }
    } else {
      return json_encode(['success' => false, 'message' => '尚未登入帳號']);
    }
  }

  $SESSION_DATA = json_decode(checkSessionToken());
