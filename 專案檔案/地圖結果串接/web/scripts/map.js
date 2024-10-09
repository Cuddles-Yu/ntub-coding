var map = L.map('map');

map.on('locationerror', function (e) {
  showAlert('red', '定位失敗，請確認您是否已開啟定位。');
});

var OpenStreetMap_Mapnik = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);
var storeIcon = L.icon({
  iconUrl: './images/location-mark1.png',
  iconSize: [30, 30],
  popupAnchor: [0, -20]
})
var mapIcon = L.icon({
  iconUrl: './images/location-mark2.png',
  iconSize: [30, 30],
  popupAnchor: [0, -20]
});
var userIcon = L.icon({
  iconUrl: './images/location-mark3.png',
  iconSize: [20, 20],
  popupAnchor: [0, -20]
});
var markers = new L.MarkerClusterGroup({
maxClusterRadius: function (zoom) {
    return zoom > 14 ? 40 : 80;
}
});

L.control.scale({
    position: 'bottomleft',
    imperial: false
}).addTo(map);


// 在頁面加載時自動抓取使用者的位置並顯示在地圖上
window.addEventListener('load', function () {
  generateNavigationButton();
  generateInfoBar();
  map.on('moveend', function () {
    getCenter();        
  });  
});

function generateNavigationButton() {
  var locationButton = L.control({ position: 'topleft' });
  locationButton.onAdd = function (map) {
    var button = L.DomUtil.create('button', 'btn-solid-gray');
    button.setAttribute('id', 'current-locate-button');
    button.setAttribute('type', 'button');
    button.setAttribute('onclick', 'defaultLocate()');
    button.setAttribute('style', 
      'height:25px;'+
      'margin-top:-60px;'+
      'margin-left:50px;'
    );
    button.innerHTML = '<img src="/images/button-navigation.png" style="max-width:80%;max-height:80%;margin-bottom:1px;margin-right:5px;"></img>返回所在位置';
    return button;
  };
  locationButton.addTo(map);
  var searchButton = L.control({ position: 'topleft' });
  searchButton.onAdd = function (map) {
    var button = L.DomUtil.create('button', 'btn-solid-gray');
    button.setAttribute('id', 'search-locate-button');
    button.setAttribute('type', 'button');
    button.setAttribute('onclick', 'searchLocate()');
    button.setAttribute('style', 
      'height:25px;'+
      'margin-top:-30px;'+
      'margin-left:50px;'+
      'display:none;'
    );
    button.innerHTML = '<img src="/images/button-search-target.png" style="max-width:80%;max-height:80%;margin-bottom:1px;margin-right:5px;"></img>返回查詢位置';
    return button;
  };
  searchButton.addTo(map);
}


function generateInfoBar(info='') {
  var mapInfoBar = L.control({ position: 'topright' });
  mapInfoBar.onAdd = function (map) {
    var div = L.DomUtil.create('div', '');              
    div.setAttribute('id', 'map-info-bar');
    div.innerHTML = info;
    div.setAttribute('style', 
      'background-color:white;'+
      'padding:5px 10px;'+
      'border-top-right-radius:10px;'+
      'border-bottom-left-radius:10px;'+
      'margin:-1px;'+
      'font-weight:bold;'+
      'border:1px solid #716d6d;'+
      'display:none;'
    );
    return div;
  };            
  mapInfoBar.addTo(map);
}
function showInfoBar(info) {
  var infoBar = document.getElementById('map-info-bar');
  infoBar.innerHTML = info;
  infoBar.style.display = (info.trim()!=='')?'block':'none';
}

// 定位使用者所在位置的函數
async function defaultLocate(autoSetView = true) {
  const defaultLat = 25.0418963;
  const defaultLng = 121.5230431;
  if (autoSetView) map.setView([defaultLat, defaultLng], 16);
  if (navigator.geolocation) {
    try {
      const position = await new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject);
      });
      const userLat = position.coords.latitude;
      const userLng = position.coords.longitude;
      map.addLayer(L.marker([userLat, userLng], {
        icon: userIcon
      }));
      if (autoSetView) map.setView([userLat, userLng], 16);
    } catch (error) {
      console.log('無法取得您的位置: ' + error.message);
    }
  } else {
    console.log('您的瀏覽器不支援地理定位功能。');
  }
  document.getElementById('crosshair').style.display = 'block';
}

function searchLocate() {
  var lat = document.getElementById('map').getAttribute('search-lat');
  var lng = document.getElementById('map').getAttribute('search-lng');
  var zoom = document.getElementById('map').getAttribute('search-zoom');
  if (lat&&lng&&zoom) map.setView([lat, lng], zoom);
}

// 取得地圖中心經緯度
function getCenter() {
  var mapCenter = map.getCenter();
  document.getElementById('map').setAttribute('data-lat', mapCenter.lat);
  document.getElementById('map').setAttribute('data-lng', mapCenter.lng);
  return mapCenter;
}

// 設置地圖中心經緯度
function setView([lat, lng], zoomLevel) {
  map.setView([lat, lng], zoomLevel);
  getCenter()
}

// 設置地圖中心函式(會讓地圖自動縮放)
function setPlaceCenter(latlngs) {
    if (latlngs.length === 0) return;
    var bounds = L.latLngBounds(latlngs);
    var currentCenter = map.getCenter();
    if (bounds.isValid() && latlngs.length > 1) {
      var zoomLevel = map.getBoundsZoom(bounds);
      map.setZoomAround(currentCenter, zoomLevel);
    } else if (latlngs.length === 1) {
      setView(latlngs[0], 16);
    }
    var mapCenter = getCenter();
    document.getElementById('map').setAttribute('search-lat', mapCenter.lat);
    document.getElementById('map').setAttribute('search-lng', mapCenter.lng);
    document.getElementById('map').setAttribute('search-zoom', zoomLevel);    
    document.getElementById('search-locate-button').style.display = 'block';    
}