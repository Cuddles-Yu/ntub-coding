let alertTimeout;
function showAlert(type, message) {
  const ALERT_INTERVAL = 5000;
  const alertBox = document.getElementById('alert-box');
  clearTimeout(alertTimeout);
  alertBox.classList.remove('alert-show');
  alertBox.classList.add('alert-hide');
  setTimeout(function() {
      alertBox.className = 'alert';      
      if (type === 'green' || type === 'success') {
        alertBox.classList.add('alert-success');
      } else if (type === 'orange' || type === 'warning') {
        alertBox.classList.add('alert-warning');
      } else if (type === 'red' || type === 'danger') {
        alertBox.classList.add('alert-danger');
      }      
      alertBox.textContent = message;
      alertBox.classList.remove('alert-hide');
      alertBox.classList.add('alert-show');
      alertTimeout = setTimeout(function() {
        alertBox.classList.remove('alert-show');
        alertBox.classList.add('alert-hide');
      }, ALERT_INTERVAL);
  }, 200);
}

function clearInputs(...inputs) {
  inputs.forEach(input => input.value = '');
}

function updateLabelValue(id) {
  var slider = document.getElementById(id);
  var output = document.getElementById(id + '-value');
  output.textContent = slider.value;
}

function closeOpenedModal() {
  const modals = document.querySelectorAll('.modal');
  modals.forEach(modalElement => {
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) modalInstance.hide();
  });
  const backdrops = document.querySelectorAll('.modal-backdrop');
  backdrops.forEach(function (backdrop) {
      backdrop.remove();
  });
}

/* 自動讓登入表單的第一個輸入框取得焦點 */
document.getElementById('loginModal').addEventListener('shown.bs.modal', function () {
  document.getElementById('login-email').focus()
})
document.getElementById('signupModal1').addEventListener('shown.bs.modal', function () {
  document.getElementById('signup-email').focus()
})
document.getElementById('logoutModal').addEventListener('shown.bs.modal', function () {
  document.getElementById('logout-confirm-button').focus()
})

function cancelSignup(targetId) {
  closeOpenedModal();
  let modal = new bootstrap.Modal(document.getElementById('cancelSignupModal'));
  document.getElementById('cancel-signup-cancel-button').setAttribute('onclick', `restoreSignup('${targetId}')`);
  modal.show();
}
function restoreSignup(targetId) {
  closeOpenedModal();
  let modal = new bootstrap.Modal(document.getElementById(targetId));
  modal.show();
}

function cancelModal() {
  closeOpenedModal();
  clearInputs(
    document.getElementById('login-email'), 
    document.getElementById('login-password')
  )
  document.getElementById("remember").checked = false;
  document.getElementById('login-message').style.display = 'none';
  clearInputs(
    document.getElementById('signup-email'), 
    document.getElementById('signup-name'), 
    document.getElementById('signup-password'), 
    document.getElementById('signup-check-password')
  );
  document.getElementById('signup-consent').checked = false;
  document.getElementById('signup-message').style.display = 'none';
}

/* 自動在電子郵件欄位失去焦點時，驗證 */
// const emailInput = document.getElementById('signup-email');
// emailInput.addEventListener('blur', function() {
//   if (emailInput.value.trim()!=="") emailVerifyRequest();
// });

/* 切換密碼顯示/隱藏 */
function togglePasswordVisibility(inputId, iconId, autoFocus = true) {
  var passwordInput = document.getElementById(inputId);
  var passwordIcon = document.getElementById(iconId);
  if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      passwordIcon.setAttribute('src', 'images/password-show.png');
  } else {
      passwordInput.type = 'password';
      passwordIcon.setAttribute('src', 'images/password-hide.png');
  }
  if (autoFocus) passwordInput.focus();
}
document.getElementById('login-toggle-password').addEventListener('click', function() {
  togglePasswordVisibility('login-password', 'login-toggle-password');
});
document.getElementById('signup-toggle-password').addEventListener('click', function() {    
  togglePasswordVisibility('signup-password', 'signup-toggle-password');
  togglePasswordVisibility('signup-check-password', 'signup-toggle-check-password', false);
});
document.getElementById('signup-toggle-check-password').addEventListener('click', function() {    
  togglePasswordVisibility('signup-check-password', 'signup-toggle-check-password');  
  togglePasswordVisibility('signup-password', 'signup-toggle-password', false);  
});


function bindFormControl(inputClass, buttonId) {
  const inputs = document.querySelectorAll(inputClass);
  const submitButton = document.getElementById(buttonId);
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
}

document.addEventListener('DOMContentLoaded', function() {
  bindFormControl('.login-input', 'login-submit-button');
  bindFormControl('.signup-input', 'signup1-next-button');
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
    iconContainer.innerHTML = '<img src="/images/logo-yellow.png" id="web_logo"> <a href="../home">評星宇宙</a>';
} else {
    iconContainer.innerHTML = '<img src="/images/logo-blue+.png" id="web_logo"> <a href="../home">評星宇宙</a>';
}

/* 點擊使用者圖示時，顯示/隱藏會員下拉選單 */
const memberIcon = document.getElementById('user_icon');
const dropdownMenu = document.getElementById('dropdownMenu');
memberIcon.addEventListener('click', function() {
    dropdownMenu.style.display = dropdownMenu.style.display === 'none' || dropdownMenu.style.display === '' ? 'block' : 'none';
});
document.addEventListener('click', function(event) {
    if (!memberIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.style.display = 'none';
    }
});