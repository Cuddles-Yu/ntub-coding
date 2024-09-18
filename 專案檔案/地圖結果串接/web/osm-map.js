// 建構地圖，經緯度座標中心、縮放等級
var map = L.map('map', {
    center: [25.03746, 121.564558],
    zoom: 11,
});

// 檢查地圖是否建構成功
if (map) {
    console.log('地圖建構成功！');
}

// 建構不同地圖圖層，載入地圖資料
var OpenStreetMap_Mapnik = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Stadia 衛星
var Stadia_AlidadeSatellite = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_satellite/{z}/{x}/{y}{r}.{ext}', {
    minZoom: 0,
    maxZoom: 20,
    attribution: '&copy; CNES, Distribution Airbus DS, © Airbus DS, © PlanetObserver (Contains Copernicus Data) | &copy; <a href="https://www.stadiamaps.com/" target="_blank">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    ext: 'jpg'
});

// Stadia 明亮
var Stadia_OSMBright = L.tileLayer('https://tiles.stadiamaps.com/tiles/osm_bright/{z}/{x}/{y}{r}.{ext}', {
    minZoom: 0,
    maxZoom: 20,
    attribution: '&copy; <a href="https://www.stadiamaps.com/" target="_blank">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    ext: 'png'
});

// Stadia 戶外
var Stadia_Outdoors = L.tileLayer('https://tiles.stadiamaps.com/tiles/outdoors/{z}/{x}/{y}{r}.{ext}', {
    minZoom: 0,
    maxZoom: 20,
    attribution: '&copy; <a href="https://www.stadiamaps.com/" target="_blank">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    ext: 'png'
});

// 建構控制面板
var baseMaps = {
    "OpenStreetMap 預設": OpenStreetMap_Mapnik,
    "Stadia 衛星": Stadia_AlidadeSatellite,
    "Stadia 明亮": Stadia_OSMBright,
    "Stadia 戶外": Stadia_Outdoors,
};

// 添加圖層控制面板到地圖
L.control.layers(baseMaps).addTo(map);

// 添加比例尺
L.control.scale({
    position: 'bottomright', // 比例尺位置(可選項目：'topright', 'topleft', 'bottomright', 'bottomleft')
    imperial: false // 不顯示英制單位
}).addTo(map);

// 添加圖例說明
var legend = L.control({position: 'bottomleft'});

legend.onAdd = function (map) {
    var div = L.DomUtil.create('div', 'legend');
    div.style.textAlign = 'left';
    div.innerHTML += '<div style="text-align: center; font-size: 20px; font-weight: bold">標記顏色說明</div>';
    div.innerHTML += '<span style="background-color: #008000; display: inline-block; width: 15px; height: 15px;"></span> <span>評分 90-100</span><br>';
    div.innerHTML += '<span style="background-color: #3388ff; display: inline-block; width: 15px; height: 15px;"></span> <span>評分 80-89</span><br>';
    div.innerHTML += '<span style="background-color: #9370DB; display: inline-block; width: 15px; height: 15px;"></span> <span>評分 70-79</span><br>';
    div.innerHTML += '<span style="background-color: #FFA500; display: inline-block; width: 15px; height: 15px;"></span> <span>評分 60-69</span><br>';
    div.innerHTML += '<span style="background-color: #f03; display: inline-block; width: 15px; height: 15px;"></span> <span>評分 0-59</span><br>';
    return div;
};
legend.addTo(map);

// 創建每個類別的圖標並設置顏色
var chineseMarker = createCustomMarker('bowl-rice', 'red');
var americaMarker = createCustomMarker('burger', 'red');
var japaneseMarker = createCustomMarker('fish-fins', 'red');
var sweetMarker = createCustomMarker('cheese', 'red');
var beverageMarker = createCustomMarker('mug-hot', 'red');

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
        processJsonData(data);
        document.getElementById('searchResults').innerHTML = search.html; // 更新HTML
    })
    .catch(error => {
        console.error('引入JSON資料時發生了一些問題：', error);
    });

var markers = new L.MarkerClusterGroup();

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
        let tag = data[i].tag;
        let icon;

        // 根據 tag 設置圖標
        switch (tag) {
            case "餐廳":
                icon = chineseMarker;
                break;
            case "火鍋":
                icon = americaMarker;
                break;
            // 其他標籤...
            default:
                icon = createCustomMarker('info-sign', 'darkblue'); // 預設圖標
                break;
        }

        // 將評分轉換為0到100範圍
        let rating = data[i].rating * 20;

        // 根據評分設置 marker 的顏色
        let markerColor = 'red'; // 默認顏色
        if (rating >= 90) {
            markerColor = 'green'; // 真實評分90以上為綠色
        }
        else if (rating >= 80) {
            markerColor = 'blue'; // 真實評分80以上為藍色
        }
        else if (rating >= 70) {
            markerColor = 'purple'; // 真實評分70以上為紫色
        }
        else if (rating >= 60) {
            markerColor = 'orange'; // 真實評分60以上為橘色
        }

        // 使用AwesomeMarkers的圖示，並設置顏色
        icon = createCustomMarker(icon.options.icon, markerColor);

        // 檢查是否有有效的 Latlng
        if (data[i].latitude && data[i].longitude) {
            var latlng = [parseFloat(data[i].latitude), parseFloat(data[i].longitude)];
            // 檢查經緯度數據的有效性
            if (isFinite(latlng[0]) && isFinite(latlng[1])) {
                console.log(`Latitude: ${data[i].latitude}, Longitude: ${data[i].longitude}`);
                latlngs.push(latlng);

                // 建立 L.Marker，確保經緯度順序正確（緯度在前latitude，經度在後longtitude）
                var marker = L.marker(latlng, { icon: icon })
                    .bindPopup(
                        `
                        <img src="${data[i].preview_image}" style="width: 200px; height: 112.5px; object-fit: cover; object-position: center;"/>
                        <div style="font-weight: bold; font-size: 16px; margin-top: 10px; margin-bottom: 10px;">${data[i].name}</div>
                        <div style="font-size: 14px; margin-bottom: 5px;">評分：${rating}</div> <!-- 乘以20 -->
                        <div style="font-size: 14px; margin-bottom: 5px;">評論數：${data[i].total_withcomments}/${data[i].sample_ratings}</div>
                        <div style="font-size: 14px; margin-bottom: 5px;">標籤：${data[i].tag}</div>
                        `
                    );

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

    // 設置地圖中心
    if (latlngs.length > 0) {
        setPlaceCenter(latlngs);
    } else {
        console.log("地點數量為0，無法計算中心");
    }
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

// 建構客製化icon函數
function createCustomMarker(iconName, color) {
    return L.AwesomeMarkers.icon({
        icon: iconName,
        markerColor: color,
        prefix: 'fa',
    });
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


