<?
require_once('./shared/conn_pdo.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $mem_mail = $_POST['mem_mail'];

        // 查詢資料庫，看信箱是否已經存在
        $sql_str = "SELECT * FROM member WHERE mem_mail = :mem_mail";
        $RS = $conn->prepare($sql_str);
        $RS->bindParam(':mem_mail', $mem_mail);
        $RS->execute();
        
        $total = $RS->rowCount();  // 計算結果數量，來判斷信箱是否已經存在
        
        if ($total >= 1) {
            // 如果信箱已經存在，則提示使用者
            header('Location: signup-page1.php?msg=1');  // 重定向到註冊頁面並加上提示參數
            exit();
        } else {
            // 信箱不存在，進行註冊流程
            $mem_name = $_POST['mem_name'];
            $mem_pwd = password_hash($_POST['mem_pwd'], PASSWORD_DEFAULT); // 使用 hash 加密密碼

            // 插入新會員資料
            $sql_insert = "INSERT INTO member (mem_mail, mem_name, mem_pwd) VALUES (:mem_mail, :mem_name, :mem_pwd)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bindParam(':mem_mail', $mem_mail);
            $stmt_insert->bindParam(':mem_name', $mem_name);
            $stmt_insert->bindParam(':mem_pwd', $mem_pwd);
            $stmt_insert->execute();
            
            // 註冊成功後跳轉到歡迎頁面或登入頁面
            header('Location: signup-page2.php');
            exit();
        }
    }?>