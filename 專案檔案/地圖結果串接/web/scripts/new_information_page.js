/* 點擊漢堡圖示時，顯示/隱藏選單 */
document.getElementById('hamburger_btn').addEventListener('click', function() {
    const navMenu2 = document.getElementById('nav_menu2');
    if (navMenu2.style.display === 'flex' || navMenu2.style.display === '') 
    {
        navMenu2.style.display = 'none';
    } 
    else 
    {
        navMenu2.style.display = 'flex';
    }
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
    infoDiv.style.color = "#6100FF";
    preferenceDiv.style.color = "#000000";
    weightDiv.style.color = "#000000";
    placeDiv.style.color = "#000000";
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
    infoDiv.style.color = "#000000";
    preferenceDiv.style.color = "#6100FF";
    weightDiv.style.color = "#000000";
    placeDiv.style.color = "#000000";
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
    infoDiv.style.color = "#000000";
    preferenceDiv.style.color = "#000000";
    weightDiv.style.color = "#6100FF";
    placeDiv.style.color = "#000000";
}

/* 啟用滑動條 */
function enableSlider() {
    const sliders = document.querySelectorAll('#atmosphere, #product, #service, #price, #popularity');
    sliders.forEach(slider => {
        slider.disabled = false;
    });
}

/* 禁用滑動條 */
function disableSlider() {
    const sliders = document.querySelectorAll('#atmosphere, #product, #service, #price, #popularity');
    sliders.forEach(slider => {
        slider.disabled = true;
    });
}

/* 切換編輯模式 */
function toggleEditMode() {
    const button = document.getElementById('edit_button');
    const buttonText = document.getElementById('button_text');

    if (buttonText.textContent === '編輯') {
        enableSlider();
        buttonText.textContent = '完成';
        button.onclick = disableEditMode;
    } else {
        disableSlider();
        buttonText.textContent = '編輯';
        button.onclick = toggleEditMode;
    }
}

function disableEditMode() {
    disableSlider();
    const buttonText = document.getElementById('button_text');
    buttonText.textContent = '編輯';
    document.getElementById('edit_button').onclick = toggleEditMode;
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
    infoDiv.style.color = "#000000";
    preferenceDiv.style.color = "#000000";
    weightDiv.style.color = "#000000";
    placeDiv.style.color = "#6100FF";
}