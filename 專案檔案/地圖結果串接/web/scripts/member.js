document.querySelectorAll('.member-menu').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});
document.querySelectorAll('.member-page').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});


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

});

document.addEventListener('DOMContentLoaded', function() {

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

function editSettings() {
    const buttonGroups = document.querySelectorAll('.title_text');
    buttonGroups.forEach(group => {
        group.querySelector('.deselect_icon').style.display = 'inline';
    });

    document.getElementById('preference_save_button').style.display = 'inline';
    document.getElementById('preference_cancel_button').style.display = 'inline';
    document.getElementById('preference_edit_button').style.display = 'none';

    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        if (input.type === 'checkbox' || input.type === 'radio') {
            originalSettings[input.id] = input.checked;
            input.disabled = false;
        } else if (input.type === 'number' && input.readOnly) {
            originalSettings[input.id] = input.value;
            input.readOnly = false;
        }
    });
}

function saveSettings() {
  const buttonGroups = document.querySelectorAll('.title_text');
  buttonGroups.forEach(group => {
      group.querySelector('.select_icon').style.display = 'none';
      group.querySelector('.deselect_icon').style.display = 'none';
      group.querySelector('.mixed_icon').style.display = 'none';
  });
  document.getElementById('preference_save_button').style.display = 'none';
  document.getElementById('preference_cancel_button').style.display = 'none';
  document.getElementById('preference_edit_button').style.display = 'inline';
  const inputs = document.querySelectorAll('input');
  inputs.forEach(input => {
      if (input.type === 'checkbox' || input.type === 'radio') {
          input.disabled = true;
      } else if (input.type === 'number' && !input.readOnly) {
          input.readOnly = true;
      }
  });
  updatePreferences('member');
}

function cancelEdit() {
    const buttonGroups = document.querySelectorAll('.title_text');
    buttonGroups.forEach(group => {
        group.querySelector('.select_icon').style.display = 'none';
        group.querySelector('.deselect_icon').style.display = 'none';
        group.querySelector('.mixed_icon').style.display = 'none';
    });

    document.getElementById('preference_save_button').style.display = 'none';
    document.getElementById('preference_cancel_button').style.display = 'none';
    document.getElementById('preference_edit_button').style.display = 'inline';

    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        if (input.type === 'checkbox' || input.type === 'radio') {
            input.checked = originalSettings[input.id];
            input.disabled = true;
        } else if (input.type === 'number' && input.readOnly === false) {
            input.value = originalSettings[input.id];
            input.readOnly = true;
        }
    });
}


document.addEventListener('DOMContentLoaded', function() {
  // 全選、取消全選、混合圖示(個人需求)
  const selectAllIcon = document.getElementById('select_all_icon1');
  const deselectAllIcon = document.getElementById('deselect_all_icon1');
  const mixedIcon = document.getElementById('mixed_icon1');
  const checkboxes = document.querySelectorAll('.select_personal');
  selectAllIcon.style.display = 'none';
  deselectAllIcon.style.display = 'none';
  mixedIcon.style.display = 'none';
  function updateIcons() {
    const total = checkboxes.length;
    const checked = document.querySelectorAll('.select_personal:checked').length;
    if (checked === total) {
      selectAllIcon.style.display = 'inline';
      deselectAllIcon.style.display = 'none';
      mixedIcon.style.display = 'none';
    } else if (checked === 0) {
      selectAllIcon.style.display = 'none';
      deselectAllIcon.style.display = 'inline';
      mixedIcon.style.display = 'none';
    } else {
      selectAllIcon.style.display = 'none';
      deselectAllIcon.style.display = 'none';
      mixedIcon.style.display = 'inline';
    }
  }
  function selectAll() {
    checkboxes.forEach(checkbox => checkbox.checked = true);
    updateIcons();
  }
  function deselectAll() {
    checkboxes.forEach(checkbox => checkbox.checked = false);
    updateIcons();
  }
  selectAllIcon.addEventListener('click', deselectAll);
  deselectAllIcon.addEventListener('click', selectAll);
  mixedIcon.addEventListener('click', selectAll);
  checkboxes.forEach(checkbox => checkbox.addEventListener('change', updateIcons));
  updateIcons();
});

