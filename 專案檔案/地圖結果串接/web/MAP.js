//整合地圖通用邏輯功能

// 建構地圖，經緯度座標中心、縮放等級
var map = L.map('map', {
    center: [25.0418963, 121.5230431], //改成北商座標
    zoom: 16,
});

// 檢查地圖是否建構成功
if (map) {
    //console.log('地圖建構成功！');
}

// OpenStreetMap 預設
var OpenStreetMap_Mapnik = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// 添加比例尺
L.control.scale({
    position: 'bottomleft', // 比例尺位置(可選項目：'topright', 'topleft', 'bottomright', 'bottomleft')
    imperial: false // 不顯示英制單位
}).addTo(map);

// 創建商家的位置圖標
var mapIcon = L.icon({
    iconUrl: './images/location_mark1.png',
    iconSize: [30, 30],
    popupAnchor: [0, -20] // 彈出框的位置(圖標頂部中心點)
})

// 創建單一該商家的位置圖標
var mapIcon = L.icon({
    iconUrl: './images/location_mark2.png',
    iconSize: [30, 30],
    popupAnchor: [0, -20]
});

// 創建使用者的位置圖標  
var userIcon = L.icon({
    iconUrl: './images/location_mark3.png',
    iconSize: [20, 20], // 圖標大小
    popupAnchor: [0, -20] // 彈出窗口錨點
});

// 在頁面加載時自動抓取使用者的位置並顯示在地圖上
window.onload = function () {
    userLocate();
};

// 定位使用者所在位置的函數
function userLocate() {

    // 嘗試獲取使用者位置
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var userLat = position.coords.latitude;
            var userLng = position.coords.longitude;

            // 從使用者偏好中獲取半徑(縮放等級)
            var zoomLevel = getZoomLevel();

            // 建立使用者的標點
            var userMarker = L.marker([userLat, userLng], {
                icon: userIcon
            }).bindPopup('您的位置').openPopup();

            // 將使用者標點加入地圖
            map.addLayer(userMarker);

            // 設置地圖中心為使用者位置
            map.setView([userLat, userLng], zoomLevel); 
        }, function (error) {
            alert('無法取得您的位置: ' + error.message);
        });
    } else {
        alert('您的瀏覽器不支援地理定位功能。');
    }
}

//根據使用者偏好的半徑距離(縮放等級)設置地圖
function getZoomLevel() {
    // 獲取使用者偏好的縮放等級(的函數)

    //縮放等級 11 剛好是台北市、新北市範圍(縮放等級越大，地圖就被放的越大)
    // 這裡返回一個預設值
    return 11;
}

// 使用者滑動後取得目前地圖中心經緯度
function moveGetCenter() {
    map.on('moveend', function () {
        var moveCenter = map.getCenter(); // 取得地圖中心點的經緯度，回傳 L.LatLng 物件
        document.getElementById('latitude').innerText = moveCenter.lat;
        document.getElementById('longitude').innerText = moveCenter.lng;
    });
}

// 取得地圖中心經緯度
function getCenter() {
    var mapCenter = map.getCenter(); // 取得地圖中心點的經緯度
    //console.log("地圖中心經緯度：", mapCenter);
    return mapCenter; // 回傳 L.LatLng 物件
}

// 設置地圖中心經緯度
function setCenter(lat, lng) {
    var zoomLevel = map.getZoom(); // 取得地圖縮放等級
    map.setView([lat, lng], zoomLevel); // 設置地圖中心點的經緯度 // 緯度 經度 縮放等級
    //console.log("設置地圖中心到：", [lat, lng]);
}