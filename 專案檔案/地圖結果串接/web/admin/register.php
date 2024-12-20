<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn;
  $stmt = $conn->prepare("
    SELECT auth_key FROM administrators
  ");
  $stmt->execute();
  $results = $stmt->get_result();
  $authKeys = [];
  while ($row = $results->fetch_assoc()) {
      $authKeys[] = $row['auth_key'];
  }
  $providedAuthKey = $_GET['auth']??'';
  if (!$providedAuthKey && !in_array(needle:$providedAuthKey, haystack:$authKeys)) {
    echo '您沒有權限訪問此頁面';
    exit;
  }
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <title>管理員註冊 - 評星宇宙</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="/styles/elem/form.css?v=<?=$VERSION?>">
</head>

<body class="form-body">
  <div class='form-container' id="container" auth="<?=$providedAuthKey?>">
    <h2 class="form-h2">管理者註冊</h2>
    <form novalidate>
      <div>
          <input type='text' id='name' class='form-input' placeholder='名稱'>
          <span id='nameError' class='form-error-message'>名稱欄位不能為空</span>
      </div>
      <div>
          <input type='password' id='password' class='form-input' placeholder='密碼'>
          <span id='passwordError' class='form-error-message'>密碼欄位不能為空</span>
      </div>
    </form>
    <br>
    <button type='button' class="form-button" id='form-submit-button' onClick='registerRequest()'>註冊</button>
    <div id="authkey" class="form-error-message" style="display:none"></div>
  </div>
  <div class='form-message' id="message" style="display:none">
    <div id="loginError" class="form-error-message" style="display:block"></div>
    <button type='button' class="form-button" id='copy-button' style="display:none; margin-top: 10px" onClick='copyAuthKey()'>複製金鑰</button>
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
    function clearInputs(...inputs) {
      inputs.forEach(input => input.value = '');
    }
    function copyAuthKey() {
      const authKey = document.getElementById('container').getAttribute('auth');
      document.getElementById('loginError').innerText = '已成功複製金鑰：'+authKey;
      document.getElementById('loginError').style.color = 'green';
      navigator.clipboard.writeText(authKey)
    }
    function registerRequest() {
      const nameInput = document.getElementById('name')
      const passwordInput = document.getElementById('password')
      const name = nameInput.value;
      const password = passwordInput.value;
      document.getElementById('nameError').style.display = !name ? 'block' : 'none';
      document.getElementById('passwordError').style.display = !password ? 'block' : 'none';
      if (!name || !password) {
        !name ? nameInput.focus() : passwordInput.focus();
        return;
      }
      clearInputs(nameInput, passwordInput);
      nameInput.focus();

      const formData = new FormData();
      formData.set('name', name);
      formData.set('password', password);
      document.getElementById('message').style.display = 'block';
      fetch('./handler/register.php', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('container').setAttribute('auth', data.token);
            document.getElementById('copy-button').style.display = 'block';
            document.getElementById('loginError').innerText = `管理員 '${name}' 註冊成功`;
            document.getElementById('loginError').style.color = 'darkgreen';
          } else {
            document.getElementById('copy-button').style.display = 'none';
            document.getElementById('loginError').innerText = data.message;
            document.getElementById('loginError').style.color = 'red';
          }
        })
        .catch(() => {
          document.getElementById('copy-button').style.display = 'none';
          document.getElementById('loginError').innerText = '發生非預期的錯誤，請稍後再試';
          document.getElementById('loginError').style.color = 'red';
        });
    }
  </script>

</body>
</html>