// 全選、取消全選、混合圖示(用餐方式)
document.addEventListener('DOMContentLoaded', function() {
  const selectAllIcon = document.getElementById('select_all_icon2');
  const deselectAllIcon = document.getElementById('deselect_all_icon2');
  const mixedIcon = document.getElementById('mixed_icon2');
  const checkboxes = document.querySelectorAll('.select_method');
  function updateIcons() {
    const total = checkboxes.length;
    const checked = document.querySelectorAll('.select_method:checked').length;
    if (checked === total) {
      selectAllIcon.style.display = 'inline';
      deselectAllIcon.style.display = 'none';
      mixedIcon.style.display = 'none';
    } else if (checked === 0) {
      selectAllIcon.style.display = 'none';
      deselectAllIcon.style.display = 'inline';
      mixedIcon.style.display = 'none';
    } else {
      selectAllIcon.style.display = 'none';
      deselectAllIcon.style.display = 'none';
      mixedIcon.style.display = 'inline';
    }
  }
  function selectAll() {
    checkboxes.forEach(checkbox => checkbox.checked = true);
    updateIcons();
  }
  function deselectAll() {
    checkboxes.forEach(checkbox => checkbox.checked = false);
    updateIcons();
  }
  selectAllIcon.addEventListener('click', deselectAll);
  deselectAllIcon.addEventListener('click', selectAll);
  mixedIcon.addEventListener('click', selectAll);
  checkboxes.forEach(checkbox => checkbox.addEventListener('change', updateIcons));
  updateIcons();
});

// 全選、取消全選、混合圖示(用餐時段)
document.addEventListener('DOMContentLoaded', function() {
  const selectAllIcon = document.getElementById('select_all_icon3');
  const deselectAllIcon = document.getElementById('deselect_all_icon3');
  const mixedIcon = document.getElementById('mixed_icon3');
  const checkboxes = document.querySelectorAll('.select_time');
  function updateIcons() {
    const total = checkboxes.length;
    const checked = document.querySelectorAll('.select_time:checked').length;
    if (checked === total) {
      selectAllIcon.style.display = 'inline';
      deselectAllIcon.style.display = 'none';
      mixedIcon.style.display = 'none';
    } else if (checked === 0) {
      selectAllIcon.style.display = 'none';
      deselectAllIcon.style.display = 'inline';
      mixedIcon.style.display = 'none';
    } else {
      selectAllIcon.style.display = 'none';
      deselectAllIcon.style.display = 'none';
      mixedIcon.style.display = 'inline';
    }
  }
  function selectAll() {
      checkboxes.forEach(checkbox => checkbox.checked = true);
      updateIcons();
  }
  function deselectAll() {
      checkboxes.forEach(checkbox => checkbox.checked = false);
      updateIcons();
  }
  selectAllIcon.addEventListener('click', deselectAll);
  deselectAllIcon.addEventListener('click', selectAll);
  mixedIcon.addEventListener('click', selectAll);
  checkboxes.forEach(checkbox => checkbox.addEventListener('change', updateIcons));
  updateIcons();
});

// 全選、取消全選、混合圖示(用餐氛圍)
document.addEventListener('DOMContentLoaded', function() {
  const selectAllIcon = document.getElementById('select_all_icon4');
  const deselectAllIcon = document.getElementById('deselect_all_icon4');
  const mixedIcon = document.getElementById('mixed_icon4');
  const checkboxes = document.querySelectorAll('.select_open_hour');
  function updateIcons() {
    const total = checkboxes.length;
    const checked = document.querySelectorAll('.select_open_hour:checked').length;
    if (checked === total) {
      selectAllIcon.style.display = 'inline';
      deselectAllIcon.style.display = 'none';
      mixedIcon.style.display = 'none';
    } else if (checked === 0) {
      selectAllIcon.style.display = 'none';
      deselectAllIcon.style.display = 'inline';
      mixedIcon.style.display = 'none';
    } else {
      selectAllIcon.style.display = 'none';
      deselectAllIcon.style.display = 'none';
      mixedIcon.style.display = 'inline';
    }
  }
  function selectAll() {
      checkboxes.forEach(checkbox => checkbox.checked = true);
      updateIcons();
  }
  function deselectAll() {
      checkboxes.forEach(checkbox => checkbox.checked = false);
      updateIcons();
  }
  selectAllIcon.addEventListener('click', deselectAll);
  deselectAllIcon.addEventListener('click', selectAll);
  mixedIcon.addEventListener('click', selectAll);
  checkboxes.forEach(checkbox => checkbox.addEventListener('change', updateIcons));
  updateIcons();
});

