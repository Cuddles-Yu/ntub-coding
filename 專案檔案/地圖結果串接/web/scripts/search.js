// 監聽 store-body 的點擊事件
document.querySelectorAll('.store-body').forEach(storeBody => {
    storeBody.addEventListener('click', function(event) {
      const target = event.target;
  
      // 檢查點擊的是否是 love, map-link 或 web，阻止冒泡
      if (target.closest('.love') || target.closest('.map-link') || target.closest('.web')) {
        event.stopPropagation(); // 阻止冒泡，防止觸發 store-body 的點擊事件
      } else {
        // 否則進行 store-body 的跳轉
        const storeName = this.querySelector('.store-name').textContent;
        const storeId = 'store-id'; // 根據具體需求設置 store-id
        redirectToDetailPage(storeName, storeId);
      }
    });
  });