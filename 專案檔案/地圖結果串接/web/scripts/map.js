var map = L.map('map', {
  zoom: 11,
  minZoom: 11,
  maxZoom: 18,
  maxBoundsViscosity: 0.5,  
  maxBounds: L.latLngBounds(
    L.latLng(24.75, 121.1),
    L.latLng(25.35, 122.0)
  )
});

map.on('locationerror', function (e) {
  showAlert('red', '定位失敗，請確認您是否已開啟定位');
});
map.on('moveend', function() {
  getCenter();
  var bounds = map.getBounds();
  var maxBounds = map.options.maxBounds;
  if (!maxBounds.contains(bounds)) showAlert("red", "僅支援雙北地區餐廳");
});

var OpenStreetMap_Mapnik = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>contributors'
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
var centerIcon = L.icon({
  iconUrl: './images/location-target.png',
  iconSize: [34, 34],
  popupAnchor: [0, -20]
});

var markers = new L.MarkerClusterGroup({
  showCoverageOnHover: false,
  maxClusterRadius: function (zoom) {
    return zoom > 14 ? 40 : 80;
  },
  iconCreateFunction: function(cluster) {    
		return L.divIcon({
      className: 'leaflet-marker',
      html: `<div style="position: relative; width: 40px; height: 40px;opacity:0.9;">
              <img src="/images/location-group3.png" style="width: 100%; height: 100%;">
              <w style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -60%); color: white; font-size: 15px;">${cluster.getChildCount()}</w>
            </div>`,
      iconSize: [40, 40],
      iconAnchor: [20, 40]
  })}
});


// var geoLayer;
// document.getElementById('citySelect').addEventListener('change', function() {
//   var city = this.value;
//   if (geoLayer) map.removeLayer(geoLayer);
//   if (city) {
//     fetch(`/geo/${city}.json`)
//       .then(response => response.json())
//       .then(json => {
//         geoLayer = L.geoJSON(json).bindPopup(function (layer) {
//           return layer.feature.properties.T_Name;
//         }).addTo(map);
//         //map.fitBounds(currentLayer.getBounds());
//       });
//   }
// });

// const popup = L.popup();
// function onMapClick(e) {
//   let lat = e.latlng.lat;
//   let lng = e.latlng.lng;
//   popup
//     .setLatLng(e.latlng)
//     .setContent(`緯度：${lat}<br/>經度：${lng}`)
//     .openOn(map);
// }
// map.on('click', onMapClick);


L.control.scale({
    position: 'bottomleft',
    imperial: false
}).addTo(map);


window.addEventListener('load', function () {
  generateNavigationButton();
  generateInfoBar();
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
    button.innerHTML = '<img src="/images/button-navigation.png" style="max-width:80%;max-height:80%;margin-bottom:1px;margin-right:5px;"></img>所在位置';
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
    button.innerHTML = '<img src="/images/button-search-target.png" style="max-width:80%;max-height:80%;margin-bottom:1px;margin-right:5px;"></img>查詢位置';
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
      showAlert('red', `取得您的位置過程中發生非預期的錯誤`);
    }
  } else {
    showAlert('red', '不支援地理定位功能');
  }
  var crosshair = document.getElementById('crosshair');
  if (crosshair) crosshair.style.display = 'block';
}

function searchLocate() {
  var lat = document.getElementById('map').getAttribute('search-lat');
  var lng = document.getElementById('map').getAttribute('search-lng');
  var zoom = document.getElementById('map').getAttribute('search-zoom');
  if (lat&&lng&&zoom) map.setView([lat, lng], zoom);
}

function getCenter() {
  var mapCenter = map.getCenter();
  document.getElementById('map').setAttribute('data-lat', mapCenter.lat);
  document.getElementById('map').setAttribute('data-lng', mapCenter.lng);
  return mapCenter;
}
function setView([lat, lng], zoomLevel) {
  map.setView([lat, lng], zoomLevel);
  getCenter()
}
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