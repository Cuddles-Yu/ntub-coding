<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員登入頁面</title>
    <link rel="stylesheet" href="./styles/new_login_page.css"> <!-- 載入 login_page.css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php 
    if( isset($_GET['msg']) && $_GET['msg'] == 1 ){ 
        echo '
        <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 start-50 translate-middle-x" role="alert">
            輸入的帳號或密碼有誤，請重新登入！
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
    ?> 

    <div class="login-container">
        <div class="left-side">
            <img src="images/logo-text-blue+.png" alt="評星宇宙logo" id="container_logo">
        </div>

        <div class="right-side">
            <form class="login-form" method="post" action="./member/login_check.php">
                <div class="title">登入您的帳號</div>
                
                <div class="input-group">
                    <img src="images/icon-email.png" alt="mail" id="mail_icon">
                    <input type="email" id="email" name="mem_mail" placeholder="電子郵件地址" required>
                </div>

                <div class="forget_pwd">忘記密碼？</div>

                <div class="input-group">
                    <img src="images/icon-key.png" alt="password" id="password_icon">
                    <input type="password" id="password" name="mem_pwd" placeholder="密碼" required>
                    <img src="images/password-hide.png" alt="password" id="toggle-password">
                </div>

                <div class="remember_container">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember" id="remember_text">記住我</label>
                    <button id="login-button" type="submit" value="">
                        <span>登入</span>
                    </button>
                </div>
            </form>  
            <div class="divider">
                <span>or</span>
            </div>

            <div class="login_area">
                <button class="google_button">
                    <img src="images/icon-google.png" alt="google_icon" id="google_icon">
                    <span>Google登入</span>
                </button>
                <a type="button" href='./signup-page1.php'>
                    <button class="sign_up_button" >註冊新帳號</button>                    
                </a>
            </div>       

            <div class="agree">ⓘ 登入即表示您同意我們的服務條款與隱私政策</div>
               
                         
        </div>
        
    </div>

    <script src="scripts/new_login_page.js"></script> <!-- 載入 login_page.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>