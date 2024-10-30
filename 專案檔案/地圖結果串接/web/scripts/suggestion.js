document.querySelectorAll('.suggestion-menu').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});
document.querySelectorAll('.suggestion-page').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});

window.addEventListener('load', function () {
  generateStoreSuggestion('tab-content-random');
});

document.addEventListener('DOMContentLoaded', function () {

  document.querySelectorAll('.nav-link').forEach(tab => {
    tab.addEventListener('click', function() {
      const targetTab = tab.getAttribute('data-tab');
      if (targetTab !== 'tab-content-random' && tab.classList.contains('active')) return;
      const targetContent = document.getElementById(targetTab);
      if (!targetContent) return;
      var regenerate = (targetContent.getElementsByClassName('carousel-container').length == 0);
      if (targetTab === 'tab-content-random') {
        document.getElementById('tab-button-random').innerHTML = '隨機推薦 <i class="fi fi-br-refresh" style="font-size:14px"></i>';
        if (tab.classList.contains('active')) regenerate = true;
      } else {
        document.getElementById('tab-button-random').innerText = '隨機推薦';
      }
      document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
      document.querySelectorAll('[class^="tab-content"]').forEach(content => content.classList.remove('active'));
      tab.classList.add('active');
      targetContent.classList.add('active');
      if (regenerate) generateStoreSuggestion(targetTab);
    });
  });

});

function generateStoreSuggestion(tabId) {
  var resultsContainer = document.getElementById(tabId);
  resultsContainer.innerHTML = `
    <div style="width:100%;height:550px;align-content:center;">
      <div class="rotating">
        <img src="./images/icon-loading.png" width="50" height="50">
      </div>
      <p style="text-align:center;">正在取得推薦餐廳...</p>
    </div>`;
  const formData = new FormData();
  formData.append('mode', tabId.replaceAll('tab-content-', ''));
  fetch('/struc/store-suggestion.php', {
    method: 'POST',
    credentials: 'same-origin',
    body: formData
  })
    .then(response => response.text())
    .then(data => {
      resultsContainer.innerHTML = data
      handleGrabContainer(resultsContainer);
    })
    .catch(() => { exceptionAlert('取得推薦餐廳'); });
}

function handleGrabContainer(resultsContainer) {
  const container = resultsContainer.querySelector('.grab-container');
  let isDown = false;
  let startX;
  let scrollLeft;
  let isDragging = false;
  const leftArrow = container.parentElement.querySelector('.left-arrow');
  const rightArrow = container.parentElement.querySelector('.right-arrow');
  const getScrollDistance = () => {
    const firstItem = container.querySelector(':scope > *');
    if (!firstItem) return 0;
    const itemStyle = window.getComputedStyle(firstItem);
    const itemWidth = firstItem.offsetWidth;
    const gapWidth = parseInt(itemStyle.marginRight) || 0;
    return (itemWidth * 4) + (gapWidth * 3);
  };
  const checkScrollability = () => {
    if (container.scrollWidth > container.clientWidth) {
      container.style.cursor = 'grab';
      leftArrow.style.display = 'block';
      rightArrow.style.display = 'block';
    } else {
      container.style.cursor = 'default';
      leftArrow.style.display = 'none';
      rightArrow.style.display = 'none';
    }
  };
  checkScrollability();
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
    container.scrollLeft = scrollLeft - walk*3;
  });
  leftArrow.addEventListener('click', () => {
    const scrollDistance = getScrollDistance();
    container.scrollBy({
      left: -scrollDistance,
      behavior: 'smooth'
    });
  });
  rightArrow.addEventListener('click', () => {
    const scrollDistance = getScrollDistance();
    container.scrollBy({
      left: scrollDistance,
      behavior: 'smooth'
    });
  });
  function checkArrowVisibility() {
    if (container.scrollLeft === 0) {
      leftArrow.style.display = 'none';
    } else {
      leftArrow.style.display = 'block';
    }
    if (container.scrollLeft + container.clientWidth >= container.scrollWidth) {
      rightArrow.style.display = 'none';
    } else {
      rightArrow.style.display = 'block';
    }
  }
  container.addEventListener('scroll', checkArrowVisibility);
  checkArrowVisibility();
}