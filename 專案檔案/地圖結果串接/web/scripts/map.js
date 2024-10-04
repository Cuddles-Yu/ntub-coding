//整合地圖通用邏輯功能


// 建構地圖，經緯度座標中心、縮放等級
var map = L.map('map');

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

// 在頁面加載時自動抓取使用者的位置並顯示在地圖上
window.onload = function () {
  moveGetCenter();
  defaultLocate();
};

// 定位使用者所在位置的函數
function defaultLocate() {
  const defaultLat = 25.0418963; // 預設緯度
  const defaultLng = 121.5230431; // 預設經度
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (position) {
      const userLat = position.coords.latitude;
      const userLng = position.coords.longitude;
      // 如果成功取得位置，使用使用者的位置
      map.addLayer(L.marker([userLat, userLng], {
        icon: userIcon
      }));
      map.setView([userLat, userLng], 16); // 設定使用者位置的視角
    }, function (error) {
      // 如果無法取得位置，使用預設座標
      alert('無法取得您的位置: ' + error.message);
      map.setView([defaultLat, defaultLng], 16); // 設定預設位置的視角
    });
  } else {
    // 如果瀏覽器不支援地理定位功能，使用預設座標
    alert('您的瀏覽器不支援地理定位功能。');
    map.setView([defaultLat, defaultLng], 16); // 設定預設位置的視角
  }
}


// 使用者滑動後取得目前地圖中心經緯度
function moveGetCenter() {
    map.on('moveend', function () {
        getCenter();        
    });
}

// 取得地圖中心經緯度
function getCenter() {
    var mapCenter = map.getCenter(); // 取得地圖中心點的經緯度
    document.getElementById('map').setAttribute('data-lat', mapCenter.lat);
    document.getElementById('map').setAttribute('data-lng', mapCenter.lng);
    return mapCenter; // 回傳 L.LatLng 物件
}

// 設置地圖中心經緯度
function setView([lat, lng], zoomLevel) {
    map.setView([lat, lng], zoomLevel);
    getCenter()
}

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
    // console.log("清除現有地標");

    // 檢查數據是否存在
    if (data.length === 0) {
        // console.log("沒有資料顯示在地圖上");
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
            setView(latlngs[0], 16); // 如果只有一個標記，放大地圖並設置中心點
        }
    } else {
        // 如果沒有新資料，則清除所有舊地標並顯示提示
        // console.log('沒有找到符合條件的地標');
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
        setView(latlngs[0], 16);
        //console.log("設置地圖視圖到: ", latlngs[0]);
    } else {
        //console.error("Bounds are not valid.");
    }

    getCenter();
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