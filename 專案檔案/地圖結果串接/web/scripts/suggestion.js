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
      const targetContent = document.getElementById(targetTab);
      if (!targetContent) return;
      var regenerate = (targetContent.getElementsByClassName('carousel-container').length == 0);
      if (targetTab === 'tab-content-3') {
        document.getElementById('tab-button-3').innerHTML = '隨機推薦 <i class="fi fi-br-refresh" style="font-size:14px"></i>';
        if (tab.classList.contains('active')) regenerate = true;
      } else {
        document.getElementById('tab-button-3').innerText = '隨機推薦';
      }
      document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
      document.querySelectorAll('[class^="tab-content"]').forEach(content => content.classList.remove('active'));
      tab.classList.add('active');
      targetContent.classList.add('active');
      if (regenerate) generateStoreSuggestion(targetTab);
    });
  });

});

function generateStoreSuggestion(content) {
  var resultsContainer = document.getElementById(content);
  resultsContainer.innerHTML = `
    <div style="height:550px;align-content:center;">
      <div class="rotating">
        <img src="./images/icon-loading.png" width="50" height="50">
      </div>
      <p style="text-align:center;">正在取得推薦餐廳...</p>
    </div>`;
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
        resultsContainer.innerHTML = data;
        handleGrabContainer(resultsContainer);
      } else {
        resultsContainer.innerHTML = "<p>沒有找到相關結果。</p>";
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

function handleGrabContainer(resultsContainer) {
  const container = resultsContainer.querySelector('.grab-container');

  let isDown = false;
  let startX;
  let scrollLeft;
  let isDragging = false;

  const leftArrow = container.parentElement.querySelector('.left-arrow');
  const rightArrow = container.parentElement.querySelector('.right-arrow');

  // 獲取內部元素的寬度和 gap
  const getScrollDistance = () => {
    const firstItem = container.querySelector(':scope > *'); // 獲取第一個子元素
    if (!firstItem) return 0; // 如果沒有元素則不滾動

    const itemStyle = window.getComputedStyle(firstItem);
    const itemWidth = firstItem.offsetWidth;  // 單個子元素的寬度
    const gapWidth = parseInt(itemStyle.marginRight) || 0; // 元素之間的 margin 充當 gap

    // 滾動距離 = 3 個元素的寬度 + 2 個 gap 的寬度
    return (itemWidth * 4) + (gapWidth * 3);
  };

  // 判斷是否需要顯示箭頭和 grab 游標
  const checkScrollability = () => {
    if (container.scrollWidth > container.clientWidth) {
      // 容器可滾動，顯示按鈕和抓取游標
      container.style.cursor = 'grab';
      leftArrow.style.display = 'block';
      rightArrow.style.display = 'block';
    } else {
      // 容器不可滾動，隱藏按鈕和抓取游標
      container.style.cursor = 'default';
      leftArrow.style.display = 'none';
      rightArrow.style.display = 'none';
    }
  };

  // 初始化檢查滾動
  checkScrollability();

  // 綁定拖曳滾動事件
  container.addEventListener('mousedown', (e) => {
    isDown = true;
    container.classList.add('active');
    startX = e.pageX - container.offsetLeft;
    scrollLeft = container.scrollLeft;
  });

  container.addEventListener('mouseleave', () => {
    isDown = false;
    container.classList.remove('active');
  });

  container.addEventListener('mouseup', () => {
    isDown = false;
    container.classList.remove('active');
  });

  container.addEventListener('mousemove', (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - container.offsetLeft;
    const walk = x - startX;
    container.scrollLeft = scrollLeft - walk*3;  // 滾動容器
  });

  // 左右箭頭控制滾動
  leftArrow.addEventListener('click', () => {
    const scrollDistance = getScrollDistance();
    container.scrollBy({
      left: -scrollDistance, // 向左滾動的距離
      behavior: 'smooth' // 平滑滾動
    });
  });

  rightArrow.addEventListener('click', () => {
    const scrollDistance = getScrollDistance();
    container.scrollBy({
      left: scrollDistance, // 向右滾動的距離
      behavior: 'smooth' // 平滑滾動
    });
  });

}