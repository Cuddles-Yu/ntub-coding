function toMemberPage() {
  window.location.href = 'member/info';
}

function toHomePage() {
  window.location.href = 'home';
}

function clearInputs(...inputs) {
  inputs.forEach(input => input.value = '');
}

function loginRequest() {
  const emailInput = document.getElementById('login-email')      
  const passwordInput = document.getElementById('login-password')
  const email = emailInput.value;   
  const password = passwordInput.value;
  if (!email || !password) {
    !email ? emailInput.focus() : passwordInput.focus();
    return;
  }      
  clearInputs(emailInput, passwordInput);
  emailInput.focus();

  const formData = new FormData();
  formData.set('email', email);     
  formData.set('password', password); 
  fetch('./member/handler/login.php', {
    method: 'POST',
    credentials: 'same-origin',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        document.getElementById('loginError').innerText = `會員編號登入成功`;
        document.getElementById('login-message').style.display = 'none';
        document.getElementById('login-cancel-button').click();
        document.getElementById('user_icon').style.display = 'flex';
        document.getElementById('login_button').style.display = 'none';
      } else {
        if ('showMessage' in data) {
          alert(data.showMessage);
        } else {
          document.getElementById('loginError').innerText = data.message;
          document.getElementById('login-message').style.display = 'block';
        }
      }
    })
    .catch(error => {
      console.error('Error:', error);
      document.getElementById('loginError').innerText = `發生非預期的錯誤，請稍後再試。`;
      document.getElementById('login-message').style.display = 'block';
    });
}

document.addEventListener('DOMContentLoaded', function() {
  const inputs = document.querySelectorAll('.login-input');
  const submitButton = document.getElementById('login-submit-button');
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


/* 點擊漢堡圖示時，顯示/隱藏選單 */
document.getElementById('hamburger_btn').addEventListener('click', function() {
  var navMenu = document.getElementById('nav_menu2');
  var overlay = document.getElementById('overlay');
  if (navMenu.classList.contains('show')) {
      navMenu.classList.remove('show');
      overlay.classList.remove('show');
  } else {
      navMenu.classList.add('show');
      overlay.classList.add('show');
  }
});  
document.getElementById('overlay').addEventListener('click', function() {
  var navMenu = document.getElementById('nav_menu2');
  var overlay = document.getElementById('overlay');
  navMenu.classList.remove('show');
  overlay.classList.remove('show');
});
document.querySelectorAll('.close-menu').forEach(tab => {
  tab.addEventListener('click', function() {
    var navMenu = document.getElementById('nav_menu2');
    var overlay = document.getElementById('overlay');
    navMenu.classList.remove('show');
    overlay.classList.remove('show');
  });
});

/* 判斷是否為白天或晚上 */
const currentHour = new Date().getHours();
const iconContainer = document.getElementById('web_name');
if (currentHour >= 6 && currentHour < 18) {
    iconContainer.innerHTML = '<img src="images/logo-yellow.png" id="web_logo"> <a href="../home">評星宇宙</a>';
} else {
    iconContainer.innerHTML = '<img src="images/logo-blue+.png" id="web_logo"> <a href="../home">評星宇宙</a>';
}