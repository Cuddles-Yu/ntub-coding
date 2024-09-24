//單商家詳細頁面的地圖標點設置

var map = L.map('map', {
    center: [25.03746, 121.564558],
    zoom: 11,
});

var Stadia_Outdoors = L.tileLayer('https://tiles.stadiamaps.com/tiles/outdoors/{z}/{x}/{y}{r}.{ext}', {
    minZoom: 0,
    maxZoom: 20,
    attribution: '&copy; <a href="https://www.stadiamaps.com/" target="_blank">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    ext: 'png'
}).addTo(map);

var mapIcon = L.icon({
    iconUrl: './images/location_mark2.png',
    iconSize: [30, 40],
    popupAnchor: [0, -20]
});

// 在頁面加載時自動抓取使用者的位置並顯示在地圖上
window.onload = function () {
    // 先定義一個空的經緯度範圍數組，稍後會將商家和使用者的經緯度都加入
    var bounds = [];
    var storeLatLng = null; // 商家的經緯度

    // 抓取商家位置
    fetch(`storedetail-landmark.php?storeId=${storeId}`)
        .then(response => response.json())
        .then(data => {
            console.log('JSON資料引入成功！');
            // 在地圖上顯示商家地標
            console.log(data);

            if (data && !data.error) {
                var latlng = [parseFloat(data[0].latitude), parseFloat(data[0].longitude)];
                storeLatLng = latlng; // 存儲商家位置的經緯度
                var marker = L.marker(latlng, { icon: mapIcon })
                    .on('click', function () {
                        window.open(`${data[0].link}`, '_blank'); // 點擊地標後開啟該店家Google Maps的連結
                    });
                map.addLayer(marker);

                // 把商家的經緯度加到範圍數組中
                bounds.push(latlng);

            } else {
                console.error('錯誤:', data.error);
            }

            // 嘗試獲取使用者位置
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var userLat = position.coords.latitude;
                    var userLng = position.coords.longitude;

                    // 建立使用者的標點
                    var userMarker = L.marker([userLat, userLng], {
                        icon: L.icon({
                            iconUrl: './images/location_mark3.png',  // 替換為你想要的使用者標點圖示
                            iconSize: [30, 40],
                            popupAnchor: [0, -20]
                        })
                    }).bindPopup('您的位置').openPopup();

                    // 將使用者標點加入地圖
                    map.addLayer(userMarker);

                    // 把使用者的經緯度加到範圍數組中
                    bounds.push([userLat, userLng]);

                    // 使用 fitBounds 調整地圖視角，確保商家和使用者都顯示在地圖上
                    map.fitBounds(bounds);

                    // 在 fitBounds 之後手動放大地圖
                    // 你可以增加一個縮放等級，例如放大 2 級
                    // var currentZoom = map.getZoom();  // 獲取當前的縮放級別
                    // map.setZoom(currentZoom - 0.6);  // 在 fitBounds 後進一步縮小2級

                     // 計算距離（公尺）
                     if (storeLatLng) {
                        var distance = calculateDistance(userLat, userLng, storeLatLng[0], storeLatLng[1]);
                        console.log(`距離：${distance} 公尺`);

                        // 在地圖上顯示距離
                        var distanceDiv = L.control({ position: 'topright' });

                        distanceDiv.onAdd = function (map) {
                            var div = L.DomUtil.create('div', 'distance-info');
                            div.innerHTML = `距離商家：${distance} 公尺`;
                            div.style.backgroundColor = 'white';
                            div.style.padding = '5px';
                            return div;
                        };

                        distanceDiv.addTo(map);  // 正確地將控制項添加到地圖
                    }

                }, function (error) {
                    alert('無法取得您的位置: ' + error.message);
                });
            } else {
                alert('您的瀏覽器不支援地理定位功能。');
            }

        })
        .catch(error => console.error('錯誤:', error));
};

// 計算兩個經緯度之間的距離（哈弗辛公式）
function calculateDistance(lat1, lng1, lat2, lng2) {
    var R = 6371000; // 地球半徑，單位：公尺
    var dLat = (lat2 - lat1) * Math.PI / 180;
    var dLng = (lng2 - lng1) * Math.PI / 180;
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLng / 2) * Math.sin(dLng / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    var distance = R * c;
    return Math.round(distance); // 返回距離，四捨五入到整數
}