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

// Stadia 戶外(需要授權碼已停用)
// var Stadia_Outdoors = L.tileLayer('https://tiles.stadiamaps.com/tiles/outdoors/{z}/{x}/{y}{r}.{ext}', {
//     minZoom: 0,
//     maxZoom: 20,
//     attribution: '&copy; <a href="https://www.stadiamaps.com/" target="_blank">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
//     ext: 'png'
// }).addTo(map);

// 添加比例尺
L.control.scale({
    position: 'bottomleft', // 比例尺位置(可選項目：'topright', 'topleft', 'bottomright', 'bottomleft')
    imperial: false // 不顯示英制單位
}).addTo(map);

// 創建logo圖標
var mapIcon = L.icon({
    iconUrl: './images/location-mark1.png',
    iconSize: [30, 30],
    popupAnchor: [0, -20] // 彈出框的位置(圖標頂部中心點)
})

var userIcon = L.icon({
    iconUrl: './images/location-mark3.png',
    iconSize: [20, 20], // 圖標大小
    popupAnchor: [0, -20] // 彈出窗口錨點
});

// 在頁面加載時自動抓取使用者的位置並顯示在地圖上
window.onload = function () {
    userLocate();
};

// 定位使用者所在位置的函數
function userLocate() {
    // 先定義一個空的經緯度範圍數組，稍後會將商家和使用者的經緯度都加入
    var bounds = [];

    // 嘗試獲取使用者位置
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var userLat = position.coords.latitude;
            var userLng = position.coords.longitude;

            // 建立使用者的標點
            var userMarker = L.marker([userLat, userLng], {
                icon: userIcon
            }).bindPopup('您的位置').openPopup();

            // 將使用者標點加入地圖
            map.addLayer(userMarker);

            // 把使用者的經緯度加到範圍數組中
            bounds.push([userLat, userLng]);

            // 設置地圖中心為使用者位置
            map.setView([userLat, userLng], 16); 

            // 引入資料庫資料(JSON格式)
            // 使用Fetch API取得JSON資料
            fetch('./base/data.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    //console.log('JSON資料引入成功！');
                    if (Array.isArray(data) && data.length > 0) {
                        processJsonData(data, bounds);
                    } else {
                        //console.log('沒有資料顯示在地圖上');
                    }
                })
                .catch(error => {
                    //console.error('引入JSON資料時發生了一些問題：', error);
                });

        }, function (error) {
            alert('無法取得您的位置: ' + error.message);
        });
    } else {
        alert('您的瀏覽器不支援地理定位功能。');
    }
}



// 設置地圖中心函式
function setPlaceCenter(latlngs) {
    if (latlngs.length === 0) {
        //console.log("地點數量為0，無法計算中心");
        return;
    }

    //console.log("計算地圖中心的 LatLngs: ", latlngs);

    var bounds = L.latLngBounds(latlngs);
    //console.log("計算地圖範圍: ", bounds);

    if (bounds.isValid() && latlngs.length > 1) {
        map.fitBounds(bounds);
        //console.log("設置地圖範圍到: ", bounds);
    } else if (latlngs.length === 1) {
        map.setView(latlngs[0], 16);
        //console.log("設置地圖視圖到: ", latlngs[0]);
    } else {
        //console.error("Bounds are not valid.");
    }
}


// 定位成功時的處理
map.on('locationfound', function (e) {
    //console.log("成功定位使用者所在位置！");
});

// 定位失敗時的處理
map.on('locationerror', function (e) {
    //console.error("定位失敗：", e.message);
    alert("定位失敗，請確認您是否已開啟定位。");
});

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