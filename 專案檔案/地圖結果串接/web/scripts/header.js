let alertTimeout;
function showAlert(type, message, timeout = 5000) {
  const alertBox = document.getElementById('alert-box');
  clearTimeout(alertTimeout);
  alertBox.classList.remove('alert-show');
  alertBox.classList.add('alert-hide');
  setTimeout(function() {
    alertBox.className = 'alert';
    alertBox.classList.add(`alert-${type}`);
    alertBox.textContent = message;
    alertBox.classList.remove('alert-hide');
    alertBox.classList.add('alert-show');
    alertTimeout = setTimeout(function() {
      alertBox.classList.remove('alert-show');
      alertBox.classList.add('alert-hide');
    }, timeout);
  }, 50);
}

document.addEventListener('DOMContentLoaded', function () {

  // 如果為登入登出跳轉，則顯示提示訊息
  const justLoggedIn = localStorage.getItem('justLoggedIn')??'';
  const memberName = localStorage.getItem('memberName')??'';
  const justLoggedOut = localStorage.getItem('justLoggedOut')??'';
  const tryToLogin = localStorage.getItem('tryToLogin')??'';
  const loginExpired = localStorage.getItem('loginExpired')??'';
  const showMessage = localStorage.getItem('showMessage')??'';
  if (justLoggedIn === 'true') {
    showAlert('green', `會員 ${memberName} 已成功降落在評星宇宙！`);
  } 
  if (justLoggedOut === 'true') {
    showAlert('red', '您已離開評星宇宙');
  } 
  if (loginExpired === 'true')  {
    showAlert('red', showMessage);
  }
  if (tryToLogin === 'true') {
    document.getElementById('login').click();
  }
  localStorage.removeItem('justLoggedIn');
  localStorage.removeItem('memberName');
  localStorage.removeItem('justLoggedOut');
  localStorage.removeItem('tryToLogin');
  localStorage.removeItem('loginExpired')
  localStorage.removeItem('showMessage')

  // 綁定表單控制
  bindFormControl('.login-input', 'login-submit-button');
  bindFormControl('.signup-input', 'signup1-next-button');

});

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

function toggleMenu() {
  var menu = document.getElementById("member-menu-items");
  var arrow = document.getElementById("expand-arrow");
  if (menu.style.display === "none") {
    menu.style.display = "block";
    arrow.style.transform = "rotate(-180deg)"; // 箭頭旋轉效果
  } else {
    menu.style.display = "none";
    arrow.style.transform = "rotate(0deg)";
  }
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
  if (
    targetId === 'signupModal1' &&
    document.getElementById('signup-email').value === '' && 
    document.getElementById('signup-name').value === '' &&
    document.getElementById('signup-password').value === '' &&
    document.getElementById('signup-check-password').value === ''
  ) {    
    closeOpenedModal();
  } else {
    closeOpenedModal();
    let modal = new bootstrap.Modal(document.getElementById('cancelSignupModal'));
    document.getElementById('cancel-signup-cancel-button').setAttribute('onclick', `restoreSignup('${targetId}')`);
    modal.show();
  }
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

function updatePreferences(target) {
  const searchRadius = document.getElementById(`${target}-search-radius-input`).value;
  const willOpen = document.getElementById(`${target}-will-open`).checked?1:0;
  const openNow = document.getElementById(`${target}-open-now`).checked?1:0;
  const willClose = document.getElementById(`${target}-will-close`).checked?1:0;
  const closeNow = document.getElementById(`${target}-close-now`).checked?1:0;
  const parking = document.getElementById(`${target}-parking`).checked?1:0;
  const wheelchairAccessible = document.getElementById(`${target}-wheelchair-accessible`).checked?1:0;
  const vegetarian = document.getElementById(`${target}-vegetarian`).checked?1:0;
  const healthy = document.getElementById(`${target}-healthy`).checked?1:0;
  const kidsFriendly = document.getElementById(`${target}-kids-friendly`).checked?1:0;
  const petsFriendly = document.getElementById(`${target}-pets-friendly`).checked?1:0;
  const genderFriendly = document.getElementById(`${target}-gender-friendly`).checked?1:0;
  const dilivery = document.getElementById(`${target}-delivery`).checked?1:0;
  const takeaway = document.getElementById(`${target}-takeaway`).checked?1:0;
  const dineIn = document.getElementById(`${target}-dine-in`).checked?1:0;
  const breakfast = document.getElementById(`${target}-breakfast`).checked?1:0;
  const brunch = document.getElementById(`${target}-brunch`).checked?1:0;
  const lunch = document.getElementById(`${target}-lunch`).checked?1:0;
  const dinner = document.getElementById(`${target}-dinner`).checked?1:0;
  const reservation = document.getElementById(`${target}-reservation`).checked?1:0;
  const groupFriendly = document.getElementById(`${target}-group-friendly`).checked?1:0;
  const familyFriendly = document.getElementById(`${target}-family-friendly`).checked?1:0;
  const toilet = document.getElementById(`${target}-toilet`).checked?1:0;
  const wifi = document.getElementById(`${target}-wifi`).checked?1:0;
  const cash = document.getElementById(`${target}-cash`).checked?1:0;
  const creditCard = document.getElementById(`${target}-credit-card`).checked?1:0;
  const debitCard = document.getElementById(`${target}-debit-card`).checked?1:0;
  const mobilePayment = document.getElementById(`${target}-mobile-payment`).checked?1:0;
  // 請求
  const formData = new FormData();
  formData.set('searchRadius', searchRadius);
  formData.set('willOpen', willOpen);
  formData.set('openNow', openNow);
  formData.set('willClose', willClose);
  formData.set('closeNow', closeNow);
  formData.set('parking', parking);
  formData.set('wheelchairAccessible', wheelchairAccessible);
  formData.set('vegetarian', vegetarian);
  formData.set('healthy', healthy);
  formData.set('kidsFriendly', kidsFriendly);
  formData.set('petsFriendly', petsFriendly);
  formData.set('genderFriendly', genderFriendly);
  formData.set('dilivery', dilivery);
  formData.set('takeaway', takeaway);
  formData.set('dineIn', dineIn);
  formData.set('breakfast', breakfast);
  formData.set('brunch', brunch);
  formData.set('lunch', lunch);
  formData.set('dinner', dinner);
  formData.set('reservation', reservation);
  formData.set('groupFriendly', groupFriendly);
  formData.set('familyFriendly', familyFriendly);
  formData.set('toilet', toilet);
  formData.set('wifi', wifi);
  formData.set('cash', cash);
  formData.set('creditCard', creditCard);
  formData.set('debitCard', debitCard);
  formData.set('mobilePayment', mobilePayment);
  fetch('/member/handler/update-preference.php', {
    method: 'POST',
    credentials: 'same-origin',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showAlert('green', data.message);
    } else {
      showAlert('red', data.message);
    }
  })
  .catch(() => {showAlert('red', '更新偏好過程中發生非預期的錯誤');});
}


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