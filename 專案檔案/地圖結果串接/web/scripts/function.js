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

function shareStore(storeId) {
  const shareData = {
    title: '分享餐廳連結',
    url: `https://commentspace.ascdc.tw/detail?id=${storeId}`,
  };
  if (navigator.share) {
    navigator.share(shareData).then(() => {
    }).catch(() => {
      showAlert('red', '分享過程中發生錯誤，請稍後再試');
    });
  } else {
    navigator.clipboard.writeText(shareData.url).then(function() {
      showAlert('orange', '此瀏覽器不支援分享功能，已自動複製連結');
    }).catch(() => {
      showAlert('red', '複製連結失敗，請稍後再試');
    });
  }
}

function toggleFavorite(element, storeId) {
  element.disabled = true;
  const formData = new FormData();
  formData.append('storeId', storeId);
  formData.append('currentState', element.querySelector('i').classList.contains('fi-sr-heart')?'1':'0');
  fetch('/handler/toggle_favorite.php', {
    method: 'POST',
    body: formData,
    credentials: 'same-origin'
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const storeBody = element.closest('.store-body');
      const storeCard = element.closest('.card');
      const bookmarks = document.querySelectorAll('.store-bookmark');
      if (data.isFavorite) {
        if (bookmarks.length > 0) {
          bookmarks.forEach(bookmark => {
            bookmark.classList.remove('fi-br-heart');
            bookmark.classList.add('fi-sr-heart');
          });
        } else {
          const bookmark = element.querySelector('i');
          bookmark.classList.remove('fi-br-heart');
          bookmark.classList.add('fi-sr-heart');
        }
        if (storeBody) storeBody.classList.add('store-card-favorite');
        if (storeCard) storeCard.classList.add('store-card-favorite');
        showAlert(data.refresh?'purple':'green', data.message, 3000);
      } else {
        if (bookmarks.length > 0) {
          bookmarks.forEach(bookmark => {
            bookmark.classList.remove('fi-sr-heart');
            bookmark.classList.add('fi-br-heart');
          });
        } else {
          const bookmark = element.querySelector('i');
          bookmark.classList.remove('fi-sr-heart');
          bookmark.classList.add('fi-br-heart');
        }
        if (storeBody) storeBody.classList.remove('store-card-favorite');
        if (storeCard) storeCard.classList.remove('store-card-favorite');
        showAlert(data.refresh?'purple':'dark-orange', data.message, 3000);
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
  .catch(() => { exceptionAlert('收藏餐廳'); })
  .finally(() => { element.disabled = false; });
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
    nc: document.getElementById('navigation-category-select').value,
    nl: document.getElementById('navigation-landmark-select').value,
    city: document.getElementById('condition-city-select').value,
    dist: document.getElementById('condition-dist-select').value,
    gm : document.getElementById('condition-geo-radio').checked,
    r: document.getElementById('condition-search-radius-input').value,
    wo: document.getElementById('condition-will-open').checked,
    on: document.getElementById('condition-open-now').checked,
    wc: document.getElementById('condition-will-close').checked,
    cn: document.getElementById('condition-close-now').checked,
    par: document.getElementById('condition-parking').checked,
    acc: document.getElementById('condition-wheelchair-accessible').checked,
    veg: document.getElementById('condition-vegetarian').checked,
    heal: document.getElementById('condition-healthy').checked,
    kid: document.getElementById('condition-kids-friendly').checked,
    pet: document.getElementById('condition-pets-friendly').checked,
    gr: document.getElementById('condition-gender-friendly').checked,
    dy: document.getElementById('condition-delivery').checked,
    ty: document.getElementById('condition-takeaway').checked,
    di: document.getElementById('condition-dine-in').checked,
    bf: document.getElementById('condition-breakfast').checked,
    bh: document.getElementById('condition-brunch').checked,
    lh: document.getElementById('condition-lunch').checked,
    dr: document.getElementById('condition-dinner').checked,
    res: document.getElementById('condition-reservation').checked,
    gp: document.getElementById('condition-group-friendly').checked,
    fy: document.getElementById('condition-family-friendly').checked,
    tt: document.getElementById('condition-toilet').checked,
    wifi: document.getElementById('condition-wifi').checked,
    cash: document.getElementById('condition-cash').checked,
    ct: document.getElementById('condition-credit-card').checked,
    dt: document.getElementById('condition-debit-card').checked,
    me: document.getElementById('condition-mobile-payment').checked,
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
  document.getElementById('navigation-category-select').value = data.nc;
  await updateLandmark();
  document.getElementById('navigation-landmark-select').value = data.nl;
  checkLandmark();
  if (data.gm) {
    radioChecked(document.getElementById('condition-geo-radio'), true);
  } else {
    radioChecked(document.getElementById('condition-distance-radio'), true);
  }
  document.getElementById('condition-search-radius-input').value = data.r;
  document.getElementById('condition-will-open').checked = data.wo;
  document.getElementById('condition-open-now').checked = data.on;
  document.getElementById('condition-will-close').checked = data.wc;
  document.getElementById('condition-close-now').checked = data.cn;
  document.getElementById('condition-parking').checked = data.par;
  document.getElementById('condition-wheelchair-accessible').checked = data.acc;
  document.getElementById('condition-vegetarian').checked = data.veg;
  document.getElementById('condition-healthy').checked = data.heal;
  document.getElementById('condition-kids-friendly').checked = data.kid;
  document.getElementById('condition-pets-friendly').checked = data.pet;
  document.getElementById('condition-gender-friendly').checked = data.gr;
  document.getElementById('condition-delivery').checked = data.dy;
  document.getElementById('condition-takeaway').checked = data.ty;
  document.getElementById('condition-dine-in').checked = data.di;
  document.getElementById('condition-breakfast').checked = data.bf;
  document.getElementById('condition-brunch').checked = data.bh;
  document.getElementById('condition-lunch').checked = data.lh;
  document.getElementById('condition-dinner').checked = data.dr;
  document.getElementById('condition-reservation').checked = data.res;
  document.getElementById('condition-group-friendly').checked = data.gp;
  document.getElementById('condition-family-friendly').checked = data.fy;
  document.getElementById('condition-toilet').checked = data.tt;
  document.getElementById('condition-wifi').checked = data.wifi;
  document.getElementById('condition-cash').checked = data.cash;
  document.getElementById('condition-credit-card').checked = data.ct;
  document.getElementById('condition-debit-card').checked = data.dt;
  document.getElementById('condition-mobile-payment').checked = data.me;
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
    .catch(() => { exceptionAlert('更新偏好設定'); });
}

function countCheckedServiceMarks() {
  const checkboxes = document.querySelectorAll('.service-mark');
  const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
  return checkedCount;
}

function toUrl(url, newTab = true) {
  if (newTab) {
    window.open(url, '_blank');
  } else {
    window.location.href = url;
  }
}

function goToDetailPage(storeId) {
  toUrl(`/detail?id=${storeId}`);
}
function redirectToDetailPage(storeId) {
  data = document.getElementById('searchResults').getAttribute('search-data');
  const formData = new FormData();
  formData.set('id', storeId);
  fetch('/member/handler/browse-store.php', {
    method: 'POST',
    credentials: 'same-origin',
    body: formData
  })
  toUrl(`/detail?id=${storeId}&data=${encodeURIComponent(data)}`);
}
function urlToDetailPage(storeId) {
  const data = new URLSearchParams(window.location.search).get('data')??null;
  toUrl(`/detail?id=${storeId}&data=${encodeURIComponent(data)}`, newTab=false);
}
function openSearchPage(keyword) {
  const encodedData = new URLSearchParams(window.location.search).get('data')??null;
  if (!encodedData) {
    toUrl(`/search?q=${encodeURIComponent(keyword)}`);
    return;
  }
  var data = decodeSearchParams(encodedData);
  data.q = keyword;
  toUrl(`/search?data=${encodeSearchParams(data)}`);
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

async function updateLandmark() {
  const selectedCategory = document.getElementById(`navigation-category-select`).value;
  const landmarkSelect = document.getElementById(`navigation-landmark-select`);
  if (selectedCategory === '') {
    landmarkSelect.innerHTML = "";
    landmarkSelect.disabled = true;
    return;
  }
  landmarkSelect.disabled = false;
  landmarkSelect.innerHTML = "<option value=''>(未選擇)</option>";
  const formData = new FormData();
  formData.set('category', selectedCategory);
  await fetch('/handler/get-landmarks.php', {
    method: 'POST',
    credentials: 'same-origin',
    body: formData
  }).then(response => response.json())
    .then(data => {
      data.forEach(landmark => {
        landmarkSelect.innerHTML += `<option value="${landmark['name']}" lng="${landmark['longitude']}" lat="${landmark['latitude']}">${landmark['name']}</option>`;
      });
    })
    .catch(() => { exceptionAlert('取得快速定位'); })
    .finally(() => { checkLandmark(); });
}

function checkLandmark() {
  const selectedLandmark = document.getElementById(`navigation-landmark-select`).value;
  document.getElementById('navigation-confirm-button').disabled = (selectedLandmark === '');
  document.getElementById('navigation-search-button').disabled = (selectedLandmark === '');
}

function setLandmark() {
  const landmarkSelect = document.getElementById(`navigation-landmark-select`).querySelector('option:checked');
  const distanceRadio = document.getElementById('condition-distance-radio');
  const lat = landmarkSelect.getAttribute('lat');
  const lng = landmarkSelect.getAttribute('lng');
  if (!distanceRadio.checked) distanceRadio.checked = true;
  setView([lat, lng], 14);
  showAlert('green', `已將地圖移動至${landmarkSelect.value}`);
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