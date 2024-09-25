<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員註冊頁面 Part I</title>
    <link rel="stylesheet" href="styles/new_signup_page_1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
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
    }
    ?>
    <?php 
    if( isset($_GET['msg']) && $_GET['msg'] == 1 ){ 
        echo '
        <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 start-50 translate-middle-x" role="alert">
            該信箱已註冊，請使用其他信箱
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
    ?> 

    <div class="login-container">
        <div class="left-side">
            <img src="images/Logo設計_文字(藍+).png" alt="評星宇宙logo" id="container_logo">
        </div>

        <div class="right-side">
            <form class="login-form mem-addmem-area" action="signup-page1.php" method="post" onsubmit="return validateForm()">
                <div class="title">註冊新帳號</div>
                <div class="input-group">
                    <img src="images/user.png" alt="user" id="user_icon">
                    <input type="text" id="user_input" name="mem_name" placeholder="使用者名稱" required>
                </div>
                <div class="input-group">
                    <img src="images/mail2.png" alt="mail" id="mail_icon">
                    <input class="mem_mail" type="email" id="email_input" name="mem_mail" placeholder="電子郵件地址" required>
                </div>
                <div class="input-group">
                    <img src="images/key.png" alt="password" id="password_icon1">
                    <input class="mem_pwd" type="password" id="password_input1" name="mem_pwd" placeholder="密碼" required>
                    <img src="images/hide_pwd.png" alt="password" id="hide_password_icon1">
                </div>
                <div class="input-group">
                    <img src="images/key.png" alt="password" id="password_icon2">
                    <input class="confirm_pwd" type="password" id="password_input2" name="confirm_pwd" placeholder="確認密碼" required>
                    <img src="images/hide_pwd.png" alt="password" id="hide_password_icon2">
                </div>

                <div class="service_container">
                    <div class="service_group">
                        <input type="checkbox" id="service" name="service">
                        <label for="service" id="service_text">我已詳細閱讀並同意服務條款</label>
                    </div>
                    <button id="sign_up_button" type="submit">
                        <span>註冊</span>
                    </button>
                    <input type="hidden" name="form-name" value="addmem-form">
                </div>
            </form>
        </div>
        
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="scripts/new_signup_page_1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>