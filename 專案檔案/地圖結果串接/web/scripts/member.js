document.querySelectorAll('.member-menu').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});
document.querySelectorAll('.member-page').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});

addRadioChangeListener('member');

window.addEventListener('load', function () {

  // 預設顯示的頁面
  const urlParams = new URLSearchParams(window.location.search);
  const tab = urlParams.get('tab');
  if (tab) switchTo(tab);

  // 強制設置圖示隱藏
  document.querySelectorAll('.select_icon').forEach(function(element) {
      element.style.display = 'none';
  });
  document.querySelectorAll('.deselect_icon').forEach(function(element) {
      element.style.display = 'none';
  });
  document.querySelectorAll('.mixed_icon').forEach(function(element) {
      element.style.display = 'none';
  });

  updateArea('member');
  checkFavorite();
});


function switchTo(page) {
  // 主要區域
  document.getElementById("info_main_area").style.display = (page=='info') ? "block" : "none";
  document.getElementById("preference_main_area").style.display = (page=='preference') ? "block" : "none";
  document.getElementById("weight_main_area").style.display = (page=='weight') ? "block" : "none";
  document.getElementById("favorite_main_area").style.display = (page=='favorite') ? "block" : "none";
  // 選單標籤
  document.getElementById("info").style.color = (page=='info') ? "#8234FF" : "#5e5e5e";
  document.getElementById("info_logo").style.color = (page=='info') ? "#8234FF" : "#5e5e5e";
  document.getElementById("preference").style.color = (page=='preference') ? "#8234FF" : "#5e5e5e";
  document.getElementById("preference_logo").style.color = (page=='preference') ? "#8234FF" : "#5e5e5e";
  document.getElementById("weight").style.color = (page=='weight') ? "#8234FF" : "#5e5e5e";
  document.getElementById("weight_logo").style.color = (page=='weight') ? "#8234FF" : "#5e5e5e";
  document.getElementById("favorite").style.color = (page=='favorite') ? "#8234FF" : "#5e5e5e";
  document.getElementById("favorite_logo").style.color = (page=='favorite') ? "#8234FF" : "#5e5e5e";
  window.history.replaceState({}, '', `${location.protocol}//${location.host}${location.pathname}?tab=${page}`);
}

// 修改偏好設定按鈕
let originalSettings = {};
var editing = false;

function editPreference() {
    editing = true;
    document.getElementById('preference_save_button').style.display = 'inline';
    document.getElementById('preference_cancel_button').style.display = 'inline';
    document.getElementById('preference_edit_button').style.display = 'none';
    document.querySelectorAll('.checkbox').forEach(input => {
      originalSettings[input.id] = input.checked;
      input.disabled = false;
    });
    document.querySelectorAll('.field').forEach(input => {
      originalSettings[input.id] = input.value;
      input.disabled = false;
    });
    radioToggle('member', document.getElementById('member-distance-radio').checked?'distance':'geo');
}
function savePreference() {
  editing = false;
  hasChanged = false;
  if (!hasChanged) hasChanged = [...document.querySelectorAll('.checkbox')].some(input => input.checked != originalSettings[input.id]);
  if (!hasChanged) hasChanged = [...document.querySelectorAll('.field')].some(input => input.value != originalSettings[input.id]);
  document.getElementById('preference_save_button').style.display = 'none';
  document.getElementById('preference_cancel_button').style.display = 'none';
  document.getElementById('preference_edit_button').style.display = 'inline';
  document.querySelectorAll('.field').forEach(input => { input.disabled = true; });
  document.querySelectorAll('.checkbox').forEach(input => { input.disabled = true; });
  if (hasChanged) {
    updatePreferences('member');
    radioToggle('member', document.getElementById('member-distance-radio').checked?'distance':'geo');
  }
}
function restorePreference() {
  editing = false;
  document.getElementById('preference_save_button').style.display = 'none';
  document.getElementById('preference_cancel_button').style.display = 'none';
  document.getElementById('preference_edit_button').style.display = 'inline';
  document.querySelectorAll('.checkbox').forEach(input => {
    input.checked = originalSettings[input.id];
    input.disabled = true;
  });
  document.querySelectorAll('.field').forEach(input => {
    input.value = originalSettings[input.id];
    input.disabled = true;
  });
  radioToggle('member', document.getElementById('member-distance-radio').checked?'distance':'geo');
}

