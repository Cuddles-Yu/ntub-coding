document.querySelectorAll('.member-menu').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});
document.querySelectorAll('.member-page').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
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
}

/* 修改使用者名稱 */
document.addEventListener('DOMContentLoaded', function() {
  const input = document.getElementById('user_name');
  const changeIcon = document.getElementById('change_user_name');
  const doneIcon = document.getElementById('done_user_name');
  const cancelIcon = document.getElementById('cancel_user_name');
  let previousValue = input.value;
  changeIcon.addEventListener('click', function() {
      previousValue = input.value;
      input.removeAttribute('readonly');
      changeIcon.style.display = 'none';
      doneIcon.style.display = 'inline';
      cancelIcon.style.display = 'inline';
  });
  doneIcon.addEventListener('click', function() {
      input.setAttribute('readonly', 'readonly');
      changeIcon.style.display = 'inline';
      doneIcon.style.display = 'none';
      cancelIcon.style.display = 'none';
  });
  cancelIcon.addEventListener('click', function() {
      input.value = previousValue;
      input.setAttribute('readonly', 'readonly');
      changeIcon.style.display = 'inline';
      doneIcon.style.display = 'none';
      cancelIcon.style.display = 'none';
  });
});


/* 控制密碼小視窗的顯示和隱藏 */
document.getElementById('change_password').addEventListener('click', function() {
    document.getElementById('password_modal').style.display = 'block';
});

function closeModal() {
    document.getElementById('password_modal').style.display = 'none';
}

// 點擊密碼小視窗外部區域時關閉密碼小視窗
window.onclick = function(event) {
    if (event.target == document.getElementById('password_modal')) {
        document.getElementById('password_modal').style.display = 'none';
    }
}

// 新密碼設定提交時進行檢查
document.getElementById('password_form').addEventListener('submit', function(event) {
    event.preventDefault();
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    if (newPassword === confirmPassword) {
        if (currentPassword !== newPassword) {
            document.getElementById('success_message').style.display = 'block';
            setTimeout(closeModal, 2500);
        } else {
            alert('新密碼不能與目前密碼相同，請重新輸入。');
        }
    } else {
        alert('新密碼和確認密碼不一致，請重新輸入。');
    }
});

// 強制設置圖示隱藏
window.onload = function() {
    document.querySelectorAll('.select_icon').forEach(function(element) {
        element.style.display = 'none';
    });
    document.querySelectorAll('.deselect_icon').forEach(function(element) {
        element.style.display = 'none';
    });
    document.querySelectorAll('.mixed_icon').forEach(function(element) {
        element.style.display = 'none';
    });

    // 頁面載入後檢查 URL 中是否有搜尋關鍵字，並自動執行搜尋
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab) {
      window.history.replaceState({}, '', `${location.protocol}//${location.host}${location.pathname}`);
      switchTo(tab);
    }   
};

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
    const lowerScoreInput = document.getElementById('lower_score');
    let lowerScoreValue = parseInt(lowerScoreInput.value, 10);

    // 檢查分數是否在合理範圍(0-100)內
    if (lowerScoreValue > 100) {
        lowerScoreValue = 100;
    } else if (lowerScoreValue < 0) {
        lowerScoreValue = 0;
    }
    lowerScoreInput.value = lowerScoreValue;

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

// 全選、取消全選、混合圖示(個人需求)
document.addEventListener('DOMContentLoaded', function() {
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

    // 初始化圖示狀態
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

    // 初始化圖示狀態
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

    // 初始化圖示狀態
    updateIcons();
});

// 全選、取消全選、混合圖示(用餐氛圍)
document.addEventListener('DOMContentLoaded', function() {
    const selectAllIcon = document.getElementById('select_all_icon4');
    const deselectAllIcon = document.getElementById('deselect_all_icon4');
    const mixedIcon = document.getElementById('mixed_icon4');
    const checkboxes = document.querySelectorAll('.select_atmosphere');

    function updateIcons() {
        const total = checkboxes.length;
        const checked = document.querySelectorAll('.select_atmosphere:checked').length;

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

    // 初始化圖示狀態
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

    // 初始化圖示狀態
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

    // 初始化圖示狀態
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

    // 初始化圖示狀態
    updateIcons();
});

/* 切換編輯模式 */
var initialValues = {};

function toggleEditMode2() {
    var sliders = document.querySelectorAll('input[type="range"]');
    var editButton2 = document.getElementById('weight_edit_button');
    var cancelButton2 = document.getElementById('weight_cancel_button');

    if (editButton2.innerText === '修改') {
        sliders.forEach(function(slider) {
            initialValues[slider.id] = slider.value; // 保存初始值
            slider.disabled = false;
        });
        editButton2.innerText = '完成';
        cancelButton2.style.display = 'inline';
    } else {
        sliders.forEach(function(slider) {
            slider.disabled = true;
        });
        editButton2.innerText = '修改';
        cancelButton2.style.display = 'none';
    }
}

function cancelEditMode2() {
    var sliders = document.querySelectorAll('input[type="range"]');
    var editButton2 = document.getElementById('weight_edit_button');
    var cancelButton2 = document.getElementById('weight_cancel_button');

    sliders.forEach(function(slider) {
        slider.disabled = true;
        slider.value = initialValues[slider.id]; // 恢復到初始值
        document.getElementById(slider.id + '_value').innerText = initialValues[slider.id] + '%'; // 更新顯示的值
    });
    editButton2.innerText = '修改';
    cancelButton2.style.display = 'none';
}

/* 權重 %數值更新 */
function updateLabelValue(id) {
    var slider = document.getElementById(id);
    var output = document.getElementById(id + '_value');
    output.textContent = slider.value;
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