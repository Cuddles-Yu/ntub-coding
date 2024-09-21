/* 點擊漢堡圖示時，顯示/隱藏選單 */
document.getElementById('hamburger_btn').addEventListener('click', function() {
    var navMenu = document.getElementById('nav_menu2');
    var overlay = document.getElementById('overlay');
    if (navMenu.classList.contains('show')) {
        navMenu.classList.remove('show');
        overlay.classList.remove('show');
    } else {
        navMenu.classList.add('show');
        overlay.classList.add('show');
    }
});

document.getElementById('overlay').addEventListener('click', function() {
    var navMenu = document.getElementById('nav_menu2');
    var overlay = document.getElementById('overlay');
    navMenu.classList.remove('show');
    overlay.classList.remove('show');
});

/* 切換到基本資料頁面 */
function switchToInfo() {  
    // 先取得要操作的標籤
    let info_main_area_Div = document.getElementById("info_main_area");
    let preference_main_area_Div = document.getElementById("preference_main_area");
    let weight_main_area_Div = document.getElementById("weight_main_area");
    let place_main_area_Div = document.getElementById("place_main_area");

     // 再對標籤進行操作
    info_main_area_Div.style.display = "block";
    preference_main_area_Div.style.display = "none";
    weight_main_area_Div.style.display = "none";
    place_main_area_Div.style.display = "none";
    
    // 取得頁面的標籤、設定顏色
    let infoDiv = document.getElementById("info");
    let preferenceDiv = document.getElementById("preference");
    let weightDiv = document.getElementById("weight");
    let placeDiv = document.getElementById("place");
    infoDiv.style.color = "#8234FF";
    preferenceDiv.style.color = "#5e5e5e";
    weightDiv.style.color = "#5e5e5e";
    placeDiv.style.color = "#5e5e5e";
}

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
    event.preventDefault(); // 防止表單提交

    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    // 檢查新密碼和確認密碼是否一致
    if (newPassword === confirmPassword) {
        // 檢查目前密碼和新密碼是否不同
        if (currentPassword !== newPassword) {
            // 顯示成功訊息
            document.getElementById('success_message').style.display = 'block';

            // 隱藏模態視窗
            setTimeout(closeModal, 2500); // 2.5秒後關閉模態視窗
        } else {
            alert('新密碼不能與目前密碼相同，請重新輸入。');
        }
    } else {
        alert('新密碼和確認密碼不一致，請重新輸入。');
    }
});

/* 切換到偏好設定頁面 */
function switchToPreference() {  
    // 先取得要操作的標籤
    let info_main_area_Div = document.getElementById("info_main_area");
    let preference_main_area_Div = document.getElementById("preference_main_area");
    let weight_main_area_Div = document.getElementById("weight_main_area");
    let place_main_area_Div = document.getElementById("place_main_area");

     // 再對標籤進行操作
    info_main_area_Div.style.display = "none";
    preference_main_area_Div.style.display = "block";
    weight_main_area_Div.style.display = "none";
    place_main_area_Div.style.display = "none";
    
    // 取得頁面的標籤、設定顏色
    let infoDiv = document.getElementById("info");
    let preferenceDiv = document.getElementById("preference");
    let weightDiv = document.getElementById("weight");
    let placeDiv = document.getElementById("place");
    infoDiv.style.color = "#5e5e5e";
    preferenceDiv.style.color = "#8234FF";
    weightDiv.style.color = "#5e5e5e";
    placeDiv.style.color = "#5e5e5e";
}

// 修改偏好設定按鈕
let originalSettings = {};

