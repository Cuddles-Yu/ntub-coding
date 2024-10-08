<?php if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) { header('Location: /home'); exit;} ?>

<?php
  $TOKEN_EXPIRATION_TIME = 0 *(60*60*24*30)+ 1 *(60*60*24)+ 0 *(60*60)+ 0 *60+ 0; //月日時分秒  
  $SESSION_EXPIRATION_TIME = 6 *(60*60*24*30)+ 0 *(60*60*24)+ 0 *(60*60)+ 0 *60+ 0; //月日時分秒
  ini_set('session.gc_maxlifetime', $SESSION_EXPIRATION_TIME);  
  ini_set('session.cookie_lifetime', $SESSION_EXPIRATION_TIME);
  session_start();

  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';

  function resetSession() {
    session_unset();
    session_destroy();    
  }

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
          if (isset($_COOKIE['remember'])) {            
            return json_encode(['success' => true, 'message' => '驗證成功，已自動登入', 'member_id' => $member_id, 'expired' => false]);
          } else {        
            resetSession();
            return json_encode(['success' => false, 'showMessage' => '保持登入狀態已關閉，請重新登入以驗證身份', 'expired' => true]);
          }
        } else {
          resetSession();            
          return json_encode(['success' => false, 'showMessage' => '連線階段已過期，請重新登入以驗證身份', 'expired' => true]);
        }
      } else {
        resetSession();
        return json_encode(['success' => false, 'showMessage' => '連線金鑰驗證失敗，請重新登入以驗證身份', 'expired' => true]);
      }      
    } else {
      return json_encode(['success' => false, 'message' => '尚未登入帳號', 'expired' => false]);
    }
  }

  $SESSION_DATA = json_decode(checkSessionToken());
  $MEMBER_ID = $SESSION_DATA->success ? $SESSION_DATA->member_id : null;
  if (!$SESSION_DATA->success&&$SESSION_DATA->expired) {
    echo "
      <script>
        localStorage.setItem('loginExpired', 'true');
        localStorage.setItem('showMessage', '".$SESSION_DATA->showMessage."');
      </script>
    ";
  }

?>
