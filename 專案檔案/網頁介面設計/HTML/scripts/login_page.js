// 顯示/隱藏密碼
function togglePasswordVisibility() {
    var passwordInput = document.getElementById('password');
    var togglePasswordButton = document.getElementById('togglePassword');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        togglePasswordButton.innerText = '隱藏密碼';
    }
    else {
        passwordInput.type = 'password';
        togglePasswordButton.innerText = '顯示密碼';
    }
}