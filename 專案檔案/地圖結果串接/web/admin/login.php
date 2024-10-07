<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者登入 - 評星宇宙</title>
    <link rel="stylesheet" href="/styles/form.css">
</head>
<body class="form-body">
  <?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
    global $conn;
    $redirectUrl = $_POST['redirect_url'] ?? null;
    if (empty($redirectUrl)) header('Location: /home');
  ?>
  <div class='form-container'>
    <h2 class="form-h2">管理者登入</h2>
    <form novalidate>
      <div>
          <input type='text' id='name' class='form-input' placeholder='名稱' required>
          <span id='nameError' class='form-error-message'>名稱欄位不能為空</span>
      </div>    
      <div>
          <input type='password' id='password' class='form-input' placeholder='密碼' required>
          <span id='passwordError' class='form-error-message'>密碼欄位不能為空</span>
      </div>
    </form>    
    <br>
    <button type='button' class="form-button" id='form-submit-button' onClick='loginRequest()'>登入</button>
    <br>
    <div id="loginError" class="form-error-message" style="display:none; color:red; padding-top: 10px"></div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.form-input');
        const submitButton = document.getElementById('form-submit-button');
        inputs.forEach((input, index) => {
            input.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    if (index < inputs.length - 1) {
                      inputs[index + 1].focus();
                    } else {
                      submitButton.click();
                    }
                }
            });
        });
        inputs[0].focus();
    });

    function loginRequest() {
      const formData = new FormData();
      formData.set('name', document.getElementById('name').value);     
      formData.set('password', document.getElementById('password').value); 
      // 獲取 HTML 搜索結果
      fetch('./handler/check.php', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // 如果登入成功，跳轉到登入後的功能頁面
            window.location.href = './git.php?auth=' + data.token;
          } else {
            // 如果登入失敗，顯示錯誤訊息
            document.getElementById('loginError').innerText = data.message;
            document.getElementById('loginError').style.display = 'block';
          }
        })
        .catch(error => {
          console.error('Error:', error);
          document.getElementById('loginError').innerText = '伺服器發生錯誤，請稍後再試。';
          document.getElementById('loginError').style.display = 'block';
        });
    }
  </script>

</body>
</html>