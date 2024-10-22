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
    // const buttonGroups = document.querySelectorAll('.title_text');
    // buttonGroups.forEach(group => {
    //     group.querySelector('.deselect_icon').style.display = 'inline';
    // });
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
  // const buttonGroups = document.querySelectorAll('.title_text');
  // buttonGroups.forEach(group => {
  //     group.querySelector('.select_icon').style.display = 'none';
  //     group.querySelector('.deselect_icon').style.display = 'none';
  //     group.querySelector('.mixed_icon').style.display = 'none';
  // });
  editing = false;
  document.getElementById('preference_save_button').style.display = 'none';
  document.getElementById('preference_cancel_button').style.display = 'none';
  document.getElementById('preference_edit_button').style.display = 'inline';
  document.querySelectorAll('.field').forEach(input => { input.disabled = true; });
  document.querySelectorAll('.checkbox').forEach(input => { input.disabled = true; });
  updatePreferences('member');

  radioToggle('member', document.getElementById('member-distance-radio').checked?'distance':'geo');
}

function cancelEdit() {
  // const buttonGroups = document.querySelectorAll('.title_text');
  // buttonGroups.forEach(group => {
  //     group.querySelector('.select_icon').style.display = 'none';
  //     group.querySelector('.deselect_icon').style.display = 'none';
  //     group.querySelector('.mixed_icon').style.display = 'none';
  // });
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

  sliders.forEach(function(slider) {
    slider.disabled = true;
  });
  editButton.style.display = 'inline';
  saveButton.style.display = 'none';
  cancelButton.style.display = 'none';  

  if (atmosphere+product+service+price == 0) {
    showAlert('red', '更新失敗，至少要有一項權重值 > 0');
    cancelEditMode2();
    return;
  }

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

function toggleEditMode2() {
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

function cancelEditMode2() {
  const sliders = document.querySelectorAll('#weight_main_area input[type="range"]');
  const editButton = document.getElementById('weight_edit_button');
  const saveButton = document.getElementById('weight_save_button');
  const cancelButton = document.getElementById('weight_cancel_button');
  sliders.forEach(function(slider) {
      slider.disabled = true;
      slider.value = initialValues[slider.id]; // 恢復到初始值
      console.log(slider.id);
      document.getElementById(slider.id + '-value').innerText = initialValues[slider.id]; // 更新顯示的值
  });
  editButton.style.display = 'inline';
  saveButton.style.display = 'none';
  cancelButton.style.display = 'none';
}

/* 權重 %數值更新 */
function updateLabelValue(id) {
    var slider = document.getElementById(id);
    var output = document.getElementById(id + '_value');
    output.textContent = slider.value;
    slider.setAttribute('value', slider.value);
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