// 全選、取消全選、混合圖示(用餐規劃)
document.addEventListener('DOMContentLoaded', function() {
  const selectAllIcon = document.getElementById('select_all_icon5');
  const deselectAllIcon = document.getElementById('deselect_all_icon5');
  const mixedIcon = document.getElementById('mixed_icon5');
  const checkboxes = document.querySelectorAll('.select_plan');
  function updateIcons() {
    const total = checkboxes.length;
    const checked = document.querySelectorAll('.select_plan:checked').length;
    if (checked === total) {
      selectAllIcon.style.display = 'inline';
      deselectAllIcon.style.display = 'none';
      mixedIcon.style.display = 'none';
    } else if (checked === 0) {
      selectAllIcon.style.display = 'none';
      deselectAllIcon.style.display = 'inline';
      mixedIcon.style.display = 'none';
    } else {
      selectAllIcon.style.display = 'none';
      deselectAllIcon.style.display = 'none';
      mixedIcon.style.display = 'inline';
    }
  }
  function selectAll() {
    checkboxes.forEach(checkbox => checkbox.checked = true);
    updateIcons();
  }
  function deselectAll() {
    checkboxes.forEach(checkbox => checkbox.checked = false);
    updateIcons();
  }
  selectAllIcon.addEventListener('click', deselectAll);
  deselectAllIcon.addEventListener('click', selectAll);
  mixedIcon.addEventListener('click', selectAll);
  checkboxes.forEach(checkbox => checkbox.addEventListener('change', updateIcons));
  updateIcons();
});

// 全選、取消全選、混合圖示(基礎設施)
document.addEventListener('DOMContentLoaded', function() {
  const selectAllIcon = document.getElementById('select_all_icon6');
  const deselectAllIcon = document.getElementById('deselect_all_icon6');
  const mixedIcon = document.getElementById('mixed_icon6');
  const checkboxes = document.querySelectorAll('.select_facility');
  function updateIcons() {
    const total = checkboxes.length;
    const checked = document.querySelectorAll('.select_facility:checked').length;
    if (checked === total) {
      selectAllIcon.style.display = 'inline';
      deselectAllIcon.style.display = 'none';
      mixedIcon.style.display = 'none';
    } else if (checked === 0) {
      selectAllIcon.style.display = 'none';
      deselectAllIcon.style.display = 'inline';
      mixedIcon.style.display = 'none';
    } else {
      selectAllIcon.style.display = 'none';
      deselectAllIcon.style.display = 'none';
      mixedIcon.style.display = 'inline';
    }
  }
  function selectAll() {
    checkboxes.forEach(checkbox => checkbox.checked = true);
    updateIcons();
  }
  function deselectAll() {
    checkboxes.forEach(checkbox => checkbox.checked = false);
    updateIcons();
  }
  selectAllIcon.addEventListener('click', deselectAll);
  deselectAllIcon.addEventListener('click', selectAll);
  mixedIcon.addEventListener('click', selectAll);
  checkboxes.forEach(checkbox => checkbox.addEventListener('change', updateIcons));
  updateIcons();
});

// 全選、取消全選、混合圖示(付款方式)
document.addEventListener('DOMContentLoaded', function() {
  const selectAllIcon = document.getElementById('select_all_icon7');
  const deselectAllIcon = document.getElementById('deselect_all_icon7');
  const mixedIcon = document.getElementById('mixed_icon7');
  const checkboxes = document.querySelectorAll('.select_payment');
  function updateIcons() {
    const total = checkboxes.length;
    const checked = document.querySelectorAll('.select_payment:checked').length;
    if (checked === total) {
      selectAllIcon.style.display = 'inline';
      deselectAllIcon.style.display = 'none';
      mixedIcon.style.display = 'none';
    } else if (checked === 0) {
      selectAllIcon.style.display = 'none';
      deselectAllIcon.style.display = 'inline';
      mixedIcon.style.display = 'none';
    } else {
      selectAllIcon.style.display = 'none';
      deselectAllIcon.style.display = 'none';
      mixedIcon.style.display = 'inline';
    }
  }
  function selectAll() {
      checkboxes.forEach(checkbox => checkbox.checked = true);
      updateIcons();
  }
  function deselectAll() {
      checkboxes.forEach(checkbox => checkbox.checked = false);
      updateIcons();
  }
  selectAllIcon.addEventListener('click', deselectAll);
  deselectAllIcon.addEventListener('click', selectAll);
  mixedIcon.addEventListener('click', selectAll);
  checkboxes.forEach(checkbox => checkbox.addEventListener('change', updateIcons));
  updateIcons();
});

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
      document.getElementById(slider.id + '_value').innerText = initialValues[slider.id]; // 更新顯示的值
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