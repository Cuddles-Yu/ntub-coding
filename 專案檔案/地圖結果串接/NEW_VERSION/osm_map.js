// 建構地圖，經緯度座標中心、縮放等級
var map = L.map('map', {
    center: [25.03746, 121.564558],
    zoom: 11,
});

// 檢查地圖是否建構成功
if (map) {
    console.log('地圖建構成功！');
};

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
        console.log(data); // 這裡添加調試輸出，檢查數據格式
        processJsonData(data);
    })
    .catch(error => {
        console.error('引入JSON資料時發生了一些問題：', error);
    });

// 處理JSON資料的函數
function processJsonData(data) {
    var markers = new L.MarkerClusterGroup();

    // 檢查數據是否存在
    if (data.length === 0) {
        console.log("沒有資料顯示在地圖上");
        return;
    }

    // 建構marker，加入地圖物件:map中
    for(let i = 0; i < data.length; i++){
        let tag = data[i].tag;
        let icon;

        // 根據 tag 設置圖標
        switch(tag){
            case "中餐館":
                icon = chineseMarker;
                break;
            case "美式餐廳":
                icon = americaMarker;
                break;
            case "日式餐廳":
                icon = japaneseMarker;
                break;
            case "甜點店":
                icon = sweetMarker;
                break;
            case "飲料店":
                icon = beverageMarker;
                break;
            default:
                icon = createCustomMarker('info-sign', 'darkblue'); // 預設圖標
                break;
        }

        // 將評分轉換為0到100範圍
        let rating = data[i].rating * 20;

        // 根據評分設置 marker 的顏色
        let markerColor = 'red'; // 默認顏色
        if(rating >= 90){
            markerColor = 'green'; // 真實評分90以上為綠色
        } 
        else if(rating >= 80){
            markerColor = 'blue'; // 真實評分80以上為藍色
        }
        else if(rating >= 70){
            markerColor = 'purple'; // 真實評分70以上為紫色
        }
        else if(rating >= 60){
            markerColor = 'orange'; // 真實評分60以上為橘色
        }

        // 使用AwesomeMarkers的圖示，並設置顏色
        icon = createCustomMarker(icon.options.icon, markerColor);

        // 建立 L.Marker，確保經緯度順序正確（緯度在前，經度在後）
        var marker = L.marker([data[i].Latlng[0], data[i].Latlng[1]], {icon: icon})
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
    }

    // 將 markers 加入到 map 的圖層上
    map.addLayer(markers);

    // 設置地圖中心
    setPlaceCenter(data);
}

// 建構客製化icon函數
function createCustomMarker(iconName, color) {
    return L.AwesomeMarkers.icon({
        icon: iconName,
        markerColor: color,
        prefix: 'fa',
    });
}

// 設置地圖中心函式
function setPlaceCenter(data) {
    if (data.length === 0) {
        console.log("地點數量為0，無法計算中心");
        return;
    }

    // 獲取地點的範圍
    var bounds = L.latLngBounds(data.map(item => item.Latlng));
    map.fitBounds(bounds); // 根據給定的座標範圍自動調整地圖的視野範圍
}

// 定位使用者所在位置
function userLocate() {
    map.locate({setView: true, maxZoom: 16});
}

// 定位成功時的處理
map.on('locationfound', function(e) {
    console.log("成功定位使用者所在位置！");
});

// 定位失敗時的處理
map.on('locationerror', function(e) {
    console.error("定位失敗：", e.message);
    alert("定位失敗，請確認您是否已開啟定位。");
});
