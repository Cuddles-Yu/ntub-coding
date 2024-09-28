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

document.getElementById('overlay').addEventListener('click', function() {
  var navMenu = document.getElementById('nav_menu2');
  var overlay = document.getElementById('overlay');
  navMenu.classList.remove('show');
  overlay.classList.remove('show');
});

// 搜尋表單提交時，導向搜尋結果頁面!
document.getElementById('searchForm').addEventListener('submit', function(event) {
    event.preventDefault(); // 防止表單提交
    const keyword = document.getElementById('keyword').value;
    window.location.href = `search.html?keyword=${encodeURIComponent(keyword)}`;
});

//隱藏推薦滾動條
function handleScroll(container) {
    if (container.scrollHeight > container.clientHeight) {
      container.style.overflowY = 'scroll'; // 顯示滾動條
    } else {
      container.style.overflowY = 'auto'; // 隱藏滾動條
    }
  }
  
const containers = document.querySelectorAll('.restaurant-group, .restaurant-group-2, .restaurant-group-3');
containers.forEach(container => {
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
