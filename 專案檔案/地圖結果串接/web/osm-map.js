// 建構地圖，經緯度座標中心、縮放等級
var map = L.map('map', {
    center: [25.0418963, 121.5230431], //改成北商座標
    zoom: 16,
});

// 檢查地圖是否建構成功
if (map) {
    console.log('地圖建構成功！');
}

// Stadia 戶外
var Stadia_Outdoors = L.tileLayer('https://tiles.stadiamaps.com/tiles/outdoors/{z}/{x}/{y}{r}.{ext}', {
    minZoom: 0,
    maxZoom: 20,
    attribution: '&copy; <a href="https://www.stadiamaps.com/" target="_blank">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    ext: 'png'
}).addTo(map);

// 添加比例尺
L.control.scale({
    position: 'bottomleft', // 比例尺位置(可選項目：'topright', 'topleft', 'bottomright', 'bottomleft')
    imperial: false // 不顯示英制單位
}).addTo(map);

// 創建logo圖標
var mapIcon = L.icon({
    iconUrl: './images/mapIcon.png',
    iconSize: [30, 40],
    popupAnchor: [0, -20] // 彈出框的位置(圖標頂部中心點)
})

// 引入資料庫資料(JSON格式)
// 使用Fetch API取得JSON資料
fetch('./data.php')
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('JSON資料引入成功！');
        if (Array.isArray(data) && data.length > 0) {
            processJsonData(data);
        } else {
            console.log('沒有資料顯示在地圖上');
        }
    })
    .catch(error => {
        console.error('引入JSON資料時發生了一些問題：', error);
    });

var markers = new L.MarkerClusterGroup();

// 處理JSON資料，建立地標
function processJsonData(data) {
    // 清除先前的標記
    markers.clearLayers();
    console.log("清除現有地標");

    // 檢查數據是否存在
    if (data.length === 0) {
        console.log("沒有資料顯示在地圖上");
        return;
    }

    var latlngs = [];

    // 建構marker，加入地圖物件:map中
    for (let i = 0; i < data.length; i++) {
        // 檢查是否有有效的 Latlng
        if (data[i].latitude && data[i].longitude) {
            var latlng = [parseFloat(data[i].latitude), parseFloat(data[i].longitude)];
            // 檢查經緯度數據的有效性
            if (isFinite(latlng[0]) && isFinite(latlng[1])) {
                latlngs.push(latlng);

                // 建立 L.Marker，確保經緯度順序正確（緯度在前latitude，經度在後longtitude）
                var marker = L.marker(latlng, { icon: mapIcon })
                    .bindPopup(
                        `
                        <div class="popup-content" style="cursor: pointer;">
                        <img src="${data[i].preview_image}" style="width: 200px; height: 112.5px; object-fit: cover; object-position: center;"/>
                        <div style="font-weight: bold; font-size: 16px; margin-top: 10px; margin-bottom: 10px;">${data[i].name}</div>
                        <div style="font-size: 14px; margin-bottom: 5px;">評分：${data[i].rating}</div> <!-- 乘以20 -->
                        <div style="font-size: 14px; margin-bottom: 5px;">評論數：${data[i].total_withcomments}/${data[i].sample_ratings}</div>
                        <div style="font-size: 14px; margin-bottom: 5px;">標籤：${data[i].tag}</div>
                        <button style="font-size:12px; right:0; cursor: pointer;" onclick="redirectToDetailPage('${data[i].id}')">詳細資訊</button>
                        </div>
                        `
                    )
                    .on('popupopen', function () {
                        document.querySelector('.popup-content').addEventListener('click', function () {
                            scrollToStore(data[i].id, data[i].name);
                        });
                    });

                // 將 marker 加入到 markers 群組中
                markers.addLayer(marker);
            } else {
                console.error("無效的經緯度數據: ", latlng);
            }
        } else {
            console.error("Latlng 格式錯誤或缺失");
        }
    }

    // 將 markers 加入到 map 的圖層上
    map.addLayer(markers);
}

// 設置地圖中心函式
function setPlaceCenter(latlngs) {
    if (latlngs.length === 0) {
        console.log("地點數量為0，無法計算中心");
        return;
    }

    console.log("計算地圖中心的 LatLngs: ", latlngs);

    var bounds = L.latLngBounds(latlngs);
    console.log("計算地圖範圍: ", bounds);

    if (bounds.isValid() && latlngs.length > 1) {
        map.fitBounds(bounds);
        console.log("設置地圖範圍到: ", bounds);
    } else if (latlngs.length === 1) {
        map.setView(latlngs[0], 16);
        console.log("設置地圖視圖到: ", latlngs[0]);
    } else {
        console.error("Bounds are not valid.");
    }
}

// 定位使用者所在位置
function userLocate() {
    map.locate({ setView: true, maxZoom: 16 });
}

// 定位成功時的處理
map.on('locationfound', function (e) {
    console.log("成功定位使用者所在位置！");
});

// 定位失敗時的處理
map.on('locationerror', function (e) {
    console.error("定位失敗：", e.message);
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
    return mapCenter; // 回傳 L.LatLng 物件
}

// 設置地圖中心經緯度
function setCenter(lat, lng) {
    var zoomLevel = map.getZoom(); // 取得地圖縮放等級
    map.setView([lat, lng], zoomLevel); // 設置地圖中心點的經緯度 // 緯度 經度 縮放等級
    console.log("設置地圖中心到：", [lat, lng]);
}