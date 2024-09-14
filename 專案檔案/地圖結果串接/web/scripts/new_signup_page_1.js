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
    var password = document.getElementById("password_input1").value;
    var confirmPassword = document.getElementById("password_input2").value;
    var serviceCheckbox = document.getElementById("service");

    if (password !== confirmPassword) {
        alert("密碼和確認密碼不一致，請重新輸入。");
        return false; // 阻止表單提交
    }

    if (!serviceCheckbox.checked) {
        alert("請同意服務條款。");
        return false; // 阻止表單提交
    }

    return true; // 允許表單提交
}