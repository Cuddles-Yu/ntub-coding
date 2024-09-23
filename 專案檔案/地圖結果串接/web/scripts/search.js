//搜尋結果滾動條隱藏
const storeContainer = document.querySelector('.store');

if (storeContainer.scrollWidth > storeContainer.clientWidth) {
    storeContainer.style.overflowX = 'scroll'; // 顯示垂直滾動條
} else {
    storeContainer.style.overflowX = 'auto'; // 隱藏垂直滾動條
}