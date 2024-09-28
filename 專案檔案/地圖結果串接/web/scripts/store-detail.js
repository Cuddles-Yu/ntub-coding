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


//營業時間展開框
//初始化 Popover
document.addEventListener('DOMContentLoaded', function () {
  // 初始化所有擁有 'status' class 的 popover
  var popovers = [].slice.call(document.querySelectorAll('.status'));
  
  popovers.forEach(function (popoverEl) {
    var popover = new bootstrap.Popover(popoverEl, {
      placement: 'bottom', // 固定為底部顯示
      fallbackPlacements: [] // 禁止根據空間自動調整方向
    });

    // 點擊空白處隱藏 Popover
    document.addEventListener('click', function (e) {
      if (!popoverEl.contains(e.target) && !document.querySelector('.popover')?.contains(e.target)) {
        popover.hide(); // 手動隱藏 Popover
      }
    });
  });
  
  console.log('store-detail.js資料引入成功！');
});

//其他分店展開框
const popoverTriggerList = [].slice.call(document.querySelectorAll('.other-store-rating'));

popoverTriggerList.forEach(function (popoverTriggerEl) {
  new bootstrap.Popover(popoverTriggerEl, {
    container: 'body'
  });
});

//店家資訊簡介顯示
// 獲取所有有 data-content 屬性的 li 元素
var items = document.querySelectorAll('li.introduction');

// 遍歷每個元素，檢查 data-content 屬性的值
items.forEach(function (item) {
  var content = item.getAttribute('data-content');
  if (content) {
    // 如果有内容，設置為 li 的文本內容
    item.textContent = content;
  } else {
    // 如果没有内容，隱藏 li 元素
    item.style.display = 'none';
  }
});

//提示框
// 初始化所有的 Tooltip
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})

//隱藏其他分店滾動條
const commentContainer = document.querySelector('.comment-group');

if (commentContainer.scrollHeight > commentContainer.clientHeight) {
  commentContainer.style.overflowY = 'scroll'; // 顯示垂直滾動條
} else {
  commentContainer.style.overflowY = 'auto'; // 隱藏垂直滾動條
}

//樣本選擇與排序關係
document.addEventListener('DOMContentLoaded', function () {
  // 獲取樣本選擇按鈕和排序按鈕
  const btnRadio1 = document.getElementById('btnradio1');
  const btnRadio2 = document.getElementById('btnradio2');
  const btnRadio3 = document.getElementById('btnradio3');
  const btnRadio4 = document.getElementById('btnradio4');
  const sortSelect = document.querySelector('.sort-button .form-select');

  // 為樣本選擇按鈕添加事件監聽
  btnRadio3.addEventListener('change', function () {
    if (btnRadio3.checked) {
      sortSelect.value = "由高至低"; // 當選擇"評分最高"時，設置排序為"由高至低"
    }
  });

  btnRadio4.addEventListener('change', function () {
    if (btnRadio4.checked) {
      sortSelect.value = "由低至高"; // 當選擇"評分最低"時，設置排序為"由低至高"
    }
  });

  // 可選: 如果需要再選擇"全部"或"最相關"時重製排序，可以這樣做
  btnRadio1.addEventListener('change', function () {
    if (btnRadio1.checked) {
      sortSelect.value = "相關性"; // 重製排序為"最相關"
    }
  });

  btnRadio2.addEventListener('change', function () {
    if (btnRadio2.checked) {
      sortSelect.value = "相關性"; // 重製排序為"最相關
    }
  });
});

