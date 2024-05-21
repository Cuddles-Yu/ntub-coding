// 建構地圖，經緯度座標中心、縮放等級
var map = L.map('map',{
    center:[25.03746, 121.564558],
    zoom: 11,
});

// 檢查地圖是否建構成功
if(map){
    console.log('地圖建構成功！');
};

// 定位使用者所在位置
map.locate({setView: true, maxZoom: 16});

// 建構不同地圖圖層，載入地圖資料
// OpenStreetMap 預設
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
    div.innerHTML += '<h3 style="text-align: center;">圖例說明</h3>';
    div.innerHTML += '<i class="fa-solid fa-bowl-rice"></i> <span>中式餐廳</span><br>';
    div.innerHTML += '<i class="fa-solid fa-burger"></i> <span>美式餐廳</span><br>';
    div.innerHTML += '<i class="fa-solid fa-fish-fins"></i> <span>日式餐廳</span><br>';
    div.innerHTML += '<i class="fa-solid fa-cheese"></i> <span>甜點店</span><br>';
    div.innerHTML += '<i class="fa-solid fa-mug-hot"></i> <span>飲料店</span><br>';
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
fetch('./data.json')
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('JSON資料引入成功！');
        processJsonData(data);
    })
    .catch(error => {
        console.error('引入JSON資料時發生了一些問題：', error);
    });

// 處理JSON資料的函數
function processJsonData(data) {
    var markers = new L.MarkerClusterGroup();

    // 建構marker，加入地圖物件:map中
    for(let i = 0; i < data.length; i++){
        let category = data[i].category;
        let icon;

        switch(category){
            case "中式餐廳":
                icon = chineseMarker;
                break;
            case "西式餐廳":
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

        // 根據評分設置 marker 的顏色
        // 顏色：'red', 'darkred', 'orange', 'green', 'darkgreen', 'blue', 'purple', 'darkpurple', 'cadetblue'
        let markerColor = 'darkblue'; // 預設顏色
        if(data[i].reRating >= 90){
            markerColor = 'green'; // 真實評分90以上為綠色
        } 
        else if(data[i].reRating >= 80){
            markerColor = 'blue'; // 真實評分80以上為藍色
        }
        else if(data[i].reRating >= 70){
            markerColor = 'purple'; // 真實評分70以上為紫色
        }
        else if(data[i].reRating >= 60){
            markerColor = 'orange'; // 真實評分60以上為橘色
        }

        // 使用AwesomeMarkers的圖示，並設置顏色
        icon = createCustomMarker(icon.options.icon, markerColor);

        // 建立 L.Marker
        var marker = L.marker([data[i].Latlng[0], data[i].Latlng[1]], {icon: icon})
            .bindPopup(
                `
                <h3>${data[i].name}</h3>
                <img src="${data[i].preview_image}" style="width: 200px; height: 112.5px; object-fit: cover; object-position: center;"/>
                <div>地址：${data[i].address}</div>
                <div>電話：${data[i].phoneNumber}</div>
                <div>評分：${data[i].rating}</div>
                <div>真實評分：${data[i].reRating}</div>
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