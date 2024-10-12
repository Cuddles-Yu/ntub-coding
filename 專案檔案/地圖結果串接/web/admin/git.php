<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn;

  if (isset($_GET['auth'])) {
    $authKey = $_GET['auth'];
  } else {
    $currentUrl = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    echo "
    <form id='redirectForm' method='POST' action='./login.php' style='display:none;'>
        <input type='hidden' name='redirect_url' value='".htmlspecialchars($currentUrl)."'>
    </form>
    <script>
        document.getElementById('redirectForm').submit();
    </script>";
    exit;
  }

  function get_admin_by_auth_key($authKey, $conn) {
    $adminName = '';
    $stmt = bindPrepare($conn,
    " SELECT name FROM administrators 
      WHERE auth_key = ?
    ", "s", $authKey);
    $stmt->execute();
    $stmt->bind_result($adminName);
    $stmt->fetch();
    $stmt->close();
    return $adminName;
  }
    
  $pythonPath = escapeshellcmd('C:/Users/北商學生四人小組-20240517/AppData/Local/Programs/Python/Python312/python');
  $pythonFile = './sys/git_pull.py';
  $logFile = './sys/git_history.html';

  $adminName = get_admin_by_auth_key($authKey, $conn);
  if (!$adminName) {
    coloredEcho('red', 'INVALID', '錯誤的授權金鑰，無法執行 Git Pull 程式');
    exit;
  }
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
            <p><strong>同步時間：</strong>$current_time</p>
            <p><strong>管理者：</strong>$adminName</p>
            <h3>Commit 資訊</h3>
            <p><strong>標題</strong></p>
            <pre class='git-log'><em>($commitHash) $title</em></pre>
            <p><strong>說明</strong></p>
            <pre class='git-log'><em>$body</em></pre>
            <p><strong>提交時間：</strong>$formattedDate</p>
            <p><strong>開發者：</strong>$author</p>
        </div>";
        $existingContent = file_get_contents($logFile);
        file_put_contents($logFile, $logEntry . $existingContent);
    }
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit;
  }
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <title>管理者系統 - 評星宇宙</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="/styles/elem/form.css">
  <link rel="stylesheet" href="/sys/git_history.css">
</head>

<body>
  <?php if ($adminName): ?>
    <span class='success'><strong>已驗證的授權</strong> -> <?=$adminName?></span>
    <form method='POST' style='display: inline; margin-left: 10px;'>
        <input type='hidden' name='auth' value='$authKey'>
        <button class='btn-modern' type='submit' name='pull'>Git Pull</button>
    </form>
    <h2>Git 同步紀錄</h2>
    <div id='git-history'>
      <?=file_get_contents($logFile);?>
    </div>
  <?php endif; ?>

</body>
</html>