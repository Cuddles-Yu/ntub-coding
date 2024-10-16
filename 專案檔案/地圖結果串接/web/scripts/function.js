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

function countCheckedServiceMarks() {
  const checkboxes = document.querySelectorAll('.service-mark');
  const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
  return checkedCount;
}

function redirectToDetailPage(storeId) {
  data = document.getElementById('searchResults').getAttribute('search-data');
  window.location.href = `detail?id=${storeId}&data=${encodeURIComponent(data)}`;
}
function urlToDetailPage(storeId) {
  const data = new URLSearchParams(window.location.search).get('data')??null;
  window.location.href = `detail?id=${storeId}&data=${encodeURIComponent(data)}`;
}
function openSearchPage(keyword) {
  const encodedData = new URLSearchParams(window.location.search).get('data')??null;
  var data = decodeSearchParams(encodedData);
  data.q = keyword;
  window.open(`search?data=${encodeSearchParams(data)}`, '_blank');
}

async function updateArea(target) {
  const distanceTitle = document.getElementById(`distance-title`);
  const geoTitle = document.getElementById(`geo-title`);
  const selectedCity = document.getElementById(`${target}-city-select`).value;
  const distSelect = document.getElementById(`${target}-dist-select`);
  if (selectedCity === '') {
    distSelect.innerHTML = "";
    distSelect.disabled = true;
    distanceTitle.style.color = "#663399";
    geoTitle.style.color = "";
    return;
  }
  distanceTitle.style.color = "";
  geoTitle.style.color = "#aa5d00";
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
}