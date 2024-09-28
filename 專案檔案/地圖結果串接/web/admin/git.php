<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Git同步 - 評星宇宙</title>
</head>
<body>
  <?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/db.php';
    global $conn;
    
    function colored_echo($color, $title, $text) {
        echo "<div style='color:$color; font-weight: bold; margin-bottom: 10px;'>[$title] $text</div>";
    }  

    function get_admin_by_auth_key($authKey, $conn) {
        $stmt = $conn->prepare(
          " SELECT name FROM administrators 
            WHERE auth_key = '$authKey'
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc();
    }

    function loginFailed() {
      if (!isset($_POST['name'])) {
        echo "
        <h2>管理者登入</h2>
        <form method='POST'>
            <label for='name'>名稱：</label>
            <input type='text' id='name' name='name' required><br><br>
            <label for='password'>密碼：</label>
            <input type='password' id='password' name='password' required><br><br>
            <button type='submit'>登入</button>
        </form>";
      }      
    }

    function loginSuccess($conn, $authKey){            
      $pythonPath = escapeshellcmd('C:/Users/北商學生四人小組-20240517/AppData/Local/Programs/Python/Python312/python');
      $pythonFile = './sys/git_pull.py';
      $logFile = './sys/git_history.html';
      $admin = get_admin_by_auth_key($authKey, $conn);
      if ($admin) {
          $adminName = $admin['name']; // 使用者名稱來標識管理者
        if (isset($_POST['pull'])) {
            $command = escapeshellcmd($pythonPath.' '.$pythonFile.' 2>&1');
            $output = shell_exec($command);
            date_default_timezone_set("Asia/Taipei");
            $current_time = date('Y-m-d H:i:s');
            if ($output !== null) {
                $gitLog = shell_exec('git log -1 --pretty=format:"%h|%s|%b|%ci|%an" 2>&1');
                list($commitHash, $title, $body, $date, $author) = explode('|', $gitLog);
                $dateObject = DateTime::createFromFormat('Y-m-d H:i:s O', trim($date));
                $formattedDate = $dateObject->format('Y-m-d H:i:s');
                $output = preg_replace('/\n\s*\n/', "\n", trim($output));
                $logEntry = "
                <div class='log-entry'>
                    <h3>Git Pull 資訊</h3>                    
                    <p><strong>輸出</strong></p>
                    <pre class='git-log'>$output</pre>        
                    <p><strong>同步時間：</strong> $current_time</p>
                    <p><strong>管理者：</strong> $adminName</p>                     
                    <h3>Commit 資訊</h3>
                    <p><strong>標題</strong></p>
                    <pre class='git-log'><em>($commitHash) $title</em></pre>
                    <p><strong>說明</strong></p>
                    <pre class='git-log'><em>$body</em></pre>            
                    <p><strong>提交時間：</strong> $formattedDate</p>
                    <p><strong>開發者：</strong> $author</p>                  
                </div>";
                $existingContent = file_get_contents($logFile);
                file_put_contents($logFile, $logEntry . $existingContent);
            }
            header("Location: ".$_SERVER['REQUEST_URI']);
            exit;
        }
        echo "
        <span class='success'><strong>已驗證的授權</strong> -> $adminName</span>
        <form method='POST' style='display: inline; margin-left: 10px;'>
            <input type='hidden' name='auth' value='$authKey'>
            <button class='btn-modern' type='submit' name='pull'>Git Pull</button>
        </form>";
        echo "<h2>Git 同步紀錄</h2>";
        echo "<div id='git-history'>";
        $logContent = file_get_contents($logFile);
        echo $logContent;
        echo "</div>";
      } else {
          $authKey = null;
          colored_echo('red', 'INVALID', '錯誤的授權金鑰，無法執行 Git Pull 程式');
      }
    }

    header('Content-Type: text/html; charset=utf-8');
    echo '<link rel="stylesheet" type="text/css" href="./sys/git_history.css">';

    if (isset($_GET['auth'])) {
        $authKey = $_GET['auth'];
    } else {
        $authKey = null;
    }  

    if ($authKey === null) {      
      loginFailed();
      if (isset($_POST['name']) && isset($_POST['password'])) {
          $name = $_POST['name'];
          $password = $_POST['password'];
          ### 驗證帳號密碼 ###
          $stmt = $conn->prepare("SELECT password, auth_key FROM administrators WHERE name = '$name'");
          $stmt->execute();
          $stmt->bind_result($hashedPassword, $retrievedAuthKey);
          $stmt->fetch();
          $stmt->close();
          if (password_verify($password, $hashedPassword)) {
              loginSuccess($conn, $retrievedAuthKey);
          } else {
              colored_echo('red', 'INVALID', '帳號或密碼不正確');
              loginFailed();
          }
      }
    } else {
        loginSuccess($conn, $authKey);
    }
  ?>
</body>
</html>