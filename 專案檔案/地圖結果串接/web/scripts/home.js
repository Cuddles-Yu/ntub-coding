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
  }, 300);
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