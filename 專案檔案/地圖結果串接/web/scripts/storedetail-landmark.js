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
  fetch(`struc/store-detail_landmark.php?storeId=${storeId}`, {
    method: 'POST',
    credentials: 'same-origin'
  }).then(response => response.json())
    .then(data => {
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
      }
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
          var userLat = position.coords.latitude;
          var userLng = position.coords.longitude;
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
            showInfoBar(`當前位置與該商家距離 ${normalizeDistance(distance)}`)
          }
        }, function (error) {
            alert('無法取得您的位置: ' + error.message);
        });
      } else {
        alert('您的瀏覽器不支援地理定位功能。');
      }
  }).catch(error => console.error('取得您的位置時發生錯誤:', error));
});