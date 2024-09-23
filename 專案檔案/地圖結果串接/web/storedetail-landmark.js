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
    iconUrl: './images/mapIcon.png',
    iconSize: [30, 40],
    popupAnchor: [0, -20]
});

fetch(`storedetail-landmark.php?storeId=${storeId}`)
    .then(response => response.json())
    .then(data => {
        console.log('JSON資料引入成功！');
        // 在地圖上顯示商家地標
        console.log(data);

        if (data && !data.error) {
            var latlng = [parseFloat(data[0].latitude), parseFloat(data[0].longitude)];
            var marker = L.marker(latlng, { icon: mapIcon })
            .on('click', function() {
                window.open(`https://maps.google.com/?q=${data[0].name}`, '_blank');
            });
            map.addLayer(marker);
            map.setView(latlng, 16);
        } else {
            console.error('錯誤:', data.error);
        }
    })
    .catch(error => console.error('錯誤:', error));