/* 切換編輯模式 */
var initialValues = {};
function saveWeight() {
  const sliders = document.querySelectorAll('#weight_main_area input[type="range"]');
  const editButton = document.getElementById('weight_edit_button');
  const saveButton = document.getElementById('weight_save_button');
  const cancelButton = document.getElementById('weight_cancel_button');
  const atmosphere = document.getElementById('atmosphere').value;
  const product = document.getElementById('product').value;
  const service = document.getElementById('service').value;
  const price = document.getElementById('price').value;
  hasChanged = [...sliders].some(slider => initialValues[slider.id] != slider.value);
  editButton.style.display = 'inline';
  saveButton.style.display = 'none';
  cancelButton.style.display = 'none';
  sliders.forEach(function(slider) {
    slider.disabled = true;
  });
  if (atmosphere+product+service+price == 0) {
    showAlert('red', '更新失敗，至少要有一項權重值 > 0');
    restoreWeight();
    return;
  }
  if (hasChanged) {
    const formData = new FormData();
    formData.set('atmosphere', atmosphere);
    formData.set('product', product);
    formData.set('service', service);
    formData.set('price', price);
    fetch('/member/handler/update-weight.php', {
      method: 'POST',
      credentials: 'same-origin',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showAlert('green', data.message);
      } else {
        showAlert('red', data.message);
      }
    })
    .catch(() => {showAlert('red', '更新權重過程中發生非預期的錯誤');})
  }
}
function editWeight() {
  const sliders = document.querySelectorAll('#weight_main_area input[type="range"]');
  const editButton = document.getElementById('weight_edit_button');
  const saveButton = document.getElementById('weight_save_button');
  const cancelButton = document.getElementById('weight_cancel_button');
  sliders.forEach(function(slider) {
    initialValues[slider.id] = slider.value;
    slider.disabled = false;
  });
  editButton.style.display = 'none';
  saveButton.style.display = 'inline';
  cancelButton.style.display = 'inline';
}
function restoreWeight() {
  const sliders = document.querySelectorAll('#weight_main_area input[type="range"]');
  const editButton = document.getElementById('weight_edit_button');
  const saveButton = document.getElementById('weight_save_button');
  const cancelButton = document.getElementById('weight_cancel_button');
  sliders.forEach(function(slider) {
      slider.disabled = true;
      slider.value = initialValues[slider.id];
      document.getElementById(slider.id + '-value').innerText = initialValues[slider.id];
  });
  editButton.style.display = 'inline';
  saveButton.style.display = 'none';
  cancelButton.style.display = 'none';
}

/* 箭頭圖示切換 */
document.addEventListener('DOMContentLoaded', function() {
    // 獲取所有向下箭頭和向上箭頭的元素
    var downArrows = document.querySelectorAll('.fi-sr-arrow-down');
    var upArrows = document.querySelectorAll('.fi-sr-arrow-up');

    // 為每個向下箭頭添加點擊事件
    downArrows.forEach(function(downArrow) {
        downArrow.addEventListener('click', function() {
            var id = downArrow.id.replace('down', 'up');
            var upArrow = document.getElementById(id);
            downArrow.style.display = 'none';
            upArrow.style.display = 'inline';
        });
    });

    // 為每個向上箭頭添加點擊事件
    upArrows.forEach(function(upArrow) {
        upArrow.addEventListener('click', function() {
            var id = upArrow.id.replace('up', 'down');
            var downArrow = document.getElementById(id);
            upArrow.style.display = 'none';
            downArrow.style.display = 'inline';
        });
    });
});


function handleRowClick() {
  console.log("Row clicked");
}

function shareFavorite(link) {
  const shareData = {
    title: '分享餐廳連結',
    text: `${document.getElementById('user_name').value}邀請你一起來看看所收藏的餐廳！`,
    url: link,
  };
  if (navigator.share) {
    navigator.share(shareData).then(() => {
    }).catch(() => {
      showAlert('red', '分享過程中發生錯誤，請稍後再試');
    });
  } else {
    navigator.clipboard.writeText(shareData.url).then(function() {
      showAlert('orange', '此瀏覽器不支援分享功能，已自動複製連結');
    }).catch(() => {
      showAlert('red', '複製連結失敗，請稍後再試');
    });
  }
}

function checkFavorite() {
  const container = document.querySelector('.content_row_container');
  if (document.querySelectorAll('.content_row').length == 0) {
    container.innerHTML = '<div style="height:300px;align-content:center;text-align:center;"><p style="font-size:18px">尚未收藏餐廳，前往<a href="/home">餐廳搜尋</a>或<a href="/suggestion">餐廳推薦</a>開始收藏吧！</p></div>';
  }
}