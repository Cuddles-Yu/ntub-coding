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
var storeIcon = L.icon({
    iconUrl: './images/location-mark1.png',
    iconSize: [30, 30],
    popupAnchor: [0, -20] // 彈出框的位置(圖標頂部中心點)
})

// 創建單一該商家的位置圖標
var mapIcon = L.icon({
    iconUrl: './images/location-mark2.png',
    iconSize: [30, 30],
    popupAnchor: [0, -20]
});

// 創建使用者的位置圖標  
var userIcon = L.icon({
    iconUrl: './images/location-mark3.png',
    iconSize: [20, 20], // 圖標大小
    popupAnchor: [0, -20] // 彈出窗口錨點
});

// 創建中心位置的圖標    
var centerIcon = L.icon({
    iconUrl: './images/location.png',                          //圖放這裡~~~~~
    iconSize: [30, 30],
    popupAnchor: [0, -20]
});

// 在頁面加載時自動抓取使用者的位置並顯示在地圖上
window.onload = function () {
    userLocate();
    moveGetCenter(); // 開始監聽地圖移動事件
};

// 定位使用者所在位置的函數
function userLocate() {

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

            // 設置地圖中心為使用者位置
            map.setView([userLat, userLng], 16);
        }, function (error) {
            alert('無法取得您的位置: ' + error.message);
        });
    } else {
        alert('您的瀏覽器不支援地理定位功能。');
    }
}

var distance = 1500; // 假設距離為3000公尺
var zoomLevel = getZoomLevel(distance);
map.setZoom(zoomLevel);

// 根據使用者偏好的半徑距離(縮放等級)設置地圖
function getZoomLevel(distance) {
    // 獲取使用者偏好的縮放等級(的函數)

    if (distance <= 50) {
        return 18; // 小於等於50公尺，設置最高的縮放級別
    } else if (distance <= 100) {
        return 17; // 小於等於100公尺，設置較高的縮放級別
    } else if (distance <= 200) {
        return 16; // 小於等於200公尺，設置較高的縮放級別
    } else if (distance <= 300) {
        return 15; // 小於等於300公尺，設置較高的縮放級別
    } else if (distance <= 500) {
        return 14; // 小於等於500公尺，設置較高的縮放級別
    } else if (distance <= 1000) {
        return 13; // 小於等於1公里，設置中等縮放級別
    } else if (distance <= 3000) {
        return 12; // 小於等於3公里，設置較低的縮放級別
    } else if (distance <= 5000) {
        return 11; // 小於等於5公里，設置最低的縮放級別
    }
    // 大於5公里，設置最低的縮放級別(預設值)
    return 10;
}

// 使用者滑動後取得目前地圖中心經緯度
function moveGetCenter() {
    map.on('moveend', function () {
        var moveCenter = map.getCenter(); // 取得地圖中心點的經緯度，回傳 L.LatLng 物件
        document.getElementById('latitude').innerText = moveCenter.lat;
        document.getElementById('longitude').innerText = moveCenter.lng;
        // 更新中心圖標的位置
        getCenter();

    });
}

// 取得地圖中心經緯度
function getCenter() {
    var mapCenter = map.getCenter(); // 取得地圖中心點的經緯度
    //console.log("地圖中心經緯度：", mapCenter);
    // 移除舊的中心標記（如果存在）
    if (window.centerMarker) {
        map.removeLayer(window.centerMarker);
    }

    // 創建新的中心標記並添加到地圖上
    window.centerMarker = L.marker([mapCenter.lat, mapCenter.lng], {
        icon: centerIcon
    }).addTo(map);

    return mapCenter; // 回傳 L.LatLng 物件
}

// 設置地圖中心經緯度
function setCenter(lat, lng) {
    var zoomLevel = map.getZoom(); // 取得地圖縮放等級
    map.setView([lat, lng], zoomLevel); // 設置地圖中心點的經緯度 // 緯度 經度 縮放等級
    //console.log("設置地圖中心到：", [lat, lng]);
}

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

var markers = new L.MarkerClusterGroup({
    maxClusterRadius: function (zoom) {
        // 如果地標數量多且過於接近，則自動進行群組
        return zoom > 14 ? 40 : 80; // 縮放級別大於14時群組半徑設置為較小值
    }
});

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

    // 如果有新地標資料，處理並添加到地圖
    if (data.length > 0) {
        // 建構marker，加入地圖物件:map中
        for (let i = 0; i < data.length; i++) {
            // 檢查是否有有效的 Latlng
            if (data[i].latitude && data[i].longitude) {
                var latlng = [parseFloat(data[i].latitude), parseFloat(data[i].longitude)];
                // 檢查經緯度數據的有效性
                if (isFinite(latlng[0]) && isFinite(latlng[1])) {
                    latlngs.push(latlng);

                    // 建立 L.Marker，確保經緯度順序正確（緯度在前latitude，經度在後longtitude）
                    var marker = L.marker(latlng, { icon: storeIcon })
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

        // 自動調整地圖範圍
        if (latlngs.length > 1) {
            setPlaceCenter(latlngs); // 自動調整地圖範圍
        } else if (latlngs.length === 1) {
            map.setView(latlngs[0], 16); // 如果只有一個標記，放大地圖並設置中心點
        }
    } else {
        // 如果沒有新資料，則清除所有舊地標並顯示提示
        console.log('沒有找到符合條件的地標');
        markers.clearLayers(markers); // 確保清除所有舊地標
    }
}

// 設置地圖中心函式(會讓地圖自動縮放)
function setPlaceCenter(latlngs) {
    if (latlngs.length === 0) {
        //console.log("地點數量為0，無法計算中心");
        return;
    }

    var bounds = L.latLngBounds(latlngs);

    // 保留當前地圖中心
    var currentCenter = map.getCenter();

    if (bounds.isValid() && latlngs.length > 1) {
        var zoomLevel = map.getBoundsZoom(bounds); // 計算適當的縮放級別
        map.setZoomAround(currentCenter, zoomLevel); //地圖會以當前中心點為基準進行縮放
        //console.log("設置地圖範圍到: ", bounds);
    } else if (latlngs.length === 1) {
        map.setView(latlngs[0], 16);
        //console.log("設置地圖視圖到: ", latlngs[0]);
    } else {
        //console.error("Bounds are not valid.");
    }

    // 保留搜尋中心
    var searchCenter = map.getCenter();

    // 移除舊的中心標記（如果存在）
    if (window.centerMarker) {
        map.removeLayer(window.centerMarker);
    }

    // 創建新的中心標記並添加到地圖上
    window.centerMarker = L.marker([searchCenter.lat, searchCenter.lng], {
        icon: centerIcon
    }).addTo(map);

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