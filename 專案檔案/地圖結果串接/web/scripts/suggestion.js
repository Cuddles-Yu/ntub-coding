document.querySelectorAll('.suggestion-menu').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});
document.querySelectorAll('.suggestion-page').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});

window.addEventListener('load', function () {
  generateStoreSuggestion('tab-content-1');
});

document.addEventListener('DOMContentLoaded', function () {

  document.querySelectorAll('.nav-link').forEach(tab => {
    tab.addEventListener('click', function() {
      const targetTab = tab.getAttribute('data-tab');
      if (targetTab !== 'tab-content-3' && tab.classList.contains('active')) return;
      document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
      tab.classList.add('active');
      document.querySelectorAll('[class^="tab-content"]').forEach(content => {
        content.classList.remove('active');
        content.innerHTML = '';
      });
      const targetContent = document.getElementById(targetTab);
      if (targetContent) {
        targetContent.classList.add('active');
        generateStoreSuggestion(targetTab);
      }
      if (targetTab === 'tab-content-3') {
        document.getElementById('random-nav-item').style.width = '134px';
        document.getElementById('tab-button-3').innerHTML = '隨機推薦 <i class="fi fi-br-refresh" style="font-size:14px"></i>';
      } else {
        document.getElementById('random-nav-item').style.width = '';
        document.getElementById('tab-button-3').innerText = '隨機推薦';
      }
    });
  });

});

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
  searchResults.innerHTML = '<div style="height:550px;align-content:center;"><div class="rotating"><img src="./images/icon-loading.png" width="30" height="30"></div><p style="text-align:center;">正在取得推薦餐廳...</p></div>'
  const formData = new FormData();
  formData.set('q', '蛋塔');
  fetch('/struc/store-suggestion.php', {
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

document.getElementById('overlay').addEventListener('click', function() {
  var navMenu = document.getElementById('nav_menu2');
  var overlay = document.getElementById('overlay');
  navMenu.classList.remove('show');
  overlay.classList.remove('show');
});

//隱藏推薦滾動條
function handleScroll(container) {
  if (container.scrollHeight > container.clientHeight) {
    container.style.overflowY = 'scroll'; // 顯示滾動條
  } else {
    container.style.overflowY = 'auto'; // 隱藏滾動條
  }
};
document.querySelectorAll('.restaurant-group, .restaurant-group-2, .restaurant-group-3').forEach(container => {
  handleScroll(container);
});