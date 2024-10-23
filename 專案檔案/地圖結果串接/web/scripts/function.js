window.addEventListener('load', function () {
  preloading = false;
});

function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    showAlert('green', '已複製到剪貼簿');
  }).catch(() => {
    showAlert('red', '複製到剪貼簿時發生錯誤');
  });
}

function toggleFavorite(element, storeId) {
  element.disabled = true;
  const formData = new FormData();
  formData.append('storeId', storeId);

  fetch('/handler/toggle_favorite.php', {
    method: 'POST',
    body: formData,
    credentials: 'same-origin'
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const storeBody = element.closest('.store-body');
      const storeCard = element.closest('.restaurant');
      if (data.isFavorite) {
        element.querySelector('img').src = 'images/button-favorite-active.png';
        if (storeBody) storeBody.classList.add('store-card-favorite');
        if (storeCard) storeCard.classList.add('store-card-favorite');
        showAlert('green', data.message, 3000);
      } else {
        element.querySelector('img').src = 'images/button-favorite-inactive.png';
        if (storeBody) storeBody.classList.remove('store-card-favorite');
        if (storeCard) storeCard.classList.remove('store-card-favorite');
        showAlert('dark-orange', data.message, 3000);
      }
    } else {
      showAlert('red', data.message);
      setTimeout(() => {
        closeOpenedModal();
        let modal = new bootstrap.Modal(document.getElementById('loginModal'));
        modal.show();
      }, 100);
    }
  })
  .catch(() => {showAlert('red', '收藏過程中發生非預期的錯誤');})
  .finally(() => {
    element.disabled = false;
  });
}

function toBase64(str) {
  return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function (match, p1) {
    return String.fromCharCode('0x' + p1);
  }));
}
function getEncodeSearchParams() {
  const data = {
    q: document.getElementById('keyword').value,
    lat: document.getElementById('map').getAttribute('data-lat'),
    lng: document.getElementById('map').getAttribute('data-lng'),
    city: document.getElementById('condition-city-select').value,
    dist: document.getElementById('condition-dist-select').value,
    gm : document.getElementById('condition-geo-radio').checked,
    r: document.getElementById('condition-search-radius-input').value,
    willOpen: document.getElementById('condition-will-open').checked,
    openNow: document.getElementById('condition-open-now').checked,
    willClose: document.getElementById('condition-will-close').checked,
    closeNow: document.getElementById('condition-close-now').checked,
    parking: document.getElementById('condition-parking').checked,
    accessible: document.getElementById('condition-wheelchair-accessible').checked,
    vegetarian: document.getElementById('condition-vegetarian').checked,
    healthy: document.getElementById('condition-healthy').checked,
    kids: document.getElementById('condition-kids-friendly').checked,
    pets: document.getElementById('condition-pets-friendly').checked,
    gender: document.getElementById('condition-gender-friendly').checked,
    delivery: document.getElementById('condition-delivery').checked,
    takeaway: document.getElementById('condition-takeaway').checked,
    dineIn: document.getElementById('condition-dine-in').checked,
    breakfast: document.getElementById('condition-breakfast').checked,
    brunch: document.getElementById('condition-brunch').checked,
    lunch: document.getElementById('condition-lunch').checked,
    dinner: document.getElementById('condition-dinner').checked,
    reservation: document.getElementById('condition-reservation').checked,
    group: document.getElementById('condition-group-friendly').checked,
    family: document.getElementById('condition-family-friendly').checked,
    toilet: document.getElementById('condition-toilet').checked,
    wifi: document.getElementById('condition-wifi').checked,
    cash: document.getElementById('condition-cash').checked,
    credit: document.getElementById('condition-credit-card').checked,
    debit: document.getElementById('condition-debit-card').checked,
    mobile: document.getElementById('condition-mobile-payment').checked,
  };
  return encodeSearchParams(data);
}

function fromBase64(base64) {
  return decodeURIComponent(Array.prototype.map.call(atob(base64), function(c) {
    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
  }).join(''));
}
function encodeSearchParams(data) {
  return encodeURIComponent(toBase64(JSON.stringify(data)));
}
function decodeSearchParams(encodedData) {
  const decodedData = fromBase64(decodeURIComponent(encodedData));
  return JSON.parse(decodedData);
}

async function setConditionFromData(data) {
  document.getElementById('condition-city-select').value = data.city;
  await updateArea('condition');
  document.getElementById('condition-dist-select').value = data.dist;
  if (data.gm) {
    radioChecked(document.getElementById('condition-geo-radio'), true);
  } else {
    radioChecked(document.getElementById('condition-distance-radio'), true);
  }
  document.getElementById('condition-search-radius-input').value = data.r;
  document.getElementById('condition-will-open').checked = data.willOpen;
  document.getElementById('condition-open-now').checked = data.openNow;
  document.getElementById('condition-will-close').checked = data.willClose;
  document.getElementById('condition-close-now').checked = data.closeNow;
  document.getElementById('condition-parking').checked = data.parking;
  document.getElementById('condition-wheelchair-accessible').checked = data.accessible;
  document.getElementById('condition-vegetarian').checked = data.vegetarian;
  document.getElementById('condition-healthy').checked = data.healthy;
  document.getElementById('condition-kids-friendly').checked = data.kids;
  document.getElementById('condition-pets-friendly').checked = data.petsFriendly;
  document.getElementById('condition-gender-friendly').checked = data.gender;
  document.getElementById('condition-delivery').checked = data.delivery;
  document.getElementById('condition-takeaway').checked = data.takeaway;
  document.getElementById('condition-dine-in').checked = data.dineIn;
  document.getElementById('condition-breakfast').checked = data.breakfast;
  document.getElementById('condition-brunch').checked = data.brunch;
  document.getElementById('condition-lunch').checked = data.lunch;
  document.getElementById('condition-dinner').checked = data.dinner;
  document.getElementById('condition-reservation').checked = data.reservation;
  document.getElementById('condition-group-friendly').checked = data.group;
  document.getElementById('condition-family-friendly').checked = data.family;
  document.getElementById('condition-toilet').checked = data.toilet;
  document.getElementById('condition-wifi').checked = data.wifi;
  document.getElementById('condition-cash').checked = data.cash;
  document.getElementById('condition-credit-card').checked = data.credit;
  document.getElementById('condition-debit-card').checked = data.debit;
  document.getElementById('condition-mobile-payment').checked = data.mobile;
}

