let LOADING_DURATION = 500;

function generateLoadingOverlay(during=0) {
  // 生成全景遮罩載入等待動畫
  var overlay = document.createElement('div');
  overlay.setAttribute('class', 'modal-backdrop fade show');
  overlay.setAttribute('style', '--bs-backdrop-opacity:0.3;align-content:center;');
  var loading = document.createElement('div');
  loading.setAttribute('class', 'rotating');
  var img = document.createElement('img');
  img.src = "/images/icon-loading.png";
  img.style.width = '50px';
  img.style.height = '50px';
  loading.appendChild(img);
  overlay.appendChild(loading);
  document.body.appendChild(overlay);
  if (during > 0) {
    setTimeout(function() {
      overlay.remove();
    }, during);
  }
  return overlay;
}

function logoutRequest() {
  fetch('/member/handler/logout.php', {
    method: 'POST',
    credentials: 'same-origin',
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        console.log(window.location.href);
        cancelModal();
        generateLoadingOverlay();
        localStorage.setItem('justLoggedOut', 'true');      
        setTimeout(function() {
          window.location.reload(true);
        }, LOADING_DURATION);      
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showAlert('red', '登出失敗，'+error);
    });
}

function loginRequest() {
  const emailInput = document.getElementById('login-email')      
  const passwordInput = document.getElementById('login-password')
  const rememberInput = document.getElementById('remember')
  const email = emailInput.value;   
  const password = passwordInput.value;
  if (!email || !password) {
    !email ? emailInput.focus() : passwordInput.focus();
    document.getElementById('loginError').innerText = `${!email?'電子郵件':'密碼'}欄位不能為空。`;
    document.getElementById('login-message').style.display = 'block';
    return;
  }
  const formData = new FormData();
  formData.set('email', email);     
  formData.set('password', password); 
  formData.set('remember', rememberInput.checked?1:0);
  fetch('./member/handler/login.php', {
    method: 'POST',
    credentials: 'same-origin',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        cancelModal();
        generateLoadingOverlay();
        localStorage.setItem('justLoggedIn', 'true');
        localStorage.setItem('memberName', data.name);
        setTimeout(function() {
          window.location.reload(true);          
        }, LOADING_DURATION);
      } else {
        if (!('showMessage'in data)) {
          document.getElementById('loginError').innerText = data.message;
          document.getElementById('login-message').style.display = 'block';        
        }
        emailInput.focus();
      }
      clearInputs(emailInput, passwordInput);
    })
    .catch(error => {
      console.error('Error:', error);
      document.getElementById('loginError').innerText = `發生非預期的錯誤，請稍後再試。`;
      document.getElementById('login-message').style.display = 'block';
    });    
}

async function emailVerifyRequest() {
  const emailInput = document.getElementById('signup-email');
  const email = emailInput.value.trim();
  
  // 驗證
  if (!email) {
    emailInput.focus();
    document.getElementById('signupError').innerText = '電子郵件欄位不能為空值或空格。';
    document.getElementById('signup-message').style.display = 'block';
    return false;
  } else {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      emailInput.focus();
      document.getElementById('signupError').innerText = '請輸入有效的電子郵件地址。';
      document.getElementById('signup-message').style.display = 'block';      
      return false;
    }
  }

  // 請求
  const formData = new FormData();
  formData.set('email', email);

  try {
    const response = await fetch('./member/handler/email_verify.php', {
      method: 'POST',
      credentials: 'same-origin',
      body: formData
    });
    const data = await response.json();    
    if (data.success) {
      return true;
    } else {
      emailInput.focus();
      document.getElementById('signupError').innerText = data.message;
      document.getElementById('signup-message').style.display = 'block';
      return false;
    }
  } catch (error) {
    console.error('Error:', error);
    document.getElementById('signupError').innerText = `發生非預期的錯誤，請稍後再試。`;
    document.getElementById('signup-message').style.display = 'block';
    return false;
  }
}


