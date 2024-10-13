var storeId = new URLSearchParams(window.location.search).get('id')??null;

document.querySelectorAll('.home-menu').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});
document.querySelectorAll('.home-page').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});

window.addEventListener('load', function () {
  if (storeId) {
    // generateMark();
    searchCommentsByKeyword();
  }
});

document.addEventListener('DOMContentLoaded', function () {
  document.title = `${document.getElementById('store-title').textContent.trim()}詳細資訊 - 評星宇宙`;
  //手動滑鼠滾動(推薦餐點)
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
      const walk = (x - startX) * 5;
      CardGroups.scrollLeft = scrollLeft - walk;
    });
  });
  // 初始化所有擁有 'status' class 的 popover
  var popovers = [].slice.call(document.querySelectorAll('.status'));
  popovers.forEach(function (popoverEl) {
    var popover = new bootstrap.Popover(popoverEl, {
      placement: 'bottom',
      fallbackPlacements: []
    });
    document.addEventListener('click', function (e) {
      if (!popoverEl.contains(e.target) && !document.querySelector('.popover')?.contains(e.target)) popover.hide();
    });
  });
  // 清除沒有子元素的服務項目並重新排序
  const itemGroups = document.querySelectorAll('.item-group');
  itemGroups.forEach(function (itemGroup) {
    if (itemGroup.children.length === 0) {
      const serviceGroup = itemGroup.closest('.service-group');
      const matchedGroup = itemGroup.closest('.matched-services');
      if (serviceGroup) {
        serviceGroup.remove();
      } else if (matchedGroup) {
        matchedGroup.remove();
      } 
    } else {
      const serviceItems = Array.from(itemGroup.querySelectorAll('.service-item'));
      const crossItems = serviceItems.filter(item => item.querySelector('.fi-sr-cross'));
      const otherItems = serviceItems.filter(item => !item.querySelector('.fi-sr-cross'));
      itemGroup.innerHTML = '';
      otherItems.forEach(item => itemGroup.appendChild(item));
      crossItems.forEach(item => itemGroup.appendChild(item));
    }
  });
  //樣本選擇與排序關係
  const btnRadio1 = document.getElementById('btnradio1');
  const btnRadio2 = document.getElementById('btnradio2');
  const btnRadio3 = document.getElementById('btnradio3');
  const btnRadio4 = document.getElementById('btnradio4');
  const sortSelect = document.querySelector('.sort-button .form-select');
  btnRadio3.addEventListener('change', function () {
    if (btnRadio3.checked) {
      sortSelect.value = "由高至低";
      reorderComments();
    }
  });
  btnRadio4.addEventListener('change', function () {
    if (btnRadio4.checked) {
      sortSelect.value = "由低至高";
      reorderComments();
    }
  });
  btnRadio1.addEventListener('change', function () {
    if (btnRadio1.checked) {
      sortSelect.value = "相關性";
      reorderComments();
    }
  });
  btnRadio2.addEventListener('change', function () {
    if (btnRadio2.checked) {
      sortSelect.value = "相關性";
      reorderComments();
    }
  });
  //水平滾動箭頭控制(推薦餐點)
  const recommendCard = document.querySelector('.group-card');
  if (recommendCard) {
    const recommendLeftArrow = document.querySelector('.left-arrow');
    const recommendRightArrow = document.querySelector('.right-arrow');
    const recommendScrollAmount = 500;  
    recommendLeftArrow.addEventListener('click', function () {
      recommendCard.scrollBy({ left: -recommendScrollAmount, behavior: 'smooth' });
    });
    recommendRightArrow.addEventListener('click', function () {
      recommendCard.scrollBy({ left: recommendScrollAmount, behavior: 'smooth' });
    });
    window.addEventListener('resize', updateArrowVisibility);
    updateArrowVisibility(recommendCard, recommendLeftArrow, recommendRightArrow);
  }  
  //水平滾動箭頭控制(最多人提到)
  const keywordGroup = document.querySelector('.tag-group');
  const keywordLeftArrow = document.querySelector('.left-arrow-2');
  const keywordRightArrow = document.querySelector('.right-arrow-2');
  const keywordScrollAmount = 400;
  keywordLeftArrow.addEventListener('click', function () {
    keywordGroup.scrollBy({ left: -keywordScrollAmount, behavior: 'smooth' });
  });
  keywordRightArrow.addEventListener('click', function () {
    keywordGroup.scrollBy({ left: keywordScrollAmount, behavior: 'smooth' });
  });
  window.addEventListener('resize', updateArrowVisibility);
  updateArrowVisibility(keywordGroup, keywordLeftArrow, keywordRightArrow);
  //隱藏留言滾動條
  const containers = document.querySelectorAll('.other-store');
  containers.forEach(container => {
    if (container.scrollHeight > container.clientHeight) {
      container.style.overflowY = 'scroll';
    } else {
      container.style.overflowY = 'auto';
    }
  });
});

function updateArrowVisibility(container, leftArrow, rightArrow) {
  const scrollWidth = container.scrollWidth;
  const clientWidth = container.clientWidth;
  if (scrollWidth <= clientWidth) {
    if (leftArrow) leftArrow.classList.add('hidden');
    if (leftArrow) rightArrow.classList.add('hidden');
  } else {
    if (leftArrow) leftArrow.classList.remove('hidden');
    if (leftArrow) rightArrow.classList.remove('hidden');
  }
}

function clearSearchKeyword() {
  document.getElementById('commentKeyword').value = '';
  searchCommentsByKeyword();
}

