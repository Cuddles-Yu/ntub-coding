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
  .finally(() => {element.disabled = false;});
}

function countCheckedServiceMarks() {
  const checkboxes = document.querySelectorAll('.service-mark');
  const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
  return checkedCount;
}

function updateArea(target) {
  const selectedCity = document.getElementById(`${target}-city-select`).value;
  const distSelect = document.getElementById(`${target}-dist-select`);    
  if (selectedCity === '') {
    distSelect.innerHTML = "";
    return;
  }
  distSelect.innerHTML = "<option value=''>(無限制)</option>";
  const formData = new FormData();
  formData.set('city', selectedCity);     
  fetch('/handler/get-dists.php', {
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