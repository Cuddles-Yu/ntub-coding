const allowedBounds = L.latLngBounds(
  L.latLng(24.75, 121.1),
  L.latLng(25.35, 122.0)
)

var map = L.map('map', {
  zoom: 11,
  minZoom: 11,
  maxZoom: 18,
  maxBoundsViscosity: 0.5,
  maxBounds: allowedBounds
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


var geoLayer;
function clearGeoJson() {
  if (geoLayer) map.removeLayer(geoLayer);
}
async function drawGeoJson(fit=true) {
  const city = document.getElementById('condition-city-select').value;
  const dist = document.getElementById('condition-dist-select')?document.getElementById('condition-dist-select').value:'';
  clearGeoJson();
  if (city) {
    fetch(`/geo/${city}.json`)
    .then(response => response.json())
    .then(async json => {
      if (dist) {
        const filteredFeatures = json.features.filter(feature => {
          return feature.properties.T_Name === dist;
        });
        geoLayer = L.geoJSON({
          type: "FeatureCollection",
          features: filteredFeatures
        }).addTo(map);
      } else {
        const dists = [];
        const formData = new FormData();
        formData.set('city', city);
        await fetch('/handler/get-dists.php', {
          method: 'POST',
          credentials: 'same-origin',
          body: formData
        }).then(response => response.json())
          .then(data => {
            data.forEach(dist => {dists.push(dist);});
          })
          .catch(error => {showAlert('red', error);}
        );
        const filteredFeatures = json.features.filter(feature => {
          return dists.includes(feature.properties.T_Name);
        });
        geoLayer = L.geoJSON({
          type: "FeatureCollection",
          features: filteredFeatures
        }).addTo(map);
      }
      if (fit) map.fitBounds(geoLayer.getBounds());
    });
  }
}

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
      'margin-left:50px;'+
      'display:none;'
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
  const locateButton = document.getElementById('current-locate-button');
  const locateHintShown = localStorage.getItem('locateHint');
  if (navigator.geolocation) {
    try {
      const position = await new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject, {
          enableHighAccuracy: true,
          timeout: 5000,
          maximumAge: 0
        });
      });
      const userLat = position.coords.latitude;
      const userLng = position.coords.longitude;
      const userLocation = L.latLng(userLat, userLng);
      if (allowedBounds.contains(userLocation)) {
        map.addLayer(L.marker([userLat, userLng], {
          icon: userIcon
        }));
        if (autoSetView) map.setView([userLat, userLng], 16);
      } else {
        // if (!locateHintShown) {
        //   showAlert('red', '位置不在支援範圍，自動將您定位至預設位置');
        //   localStorage.setItem('locateHint', 'true');
        // }
        locateButton.innerHTML = '<img src="/images/button-navigation.png" style="max-width:80%;max-height:80%;margin-bottom:1px;margin-right:5px;"></img>預設位置';
        if (autoSetView) map.setView([defaultLat, defaultLng], 16);
      }
    } catch (error) {
      // if (!locateHintShown) {
      //   showAlert('red', '無法確認定位，自動將您定位至預設位置');
      //   localStorage.setItem('locateHint', 'true');
      // }
      if (!document.getElementById('map').getAttribute('store-lat')) {
        locateButton.innerHTML = '<img src="/images/button-navigation.png" style="max-width:80%;max-height:80%;margin-bottom:1px;margin-right:5px;"></img>預設位置';
        if (autoSetView) map.setView([defaultLat, defaultLng], 16);
      }
    }
  } else {
    // if (!locateHintShown) {
    //   showAlert('red', '您的瀏覽器不支援定位功能，自動將您定位至預設位置');
    //   localStorage.setItem('locateHint', 'true');
    // }
    if (!document.getElementById('map').getAttribute('store-lat')) {
      locateButton.innerHTML = '<img src="/images/button-navigation.png" style="max-width:80%;max-height:80%;margin-bottom:1px;margin-right:5px;"></img>預設位置';
      if (autoSetView) map.setView([defaultLat, defaultLng], 16);
    }
  }
  locateButton.style.display = 'block';
  const crosshair = document.getElementById('crosshair');
  if (crosshair) crosshair.style.display = 'block';
}

function searchLocate() {
  var lat = document.getElementById('map').getAttribute('search-lat');
  var lng = document.getElementById('map').getAttribute('search-lng');
  var zoom = document.getElementById('map').getAttribute('search-zoom');
  if (lat&&lng&&zoom) map.setView([lat, lng], zoom);
}

function storeLocate() {
  var lat = document.getElementById('map').getAttribute('store-lat');
  var lng = document.getElementById('map').getAttribute('store-lng');
  if (lat&&lng) map.setView([lat, lng], 16);
}

function getCenter() {
  var mapCenter = map.getCenter();
  document.getElementById('map').setAttribute('data-lat', mapCenter.lat);
  document.getElementById('map').setAttribute('data-lng', mapCenter.lng);
  return mapCenter;
}
function setView([lat, lng], zoomLevel) {
  map.setView([lat, lng], zoomLevel, { animate: true });
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

function highlightMarkerById(storeId) {
  var marker = markerDictionary[storeId];
  if (marker) {
    var latlng = marker.getLatLng();
    if (latlng) {
      clearHighlightResult();
      window.scrollTo(0, 0, 'smooth');
      var element = document.querySelector(`.store-body[data-id="${storeId}"]`);
      if (element) element.classList.add('store-card-highlight');

      setView([latlng.lat, latlng.lng], 16, { animate: true });
      map.once('moveend', function () {
        var visibleParent = markers.getVisibleParent(marker);
        if (visibleParent && visibleParent !== marker) {
          visibleParent.spiderfy();
        }
        setTimeout(() => {
          marker.openPopup();
        }, 100);
      });
    }
  }
}

var markerDictionary = {};
function processMapData(data) {
  markers.clearLayers();
  var latlngs = [];
  if (data.length > 0) {
    for (let i = 0; i < data.length; i++) {
      if (data[i].latitude && data[i].longitude) {
        var latlng = [parseFloat(data[i].latitude), parseFloat(data[i].longitude)];
        if (isFinite(latlng[0]) && isFinite(latlng[1])) {
          latlngs.push(latlng);
          var marker = L.marker(latlng, { icon: storeIcon })
            .bindPopup(
              `<div class="popup-content" data-id="${data[i].id}" style="cursor:default;">
                <img src="${data[i].preview_image}" style="width:200px;height:112.5px;object-fit:cover;object-position:center;"/>
                <div style="font-weight:bold;font-size:16px;margin-top:10px;margin-bottom:10px;overflow:hidden;text-overflow:ellipsis;text-wrap:nowrap;width:200px">${data[i].name}</div>
                <div style="font-size:14px;margin-bottom:5px;color:red;">綜合評分：${data[i].score}</div>
                <div style="font-size:14px;">標籤：${data[i].tag}</div>
              </div>`,
              {
                maxWidth: 193,
                className: 'custom-popup',
                closeButton: false,
                closeOnClick: true,
              }
            ).on('click', function () {
              browseHighlightResult(data[i].id);
            });
          markerDictionary[data[i].id] = marker;
          markers.addLayer(marker);
        }
      }
    }
    map.addLayer(markers);
    setPlaceCenter(latlngs);
  }
}

function updateMapMarkers(data) {
  processMapData(data);
}