function updatePreferences(target, show = true) {
  const city = document.getElementById(`${target}-city-select`).value;
  const dist = document.getElementById(`${target}-dist-select`).value;
  const searchMode = document.getElementById(`${target}-distance-radio`).checked?'distance':'geo';
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
  formData.set('city', city);
  formData.set('dist', dist);
  formData.set('searchMode', searchMode);
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
        if (show) showAlert('green', data.message);
      } else {
        if (show) showAlert('red', data.message);
      }
    })
    .catch(() => {showAlert('red', '更新偏好過程中發生非預期的錯誤');
  });
}

function countCheckedServiceMarks() {
  const checkboxes = document.querySelectorAll('.service-mark');
  const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
  return checkedCount;
}

function goToDetailPage(storeId) {
  window.location.href = `/detail?id=${storeId}`;
}
function redirectToDetailPage(storeId) {
  data = document.getElementById('searchResults').getAttribute('search-data');
  window.location.href = `/detail?id=${storeId}&data=${encodeURIComponent(data)}`;
}
function urlToDetailPage(storeId) {
  const data = new URLSearchParams(window.location.search).get('data')??null;
  window.location.href = `/detail?id=${storeId}&data=${encodeURIComponent(data)}`;
}
function openSearchPage(keyword) {
  const encodedData = new URLSearchParams(window.location.search).get('data')??null;
  var data = decodeSearchParams(encodedData);
  data.q = keyword;
  window.open(`/search?data=${encodeSearchParams(data)}`, '_blank');
}

function radioChecked(target, state) {
  target.checked = state;
  if (state) target.disabled = false;
  target.dispatchEvent(new Event('change'));
}
function radioDisabled(target, state) {
  target.disabled = state;
  target.dispatchEvent(new Event('change'));
}

function updateRadio(target) {
  document.getElementById(`${target}-geo-radio`).checked = (document.getElementById(`${target}-city-select`).value !== '');
}

async function updateArea(target) {
  const selectedCity = document.getElementById(`${target}-city-select`).value;
  const distSelect = document.getElementById(`${target}-dist-select`);
  const distanceRadio = document.getElementById(`${target}-distance-radio`);
  const geoRadio = document.getElementById(`${target}-geo-radio`);
  if (selectedCity === '') {
    distSelect.innerHTML = "";
    distSelect.disabled = true;
    if (!preloading) radioChecked(distanceRadio, true);
    return;
  }
  distSelect.disabled = false;
  distSelect.innerHTML = "<option value=''>(無限制)</option>";
  const formData = new FormData();
  formData.set('city', selectedCity);
  await fetch('/handler/get-dists.php', {
    method: 'POST',
    credentials: 'same-origin',
    body: formData
  }).then(response => response.json())
    .then(data => {
      data.forEach(dist => {
        distSelect.innerHTML += `<option value="${dist}">${dist}</option>`;
      });
    })
    .catch(error => {showAlert('red', error);}
  );
  if (!preloading) geoRadio.checked = !distanceRadio.checked;
  if (distanceRadio.checked) distanceRadio.dispatchEvent(new Event('change'));
  if (geoRadio.checked) geoRadio.dispatchEvent(new Event('change'));
}

function radioToggle(target, type) {
  const distanceRadio = document.getElementById(`${target}-distance-radio`);
  const geoRadio = document.getElementById(`${target}-geo-radio`);
  const distanceTitle = document.getElementById(`${target}-distance-title`);
  const geoTitle = document.getElementById(`${target}-geo-title`);
  const citySelect = document.getElementById(`${target}-city-select`);
  const conditionMode = target === 'condition'
  const distanceColor = '#663399';
  const geoColor = '#aa5d00';
  if (conditionMode || editing) {
    distanceTitle.style.color = (conditionMode&&type==='distance')?distanceColor:'black';
    distanceTitle.style.cursor = 'pointer';
    if (citySelect.value === '') {
      geoTitle.style.color = 'gray';
      geoTitle.style.cursor = 'default';
      geoRadio.disabled = true;
    } else {
      geoRadio.disabled = false;
      geoTitle.style.color = (conditionMode&&type==='geo')?geoColor:'black';
      geoTitle.style.cursor = 'pointer';
    }
  } else {
    distanceRadio.disabled = true;
    geoRadio.disabled = true;
    distanceTitle.style.color = 'black';
    distanceTitle.style.cursor = 'default';
    geoTitle.style.color = (citySelect.value === '')?'gray':'black';
    geoTitle.style.cursor = 'default';
  }
}


function addRadioChangeListener(target) {
  document.getElementById(`${target}-distance-radio`).addEventListener('change', function() {
    radioToggle(target, 'distance');
  });
  document.getElementById(`${target}-geo-radio`).addEventListener('change', function() {
    radioToggle(target, 'geo');
  });
}

function preventMultipleClick(event) {
  event.stopPropagation();
}

function targetFavorite(elem) {
  elem.closest('.content_row').classList.add('favorite-target');
}