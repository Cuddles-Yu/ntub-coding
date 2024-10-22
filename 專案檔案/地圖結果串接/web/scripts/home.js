document.querySelectorAll('.home-menu').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});
document.querySelectorAll('.home-page').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});

window.addEventListener('load', async function () {
  const urlParams = new URLSearchParams(window.location.search);
  const encodedData = urlParams.get('data')??null;
  const data = encodedData ? (decodeSearchParams(encodedData)??null) : null;
  if (data) await setConditionFromData(data);
  await defaultLocate();
  showCondition();
});

document.addEventListener('DOMContentLoaded', function () {

});

function toSearchPage() {
  getCenter();
  window.location.href = `search?data=${getEncodeSearchParams()}`;
}

function saveCondition() {
  showCondition();
  setTimeout(function() {
    toSearchPage();
  }, 100);
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