function clearInputs(...inputs) {
  inputs.forEach(input => input.value = '');
}

function loginRequest() {
  document.getElementById('form-cancel-button').click();
  document.getElementById('user_icon').style.display = 'flex';
  document.getElementById('login_button').style.display = 'none';
  // const emailInput = document.getElementById('email')      
  // const passwordInput = document.getElementById('password')
  // const email = emailInput.value;   
  // const password = passwordInput.value;
  // // document.getElementById('nameError').style.display = !email ? 'block' : 'none';      
  // // document.getElementById('passwordError').style.display = !password ? 'block' : 'none';
  // if (!email || !password) {
  //   !email ? emailInput.focus() : passwordInput.focus();
  //   return;
  // }      
  // clearInputs(emailInput, passwordInput);
  // emailInput.focus();

  // const formData = new FormData();
  // formData.set('email', email);     
  // formData.set('password', password); 
  // fetch('member/handler/login.php', {
  //   method: 'POST',
  //   body: formData
  // })
  //   .then(response => response.json())
  //   .then(data => {
  //     if (data.success) {
  //       document.getElementById('loginError').innerText = `會員編號 '${data.id}' 登入成功`;
  //       document.getElementById('message').style.display = 'block';
  //     } else {
  //       document.getElementById('loginError').innerText = data.message;
  //       document.getElementById('message').style.display = 'block';
  //     }
  //   })
  //   .catch(error => {
  //     console.error('Error:', error);
  //     document.getElementById('loginError').innerText = `發生非預期的錯誤，請稍後再試。`;
  //     document.getElementById('message').style.display = 'block';
  //   });
}

document.addEventListener('DOMContentLoaded', function() {
  const inputs = document.querySelectorAll('.form-input-popup');
  const submitButton = document.getElementById('form-submit-button');
  inputs.forEach((input, index) => {
      input.addEventListener('keydown', function(event) {
          if (event.key === 'Enter') {
            event.preventDefault();
            if (index < inputs.length - 1) {
              setTimeout(() => {
                  inputs[index + 1].focus();
              }, 0);
            } else {
              submitButton.click();
            }
          }
      });
  });
  inputs[0].focus();
});