async function accountVerifyRequest() {  
  const nameInput = document.getElementById('signup-name');
  const name = nameInput.value.trim();
  const passwordInput = document.getElementById('signup-password');
  const password = passwordInput.value.trim();
  const checkPasswordInput = document.getElementById('signup-check-password');
  const checkPassword = checkPasswordInput.value.trim();
  const consentInput = document.getElementById('signup-consent');

  const isEmailValid = await emailVerifyRequest();
  if (!isEmailValid) return;
  if (!name) {
    nameInput.focus();
    document.getElementById('signupError').innerText = '名稱欄位不能為空值或空格。';
    document.getElementById('signup-message').style.display = 'block';
    return;
  }
  if (!password) {
    passwordInput.focus();
    document.getElementById('signupError').innerText = '密碼欄位不能為空值或空格。';
    document.getElementById('signup-message').style.display = 'block';
    return;
  }
  if (password !== checkPassword) {
    checkPasswordInput.focus();
    document.getElementById('signupError').innerText = '密碼與確認密碼不符。';
    document.getElementById('signup-message').style.display = 'block';
    return;
  }
  if (!consentInput.checked) {
    document.getElementById('signupError').innerText = '請閱讀並同意我們的服務條款。';
    document.getElementById('signup-message').style.display = 'block';
    return;
  }
  document.getElementById('signup-message').style.display = 'none';
  closeOpenedModal();
  let modal = new bootstrap.Modal(document.getElementById('signupModal2'));
  modal.show();
}

function signupRequest() {
  const email = document.getElementById('signup-email').value;
  const name = document.getElementById('signup-name').value;
  const password = document.getElementById('signup-password').value;
  const atmosphere = document.getElementById('signup-atmosphere').value;
  const product = document.getElementById('signup-product').value;
  const service = document.getElementById('signup-service').value;
  const price = document.getElementById('signup-price').value;
  const searchRadius = document.getElementById('signup-search-radius-input').value;
  const openNow = document.getElementById('signup-open-now').checked?1:0;
  const parking = document.getElementById('signup-parking').checked?1:0;
  const wheelchairAccessible = document.getElementById('signup-wheelchair-accessible').checked?1:0;
  const vegetarian = document.getElementById('signup-vegetarian').checked?1:0;
  const healthy = document.getElementById('signup-healthy').checked?1:0;
  const kidsFriendly = document.getElementById('signup-kids-friendly').checked?1:0;
  const petsFriendly = document.getElementById('signup-pets-friendly').checked?1:0;
  const genderFriendly = document.getElementById('signup-gender-friendly').checked?1:0;
  const dilivery = document.getElementById('signup-delivery').checked?1:0;
  const takeaway = document.getElementById('signup-takeaway').checked?1:0;
  const dineIn = document.getElementById('signup-dine-in').checked?1:0;
  const breakfast = document.getElementById('signup-breakfast').checked?1:0;
  const brunch = document.getElementById('signup-brunch').checked?1:0;
  const lunch = document.getElementById('signup-lunch').checked?1:0;
  const dinner = document.getElementById('signup-dinner').checked?1:0;
  const reservation = document.getElementById('signup-reservation').checked?1:0;
  const groupFriendly = document.getElementById('signup-group-friendly').checked?1:0;
  const familyFriendly = document.getElementById('signup-family-friendly').checked?1:0;
  const toilet = document.getElementById('signup-toilet').checked?1:0;
  const wifi = document.getElementById('signup-wifi').checked?1:0;
  const cash = document.getElementById('signup-cash').checked?1:0;
  const creditCard = document.getElementById('signup-credit-card').checked?1:0;
  const debitCard = document.getElementById('signup-debit-card').checked?1:0;
  const mobilePayment = document.getElementById('signup-mobile-payment').checked?1:0;
  // 請求
  const formData = new FormData();
  formData.set('email', email);
  formData.set('name', name);
  formData.set('password', password);
  formData.set('atmosphere', atmosphere);
  formData.set('product', product);
  formData.set('service', service);
  formData.set('price', price);
  formData.set('searchRadius', searchRadius);
  formData.set('openNow', openNow);
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
  fetch('./member/handler/signup.php', {
    method: 'POST',
    credentials: 'same-origin',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    cancelModal(); 
    if (data.success) {
      showAlert('brown', '您已成功註冊為評星宇宙會員。');
    } else {
      showAlert('red', '發生了預期之外的錯誤，請重新註冊。');
    }
  })
  .catch(error => console.error('註冊過程中發生錯誤：', error));
}