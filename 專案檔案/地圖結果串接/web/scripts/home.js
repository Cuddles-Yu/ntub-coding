document.querySelectorAll('.home-menu').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});
document.querySelectorAll('.home-page').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});

window.addEventListener('load', function () {     
  defaultLocate();  
  setCondition();
});

document.addEventListener('DOMContentLoaded', function () {

});

function syncToPreferences() {
  updatePreferences('condition', false);
  showAlert('green', '已同步至偏好設定');
}

function toSearchPage() {
  getCenter();
  const keyword = encodeURIComponent(document.getElementById('keyword').value);
  const lat = document.getElementById('map').getAttribute('data-lat');
  const lng = document.getElementById('map').getAttribute('data-lng');
  window.location.href = `search?q=${keyword}&lat=${lat}&lng=${lng}`;
}


document.querySelectorAll('.title-text-2').forEach(tab => {
  tab.addEventListener('click', function() {
      const targetTab = this.getAttribute('data-tab');
      document.querySelectorAll('[class^="tab-content"]').forEach(content => {
          content.classList.remove('active');
      });
      document.querySelector(`.${targetTab}`).classList.add('active');
      generateStoreSuggestion(targetTab);
  });
});

function generateStoreSuggestion(content) {
  var searchResults = document.getElementById(content);
  const formData = new FormData();
  formData.set('q', '蛋塔');      
  fetch('../struc/store-suggestion.php', {
    method: 'POST',
    credentials: 'same-origin',
    body: formData
  })
    .then(response => response.text())
    .then(data => {
      if (data && data.trim() !== "") {
        searchResults.innerHTML = data;
      } else {
        searchResults.innerHTML = "<p>沒有找到相關結果。</p>";
      }
    })
    .catch(() => {showAlert('red', '推薦餐廳過程中發生非預期的錯誤');});
}

document.getElementById('keyword').addEventListener('keydown', function(event) {
  if (event.key === 'Enter') {
    event.preventDefault(); // 防止表單的預設提交行為
    document.getElementById('search-button').click(); // 觸發按鈕點擊事件
  }
});

document.getElementById('overlay').addEventListener('click', function() {
  var navMenu = document.getElementById('nav_menu2');
  var overlay = document.getElementById('overlay');
  navMenu.classList.remove('show');
  overlay.classList.remove('show');
});