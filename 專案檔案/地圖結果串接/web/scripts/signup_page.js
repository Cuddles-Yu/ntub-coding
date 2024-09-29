// 顯示/隱藏密碼
document.getElementById('hide_password_icon1').addEventListener('click', function() {
    var passwordInput = document.getElementById('password_input1');
    var passwordIcon = document.getElementById('hide_password_icon1');

    if (passwordInput.type === 'password') {
        // 如果當前是隱藏密碼，則顯示密碼並更新圖片
        passwordInput.type = 'text';
        passwordIcon.src = 'images/password-show.png';
    } else {
        // 如果當前是顯示密碼，則隱藏密碼並更新圖片
        passwordInput.type = 'password';
        passwordIcon.src = 'images/password-hide.png';
    }
});

// 顯示/隱藏密碼
document.getElementById('hide_password_icon2').addEventListener('click', function() {
    var passwordInput = document.getElementById('password_input2');
    var passwordIcon = document.getElementById('hide_password_icon2');

    if (passwordInput.type === 'password') {
        // 如果當前是隱藏密碼，則顯示密碼並更新圖片
        passwordInput.type = 'text';
        passwordIcon.src = 'images/password-show.png';
    } else {
        // 如果當前是顯示密碼，則隱藏密碼並更新圖片
        passwordInput.type = 'password';
        passwordIcon.src = 'images/password-hide.png';
    }
});