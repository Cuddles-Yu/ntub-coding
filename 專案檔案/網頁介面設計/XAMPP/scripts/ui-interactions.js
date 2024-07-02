/*var accordionItems = document.querySelectorAll('.accordion-item');
  accordionItems.forEach(function(item) {
    item.addEventListener('click', function() {
      // 切換最上層項目的類別以應用不同的CSS樣式
      if (this.classList.contains('active')) {
        this.classList.remove('active');
        this.querySelector('.accordion-item-content').style.maxHeight = '30em';
      } else {
        // 移除其他已展開的項目的類別和樣式
        accordionItems.forEach(function(el) {
          el.classList.remove('active');
          el.querySelector('.accordion-item-content').style.maxHeight = null;
        });
        // 展開被點擊的項目
        this.classList.add('active');
        this.querySelector('.accordion-item-content').style.maxHeight = '15.4em';
      }
    });
  });
  */