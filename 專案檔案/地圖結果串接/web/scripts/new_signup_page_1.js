// 顯示, 隱藏密碼
document.getElementById('hide_password_icon1').addEventListener('click', function() {
    var passwordInput = document.getElementById('password_input1');
    var passwordIcon = document.getElementById('hide_password_icon1');

    if (passwordInput.type === 'password') {
        // 如果當前是隱藏密碼，則顯示密碼並更新圖片
        passwordInput.type = 'text';
        passwordIcon.src = 'images/show_pwd.png';
    } else {
        // 如果當前是顯示密碼，則隱藏密碼並更新圖片
        passwordInput.type = 'password';
        passwordIcon.src = 'images/hide_pwd.png';
    }
});

// 顯示, 隱藏密碼
document.getElementById('hide_password_icon2').addEventListener('click', function() {
    var passwordInput = document.getElementById('password_input2');
    var passwordIcon = document.getElementById('hide_password_icon2');

    if (passwordInput.type === 'password') {
        // 如果當前是隱藏密碼，則顯示密碼並更新圖片
        passwordInput.type = 'text';
        passwordIcon.src = 'images/show_pwd.png';
    } else {
        // 如果當前是顯示密碼，則隱藏密碼並更新圖片
        passwordInput.type = 'password';
        passwordIcon.src = 'images/hide_pwd.png';
    }
});

// 檢查密碼和確認密碼是否相同、已同意服務條款
function validateForm() {
    var pwd = document.getElementById("password_input1").value;
    var confirmPwd = document.getElementById("password_input2").value;
    var serviceCheckbox = document.getElementById("service");

    if (pwd !== confirmPwd) {
        alert("密碼和確認密碼不一致，請重新輸入。");
        return false; // 阻止表單提交
    }

    if (!serviceCheckbox.checked) {
        alert("請同意服務條款。");
        return false; // 阻止表單提交
    }

    return true; // 允許表單提交
}

function validateEmail() {
    var emailInput = document.getElementById("email_input").value;  // 獲取信箱的值
    var emailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;  // 檢查 Gmail 格式的正則表達式

    if (emailRegex.test(emailInput)) {
        return true;  // 信箱格式正確
    } else {
        alert("請輸入有效的 Gmail 信箱！");
        return false;  // 信箱格式不正確
    }
}

// 在表單提交之前進行驗證
document.querySelector('form').addEventListener('submit', function(event) {
    if (!validateEmail()) {
        event.preventDefault();  // 阻止表單提交
    }
});

$(document).ready(function() {
    // 當信箱輸入框失去焦點時觸發
    $('#email_input').on('blur', function() {
        var mail = $(this).val(); // 獲取使用者輸入的信箱
        
        // 使用 AJAX 向伺服器發送請求
        $.ajax({
            url: 'check_email.php', // 發送到後端 PHP 的檔案
            type: 'POST',
            data: { mem_mail: mail }, // 傳送信箱資料
            success: function(response) {
                if (response == '1') {
                    // 信箱已存在
                    $('#email_error').text('該信箱已經註冊，請使用其他信箱。');
                    $('#sign_up_button').prop('disabled', true); // 禁用註冊按鈕
                } else {
                    // 信箱不存在，可以使用
                    $('#email_error').text('該信箱可以使用。');
                    $('#sign_up_button').prop('disabled', false); // 啟用註冊按鈕
                }
            }
        });
    });
});