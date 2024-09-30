function toSearchPage() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (position) {
      var userLat = position.coords.latitude;
      var userLng = position.coords.longitude;
      localStorage.setItem('userLat', userLat);
      localStorage.setItem('userLng', userLng);

      // 在成功獲取位置後跳轉到 search 頁面
      window.location.href = `search?q=${encodeURIComponent(document.getElementById('keyword').value)}`;
    }, function (error) {
      console.error('無法取得您的位置: ' + error.message);

      // 即使無法獲取位置也跳轉到 search 頁面
      window.location.href = `search?q=${encodeURIComponent(document.getElementById('keyword').value)}`;
    });
  } else {
    console.error('您的瀏覽器不支援地理定位功能。');

    // 如果瀏覽器不支援地理定位，直接跳轉到 search 頁面
    window.location.href = `search?q=${encodeURIComponent(document.getElementById('keyword').value)}`;
  }
}

/* 點擊漢堡圖示時，顯示/隱藏選單 */
document.getElementById('hamburger_btn').addEventListener('click', function() {
  var navMenu = document.getElementById('nav_menu2');
  var overlay = document.getElementById('overlay');
  if (navMenu.classList.contains('show')) {
      navMenu.classList.remove('show');
      overlay.classList.remove('show');
  } else {
      navMenu.classList.add('show');
      overlay.classList.add('show');
  }
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
  const formData = new FormData();
  formData.set('q', '蛋塔');      
  fetch('../struc/store_suggestion.php', {
    method: 'POST',
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
    .catch(error => console.error('Error:', error));
}

document.getElementById('scroll-to-tertiary').addEventListener('click', function() {
  document.getElementById('tertiary-content').scrollIntoView({
      behavior: 'smooth',
      block: 'start'
  });
});

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

//card滾動
document.addEventListener('DOMContentLoaded', function () {
  const restaurantGroups = document.querySelectorAll('.restaurant-group');
  const leftArrows = document.querySelectorAll('.left-arrow');
  const rightArrows = document.querySelectorAll('.right-arrow');

  const scrollAmount = 690; // 每次滾動的像素量，可以根據需要調整

  function updateArrowVisibility(restaurantGroup, leftArrow, rightArrow) {
    // 檢查卡片的總寬度是否超過容器寬度
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

  restaurantGroups.forEach(function (restaurantGroup, index) {
    const leftArrow = leftArrows[index];
    const rightArrow = rightArrows[index];

    leftArrow.addEventListener('click', function () {
      restaurantGroup.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    });

    rightArrow.addEventListener('click', function () {
      restaurantGroup.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    });

    // 初次加載和窗口調整大小時更新箭頭可見性
    window.addEventListener('resize', function () {
      updateArrowVisibility(restaurantGroup, leftArrow, rightArrow);
    });

    // 初次加載時運行
    updateArrowVisibility(restaurantGroup, leftArrow, rightArrow);
  });
});

//篩選條件滾動條隱藏
const filterContainer = document.querySelector('.filter-result');

if (filterContainer.scrollWidth > filterContainer.clientWidth) {
  filterContainer.style.overflowX = 'scroll'; // 顯示垂直滾動條
} else {
  filterContainer.style.overflowX = 'auto'; // 隱藏垂直滾動條
}

document.getElementById('toggle-password').addEventListener('click', function() {
  var passwordInput = document.getElementById('password');
  var passwordIcon = document.getElementById('toggle-password');
  if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      passwordIcon.setAttribute('src', 'images/password-show.png');
  } else {
      passwordInput.type = 'password';
      passwordIcon.setAttribute('src', 'images/password-hide.png');
  }
});

//card手動滑鼠滾動
document.addEventListener('DOMContentLoaded', function() {
  const restaurantGroups = document.querySelectorAll('.restaurant-group');
  
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
      const walk = (x - startX) * 5; // 滑動速度調整
      restaurantGroup.scrollLeft = scrollLeft - walk;
    });
  });
});