function resetCommentSearch() {
  document.getElementById('commentGroup').scrollTo(0, 0)
  document.getElementById('inputGroup-sizing-default').setAttribute('style', '');
  document.getElementById('commentKeyword').setAttribute('style', '');
  document.getElementById('search-button').setAttribute('style', 'border-color:lightgray;');
  document.getElementById('clear-button').setAttribute('style', 'border-color:lightgray;color:gray;');
  document.getElementById('reset-comment-search').setAttribute('style', '');    
  document.getElementById('keyword-title').scrollIntoView({ behavior: 'smooth', block: 'start' });
  searchCommentsByKeyword();
}

function searchCommentsByTarget(button) {
  console.log(storeId);
  const target = button.querySelector('.object').textContent;  
  const commentCountTitle = document.getElementById('comment-count-title');
  document.getElementById('commentGroup').scrollTo(0, 0)
  document.getElementById('commentGroup').setAttribute('keyword', -1);
  document.getElementById('inputGroup-sizing-default').setAttribute('style', 'display:none;');
  document.getElementById('commentKeyword').setAttribute('style', 'display:none;');
  document.getElementById('search-button').setAttribute('style', 'display:none;');
  document.getElementById('clear-button').setAttribute('style', 'display:none;');
  document.getElementById('reset-comment-search').setAttribute('style', 'display:block;');    
  document.getElementById('comment-title').scrollIntoView({ behavior: 'smooth', block: 'start' });

  const formData = new FormData();
  formData.set('id', storeId);
  formData.set('q', target);
  formData.set('target', document.getElementById('filterSelect').value);
  formData.set('type', button.classList[0]);
  
  fetch('struc/comment-target.php', {
      method: 'POST',
      credentials: 'same-origin',
      body: formData
  })
  .then(response => response.json())
  .then(data => {
      commentGroup.innerHTML = data.html;
      commentCountTitle.textContent = `${data.type}關鍵字搜尋結果 ${data.count} 則 | ${target}`;
      reorderComments();
  })
  .catch(() => {showAlert('red', '發送請求時發生非預期的錯誤');});
}

function generateMark() {
  const searchTerm = document.getElementById('filterSelect').value;
  
  const formData = new FormData();
  formData.set('id', storeId);
  formData.set('target', searchTerm);
  
  fetch('struc/mark-state.php', {
      method: 'POST',
      credentials: 'same-origin',
      body: formData
  })
  .then(response => response.json())
  .then(data => {
      document.getElementById('group-keyword-good').innerHTML = data.good;
      document.getElementById('group-keyword-bad').innerHTML = data.bad;
      document.getElementById('group-keyword-middle').innerHTML = data.middle;
  })
  .catch(() => {showAlert('red', '發送請求時發生非預期的錯誤');});
}

function searchCommentsByKeyword() {
  const searchTerm = document.getElementById('commentKeyword').value.trim();
  const commentGroup = document.getElementById('commentGroup');
  const commentCountTitle = document.getElementById('comment-count-title');
  if (commentGroup.getAttribute('keyword') === searchTerm) return;
  
  const formData = new FormData();
  formData.set('id', storeId);
  formData.set('q', searchTerm);
  
  fetch('struc/comment-keyword.php', {
      method: 'POST',
      credentials: 'same-origin',
      body: formData
  })
  .then(response => response.json())
  .then(data => {
      commentGroup.setAttribute('keyword', searchTerm);
      commentGroup.innerHTML = data.html;
      if (searchTerm === '') {
          commentCountTitle.textContent = '留言 ' + data.count + ' 則';
      } else {
          commentCountTitle.textContent = '留言搜尋結果 ' + data.count + ' 則';
      }
      reorderComments();
  })
  .catch(() => {showAlert('red', '發送請求時發生非預期的錯誤');});
}

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

document.getElementById('commentKeyword').addEventListener('keydown', function(event) {
  if (event.key === 'Enter') {
    event.preventDefault(); // 防止表單的預設提交行為
    document.getElementById('search-button').click(); // 觸發按鈕點擊事件
  }
});

function reorderComments() {
  const sortValue = document.getElementById('sortSelect').value;
  const commentGroup = document.getElementById('commentGroup');
  const comments = Array.from(commentGroup.getElementsByClassName('comment-item'));
  comments.sort((a, b) => {
    const ratingA = parseInt(a.getAttribute('data-rating'));
    const ratingB = parseInt(b.getAttribute('data-rating'));
    const indexA = parseInt(a.getAttribute('data-index'));
    const indexB = parseInt(b.getAttribute('data-index'));
    if (sortValue === '由高至低') {
      return ratingB - ratingA;
    } else if (sortValue === '由低至高') {
      return ratingA - ratingB;
    } else {
      return indexA - indexB;
    }
  });
  comments.forEach(comment => commentGroup.appendChild(comment));
}

document.getElementById('sortSelect').addEventListener('change', function() {
  reorderComments();
  document.getElementById('commentGroup').scrollTo(0, 0);
});

document.getElementById('filterSelect').addEventListener('change', function() {  
  resetCommentSearch();
});

function navigateToStore(storeLat, storeLng) {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var userLat = position.coords.latitude;
      var userLng = position.coords.longitude;
      var googleMapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${storeLat},${storeLng}`;
      window.open(googleMapsUrl, '_blank');
    }, function(error) {
      alert('無法取得您的位置: ' + error.message);
    });
  } else {
    alert('您的瀏覽器不支援地理定位功能。');
  }
}