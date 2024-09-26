<?php
    header('Content-Type: text/html; charset=utf-8');

    function colored_echo($color, $title, $text) {
        echo "<div style='color:$color; font-weight: bold; margin-bottom: 10px;'>[$title] $text</div>";
    }

    $authKey = isset($_GET['auth']) ? $_GET['auth'] : null;
    $administrators = [
        'mnkrZSaAQPhmqfASJzdg6dizCarbLsJGpTnWb7bp' => '余奕博',
        'cmZC675Bs2ATyxH69yT2bZDzPD3JVAuSbqPkMpBQ' => '邱綺琳',
        'pFhqky4Kjkw93buGe2uhKVUZH9Hs87QUXNr9zgMP' => '陳彥瑾',
        'Rw7MtGsLfBgum9pFw9GBQi2ACNqiJLx6SbQ2vuzh' => '鄧惠中',
    ];

    $pythonFile = './sys/git_pull.py';
    $logFile = './sys/git_history.html';
    $cssFile = './sys/git_history.css';
    $pythonPath = escapeshellcmd('C:/Users/北商學生四人小組-20240517/AppData/Local/Programs/Python/Python312/python');

    echo '<link rel="stylesheet" type="text/css" href="'.$cssFile.'">';

    if (array_key_exists($authKey, $administrators)) {
        $adminName = $administrators[$authKey];
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
        colored_echo('red', 'INVALID', '錯誤的授權金鑰，無法執行 Git Pull 程式');
    }