function editSettings() {
    const buttonGroups = document.querySelectorAll('.title_text');
    buttonGroups.forEach(group => {
        group.querySelector('.select_all_button').style.display = 'inline';
        group.querySelector('.deselect_all_button').style.display = 'inline';
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
        group.querySelector('.select_all_button').style.display = 'none';
        group.querySelector('.deselect_all_button').style.display = 'none';
    });

    document.getElementById('preference_save_button').style.display = 'none';
    document.getElementById('preference_cancel_button').style.display = 'none';
    document.getElementById('preference_edit_button').style.display = 'inline';

    // Disable all inputs
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
        group.querySelector('.select_all_button').style.display = 'none';
        group.querySelector('.deselect_all_button').style.display = 'none';
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

// 未按下完成修改時，回到原本狀態

// 偏好設定全選/取消全選按鈕
function selectAllPersonal(event) {
    event.preventDefault();
    const checkboxes = document.querySelectorAll('.select_personal');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllPersonal(event) {
    event.preventDefault();
    const checkboxes = document.querySelectorAll('.select_personal');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

function selectAllMethod(event) {
    event.preventDefault();
    const checkboxes = document.querySelectorAll('.select_method');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllMethod(event) {
    event.preventDefault();
    const checkboxes = document.querySelectorAll('.select_method');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

function selectAllTime(event) {
    event.preventDefault();
    const checkboxes = document.querySelectorAll('.select_time');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllTime(event) {
    event.preventDefault();
    const checkboxes = document.querySelectorAll('.select_time');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

function selectAllAtmosphere(event) {
    event.preventDefault();
    const checkboxes = document.querySelectorAll('.select_atmosphere');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllAtmosphere(event) {
    event.preventDefault();
    const checkboxes = document.querySelectorAll('.select_atmosphere');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

function selectAllPlan(event) {
    event.preventDefault();
    const checkboxes = document.querySelectorAll('.select_plan');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllPlan(event) {
    event.preventDefault();
    const checkboxes = document.querySelectorAll('.select_plan');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

function selectAllFacility(event) {
    event.preventDefault();
    const checkboxes = document.querySelectorAll('.select_facility');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllFacility(event) {
    event.preventDefault();
    const checkboxes = document.querySelectorAll('.select_facility');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

function selectAllPayment(event) {
    event.preventDefault();
    const checkboxes = document.querySelectorAll('.select_payment');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllPayment(event) {
    event.preventDefault();
    const checkboxes = document.querySelectorAll('.select_payment');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

/* 切換到權重設定頁面 */
function switchToWeight() {
    // 先取得要操作的標籤
    let info_main_area_Div = document.getElementById("info_main_area");
    let preference_main_area_Div = document.getElementById("preference_main_area");
    let weight_main_area_Div = document.getElementById("weight_main_area");
    let place_main_area_Div = document.getElementById("place_main_area");

     // 再對標籤進行操作
    info_main_area_Div.style.display = "none";
    preference_main_area_Div.style.display = "none";
    weight_main_area_Div.style.display = "block";
    place_main_area_Div.style.display = "none";
    
    // 取得頁面的標籤、設定顏色
    let infoDiv = document.getElementById("info");
    let preferenceDiv = document.getElementById("preference");
    let weightDiv = document.getElementById("weight");
    let placeDiv = document.getElementById("place");
    infoDiv.style.color = "#5e5e5e";
    preferenceDiv.style.color = "#5e5e5e";
    weightDiv.style.color = "#8234FF";
    placeDiv.style.color = "#5e5e5e";
}

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
function updateValue(id) {
    var slider = document.getElementById(id);
    var output = document.getElementById(id + '_value');
    output.textContent = slider.value + '%';
}

/* 切換到收藏地點頁面 */
function switchToPlace() {  
    // 先取得要操作的標籤
    let info_main_area_Div = document.getElementById("info_main_area");
    let preference_main_area_Div = document.getElementById("preference_main_area");
    let weight_main_area_Div = document.getElementById("weight_main_area");
    let place_main_area_Div = document.getElementById("place_main_area");

     // 再對標籤進行操作
    info_main_area_Div.style.display = "none";
    preference_main_area_Div.style.display = "none";
    weight_main_area_Div.style.display = "none";
    place_main_area_Div.style.display = "block";
    
    // 取得頁面的標籤、設定顏色
    let infoDiv = document.getElementById("info");
    let preferenceDiv = document.getElementById("preference");
    let weightDiv = document.getElementById("weight");
    let placeDiv = document.getElementById("place");
    infoDiv.style.color = "#5e5e5e";
    preferenceDiv.style.color = "#5e5e5e";
    weightDiv.style.color = "#5e5e5e";
    placeDiv.style.color = "#8234FF";
}