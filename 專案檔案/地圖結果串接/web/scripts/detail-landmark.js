function calculateDistance(lat1, lng1, lat2, lng2) {
  var R = 6371000;
  var dLat = (lat2 - lat1) * Math.PI / 180;
  var dLng = (lng2 - lng1) * Math.PI / 180;
  var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
      Math.sin(dLng / 2) * Math.sin(dLng / 2);
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  var distance = R * c;
  return Math.round(distance);
}
function getZoomLevel(distance) {
  if (distance < 1500) {
      return 18;
  } else if (distance < 5000) {
      return 16;
  } else if (distance < 10000) {
      return 14;
  } else if (distance < 15000) {
      return 12;
  }
  return 11;
}
function normalizeDistance(distance) {
  if (distance < 1000.0) {
      return distance.toFixed(1) + ' 公尺';
  } else {
      return (distance / 1000).toFixed(1) + ' 公里';
  }
}

window.addEventListener('load', function () {
  var bounds = [];
  var storeLatLng = null;  
  const mapElement = this.document.getElementById('map');
  const locateButton = this.document.getElementById('current-locate-button');
  const storeButton = this.document.getElementById('search-locate-button');
  fetch(`struc/detail-landmark.php?storeId=${storeId}`, {
    method: 'POST',
    credentials: 'same-origin'
  }).then(response => response.json())
    .then(async data => {
      if (data && !data.error) {        
        var latlng = [parseFloat(data[0].latitude), parseFloat(data[0].longitude)];
        storeLatLng = latlng;
        var marker = L.marker(latlng, {
          icon: mapIcon
        }).on('click', function () {
          window.open(`${data[0].link}`, '_blank');
        });
        map.addLayer(marker);
        bounds.push(latlng);
        mapElement.setAttribute('store-lat', latlng[0]);
        mapElement.setAttribute('store-lng', latlng[1]);
        storeButton.setAttribute('onclick', 'storeLocate()');
        storeButton.innerHTML = '<img src="/images/button-search-target.png" style="max-width:80%;max-height:80%;margin-bottom:1px;margin-right:5px;"></img>餐廳位置';
      }
      if (navigator.geolocation) {
        try {
          const position = await new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject);
          });
          const userLat = position.coords.latitude;
          const userLng = position.coords.longitude;
          var userMarker = L.marker(
            [userLat, userLng],
            {icon: userIcon}
          );
          map.addLayer(userMarker);
          bounds.push([userLat, userLng]);
          setView([userLat, userLng], 11);
          if (storeLatLng) {
            var distance = calculateDistance(userLat, userLng, storeLatLng[0], storeLatLng[1]);
            var zoomLevel = getZoomLevel(distance);
            map.fitBounds(bounds, {
                padding: [50, 50],
                maxZoom: zoomLevel
            });
            storeButton.style.display = 'block';
            showInfoBar(`當前位置與該商家距離 ${normalizeDistance(distance)}`)
          }
        } catch (error) {
          locateButton.style.display = 'block';
          locateButton.setAttribute('onclick', 'storeLocate()');
          locateButton.innerHTML = '<img src="/images/button-search-target.png" style="max-width:80%;max-height:80%;margin-bottom:1px;margin-right:5px;"></img>餐廳位置';
          storeLocate();
        }
      } else {
        locateButton.style.display = 'block';
        locateButton.setAttribute('onclick', 'storeLocate()');
        locateButton.innerHTML = '<img src="/images/button-search-target.png" style="max-width:80%;max-height:80%;margin-bottom:1px;margin-right:5px;"></img>餐廳位置';
        storeLocate();
      }
    })
    .catch(() => {showAlert('red', '取得您的位置時發生非預期的錯誤');});
});