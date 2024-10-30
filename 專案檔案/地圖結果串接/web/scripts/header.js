let preloading = true;

let alertTimeout;
function showAlert(type, message, timeout = 5000) {
  const alertBox = document.getElementById('alert-box');
  clearTimeout(alertTimeout);
  alertBox.classList.remove('alert-show');
  alertBox.classList.add('alert-hide');
  setTimeout(function() {
    alertBox.className = 'alert';
    alertBox.innerHTML = '';
    alertBox.classList.add(`alert-${type}`);
    alertBox.classList.remove('alert-hide');
    alertBox.classList.add('alert-show');
    const messageText = document.createElement('span');
    messageText.textContent = message;
    alertBox.appendChild(messageText);
    const closeButton = document.createElement('button');
    closeButton.textContent = 'X';
    closeButton.classList.add('alert-close');
    closeButton.onclick = hideAlert;
    alertBox.appendChild(closeButton);
    alertTimeout = setTimeout(function() { hideAlert(); }, timeout);
  }, 50);
}

function paramAlert() {
  showAlert('red', '非預期的參數錯誤');
}
function exceptionAlert(process) {
  showAlert('red', process + '時發生非預期的錯誤');
}

function hideAlert() {
  const alertBox = document.getElementById('alert-box');
  if (alertBox.classList.contains('alert-show')) {
    alertBox.classList.remove('alert-show');
    alertBox.classList.add('alert-hide');
  }
}

window.onload = function() {

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
    setTimeout(function() {showModal('loginModal');}, 500);
  }
  localStorage.removeItem('justLoggedIn');
  localStorage.removeItem('memberName');
  localStorage.removeItem('justLoggedOut');
  localStorage.removeItem('tryToLogin');
  localStorage.removeItem('loginExpired')
  localStorage.removeItem('showMessage')

}

document.addEventListener('DOMContentLoaded', function () {

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

function showCondition() {
  const mapElem = document.getElementById('map');
  const city = document.getElementById('condition-city-select').value;
  const dist = document.getElementById('condition-dist-select').value;
  const searchRadius = document.getElementById('condition-search-radius-input').value;
  const willOpen = document.getElementById('condition-will-open').checked?1:0;
  const openNow = document.getElementById('condition-open-now').checked?1:0;
  const willClose = document.getElementById('condition-will-close').checked?1:0;
  const closeNow = document.getElementById('condition-close-now').checked?1:0;
  const serviceCount = countCheckedServiceMarks();
  const container = document.getElementById('filter-container');
  const type = document.getElementById('condition-geo-radio').checked?'geo':'distance';
  container.innerHTML = `<p class="filter-title-${type}">條件</p>`;
  if (type === 'geo'){
    container.innerHTML += `<p class="filter-item-${type}">搜尋區域：${city}${dist}</p>`;
    mapElem.style.border = '3px solid #aa5d00';
  } else {
    container.innerHTML += `<p class="filter-item-${type}">搜尋半徑：${searchRadius} 公尺</p>`;
    mapElem.style.border = '3px solid #663399';
  }
  const openStatus = [];
  if (willOpen) openStatus.push('即將營業');
  if (openNow) openStatus.push('營業中');
  if (willClose) openStatus.push('即將打烊');
  if (closeNow) openStatus.push('已打烊');
  container.innerHTML += `<p class="filter-item-${type}">狀態：${openStatus.join('/')}</p>`;
  if (serviceCount > 0) {
    container.innerHTML += `<p class="filter-item-${type}-light">包含 ${serviceCount} 項需求服務</p>`;
  }
  closeOpenedModal();
  this.window.history.replaceState({}, '', `${location.protocol}//${location.host}${location.pathname}?data=${getEncodeSearchParams()}`);
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

function closeMemberMenu() {
  document.getElementById('dropdownMenu').style.display='none';
}

/* 自動讓登入表單的第一個輸入框取得焦點 */
document.getElementById('loginModal').addEventListener('shown.bs.modal', function () {
  document.getElementById('login-email').focus()
})
document.getElementById('signupModal1').addEventListener('shown.bs.modal', function () {
  document.getElementById('signup-email').focus()
})

function confirmExternalLink(link) {
  closeOpenedModal();
  let modal = new bootstrap.Modal(document.getElementById('externalLinkModal'));
  document.getElementById('externalLink-confirm-button').setAttribute('onclick', `setTimeout(function(){toUrl('${link}');},300);`);
  modal.show();
}

function confirmNavigate(lat, lng) {
  closeOpenedModal();
  let modal = new bootstrap.Modal(document.getElementById('checkNavigationModal'));
  document.getElementById('navigation-confirm-button').setAttribute('onclick', `setTimeout(function(){navigateToStore(${lat},${lng});},300);`);
  modal.show();
}

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
    document.getElementById('cancelSignup-cancel-button').setAttribute('onclick', `showModal('${targetId}')`);
    modal.show();
  }
}

function showModal(targetId) {
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
    iconContainer.innerHTML = '<a href="/home" draggable="false"><img src="/images/logo-yellow.png" id="web_logo" style="margin-top:-8px;">評星宇宙</a>';
} else {
    iconContainer.innerHTML = '<a href="/home" draggable="false"><img src="/images/logo-blue+.png" id="web_logo" style="margin-top:-8px;">評星宇宙</a>';
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