//水平滾動箭頭控制(推薦餐點)
document.addEventListener('DOMContentLoaded', function () {
  const groupCard = document.querySelector('.group-card');
  const leftArrow = document.querySelector('.left-arrow');
  const rightArrow = document.querySelector('.right-arrow');

  const scrollAmount = 500; // 每次滾動的像素量，可以根據需要調整

  function updateArrowVisibility() {
    // 檢查卡片的總寬度是否超過容器寬度
    const cardScrollWidth = groupCard.scrollWidth;
    const containerWidth = groupCard.clientWidth;

    if (cardScrollWidth <= containerWidth) {
      leftArrow.classList.add('hidden');
      rightArrow.classList.add('hidden');
    } else {
      leftArrow.classList.remove('hidden');
      rightArrow.classList.remove('hidden');
    }
  }

  leftArrow.addEventListener('click', function () {
    groupCard.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
  });

  rightArrow.addEventListener('click', function () {
    groupCard.scrollBy({ left: scrollAmount, behavior: 'smooth' });
  });

  // 在頁面加載和窗口調整大小時更新箭頭可行性
  window.addEventListener('resize', updateArrowVisibility);
  updateArrowVisibility(); // 初次加載時運行
});

//手動滑鼠滾動(推薦餐點)
document.addEventListener('DOMContentLoaded', function() {
  const CardGroups = document.querySelectorAll('.group-card');
  
  CardGroups.forEach(function(CardGroups) {
    let isDown = false;
    let startX;
    let scrollLeft;

    CardGroups.addEventListener('mousedown', function(e) {
      isDown = true;
      CardGroups.classList.add('active');
      startX = e.pageX - CardGroups.offsetLeft;
      scrollLeft = CardGroups.scrollLeft;
    });

    CardGroups.addEventListener('mouseleave', function() {
      isDown = false;
      CardGroups.classList.remove('active');
    });

    CardGroups.addEventListener('mouseup', function() {
      isDown = false;
      CardGroups.classList.remove('active');
    });

    CardGroups.addEventListener('mousemove', function(e) {
      if (!isDown) return;
      e.preventDefault();
      const x = e.pageX - CardGroups.offsetLeft;
      const walk = (x - startX) * 5; // 滑動速度調整
      CardGroups.scrollLeft = scrollLeft - walk;
    });
  });
});

//水平滾動箭頭控制(最多人提到)
document.addEventListener('DOMContentLoaded', function () {
  const tagGroup = document.querySelector('.tag-group');
  const leftArrow2 = document.querySelector('.left-arrow-2');
  const rightArrow2 = document.querySelector('.right-arrow-2');

  const scrollAmount = 400; // 每次滾動的像素量，可以根據需要調整

  function updateArrowVisibility() {
    // 檢查卡片的總寬度是否超過容器寬度
    const tagScrollWidth = tagGroup.scrollWidth;
    const containerWidth = tagGroup.clientWidth;

    if (tagScrollWidth <= containerWidth) {
      leftArrow2.classList.add('hidden');
      rightArrow2.classList.add('hidden');
    } else {
      leftArrow2.classList.remove('hidden');
      rightArrow2.classList.remove('hidden');
    }
  }

  leftArrow2.addEventListener('click', function () {
    tagGroup.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
  });

  rightArrow2.addEventListener('click', function () {
    tagGroup.scrollBy({ left: scrollAmount, behavior: 'smooth' });
  });

  // 在頁面加載和窗口調整大小時更新箭頭可行性
  window.addEventListener('resize', updateArrowVisibility);
  updateArrowVisibility(); // 初次加載時運行
});


//隱藏留言滾動條
document.addEventListener('DOMContentLoaded', function () {
  const containers = document.querySelectorAll('.other-store');

  containers.forEach(container => {
    if (container.scrollHeight > container.clientHeight) {
      container.style.overflowY = 'scroll'; // 顯示垂直滾動條
    } else {
      container.style.overflowY = 'auto'; // 隱藏垂直滾動條
    }
  });
});

/*留言按鈕文字狀態
 document.getElementById('toggleButton').addEventListener('click', function () {
   const button = this;
   const isExpanded = button.getAttribute('aria-expanded') === 'true';

   // 根據 aria-expanded 的狀態來改變文字
   if (isExpanded) {
     button.textContent = '收起';
   } else {
     button.textContent = '展開';
   }
 });*/