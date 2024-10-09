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
  .catch(error => {
    console.error('收藏過程中發生錯誤：', error);
  })
  .finally(() => {
    element.disabled = false;
  });
}