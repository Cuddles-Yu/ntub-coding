document.querySelectorAll('.suggestion-menu').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});
document.querySelectorAll('.suggestion-page').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});

window.addEventListener('load', function () {

});

document.addEventListener('DOMContentLoaded', function () {

  // 自動載入熱門推薦的內容
  document.getElementById('tab-button-1').click();
  const restaurantGroups = document.querySelectorAll('.restaurant-group');
  const leftArrows = document.querySelectorAll('.left-arrow');
  const rightArrows = document.querySelectorAll('.right-arrow');
  const scrollAmount = 690;

  // 檢查卡片的總寬度是否超過容器寬度
  function updateArrowVisibility(restaurantGroup, leftArrow, rightArrow) {
    const cardScrollWidth = restaurantGroup.scrollWidth;
    const containerWidth = restaurantGroup.clientWidth;
    if (cardScrollWidth <= containerWidth) {
      leftArrow.classList.add('hidden');
      rightArrow.classList.add('hidden');
    } else {
      leftArrow.classList.remove('hidden');
      rightArrow.classList.remove('hidden');
    }
  }

  // 初次加載和窗口調整大小時更新箭頭可見性
  restaurantGroups.forEach(function (restaurantGroup, index) {
    const leftArrow = leftArrows[index];
    const rightArrow = rightArrows[index];
    leftArrow.addEventListener('click', function () {
      restaurantGroup.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    });
    rightArrow.addEventListener('click', function () {
      restaurantGroup.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    });
    window.addEventListener('resize', function () {
      updateArrowVisibility(restaurantGroup, leftArrow, rightArrow);
    });
    updateArrowVisibility(restaurantGroup, leftArrow, rightArrow);
  });

  //card手動滑鼠滾動
  restaurantGroups.forEach(function(restaurantGroup) {
    let isDown = false;
    let startX;
    let scrollLeft;
    restaurantGroup.addEventListener('mousedown', function(e) {
      isDown = true;
      restaurantGroup.classList.add('active');
      startX = e.pageX - restaurantGroup.offsetLeft;
      scrollLeft = restaurantGroup.scrollLeft;
    });
    restaurantGroup.addEventListener('mouseleave', function() {
      isDown = false;
      restaurantGroup.classList.remove('active');
    });
    restaurantGroup.addEventListener('mouseup', function() {
      isDown = false;
      restaurantGroup.classList.remove('active');
    });
    restaurantGroup.addEventListener('mousemove', function(e) {
      if (!isDown) return;
      e.preventDefault();
      const x = e.pageX - restaurantGroup.offsetLeft;
      const walk = (x - startX) * 5;
      restaurantGroup.scrollLeft = scrollLeft - walk;
    });
  });

});

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