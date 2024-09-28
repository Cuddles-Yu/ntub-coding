/* 點擊漢堡圖示時，顯示/隱藏選單 */
document.getElementById('hamburger_btn').addEventListener('click', function() {
  var navMenu = document.getElementById('nav_menu2');
  var overlay = document.getElementById('overlay');
  navMenu.classList.toggle('show');  // 切換顯示與隱藏
  overlay.classList.toggle('show');
});

document.getElementById('overlay').addEventListener('click', function() {
  var navMenu = document.getElementById('nav_menu2');
  var overlay = document.getElementById('overlay');
  navMenu.classList.remove('show');
  overlay.classList.remove('show');
});

//搜尋結果滾動條隱藏
const storeContainer = document.querySelector('.store');

if (storeContainer.scrollWidth > storeContainer.clientWidth) {
    storeContainer.style.overflowX = 'scroll'; // 顯示垂直滾動條
} else {
    storeContainer.style.overflowX = 'auto'; // 隱藏垂直滾動條
}

document.getElementById('keyword').addEventListener('keydown', function(event) {
  if (event.key === 'Enter') {
    event.preventDefault(); // 防止表單的預設提交行為
    document.getElementById('search-button').click(); // 觸發按鈕點擊事件
  }
});
