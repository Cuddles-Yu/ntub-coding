var keyword = new URLSearchParams(window.location.search).get('q')??null;

document.querySelectorAll('.home-menu').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});
document.querySelectorAll('.home-page').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});

window.addEventListener('load', async function () {    
  const urlParams = new URLSearchParams(window.location.search);
  const encodedData = urlParams.get('data')??null;
  const data = encodedData ? (decodeSearchParams(encodedData)??null) : null;
  if (data) {
    if (data.lat && data.lng) {
      setView([data.lat, data.lng], 16);
      await defaultLocate(false);
      document.getElementById('keyword').value = data.keyword;
      this.setTimeout(() => {document.getElementById('search-button').click();}, 50);
    } else {
      await defaultLocate();    
      if (keyword) {
        document.getElementById('keyword').value = data.keyword;
        this.setTimeout(() => {document.getElementById('search-button').click();}, 50);
      }
    }
    this.setTimeout(() => {setConditionFromData(data);}, 50);    
  }
  showCondition();
});

//搜尋結果滾動條隱藏
const storeContainer = document.querySelector('.store');
if (storeContainer.scrollWidth > storeContainer.clientWidth) {
    storeContainer.style.overflowX = 'scroll';
} else {
    storeContainer.style.overflowX = 'auto';
}

// 搜尋框按下 Enter 鍵時觸發搜尋按鈕
document.getElementById('keyword').addEventListener('keydown', function(event) {
  if (event.key === 'Enter') {
    event.preventDefault();
    document.getElementById('search-button').click();
  }
});

function searchStoresByKeyword() {
  var searchRadius = document.getElementById('condition-search-radius-input').value;
  var searchButton = document.getElementById('search-button');
  var keyword = document.getElementById('keyword').value;
  var searchResults = document.getElementById('searchResults');
  var mapCenter = getCenter();
  var lat = document.getElementById('map').getAttribute('data-lat');
  var lng = document.getElementById('map').getAttribute('data-lng');
  document.title = keyword.trim() === "" ? "搜尋結果 - 評星宇宙" : `${keyword}搜尋結果 - 評星宇宙`;
  window.history.replaceState({}, '', `${location.protocol}//${location.host}${location.pathname}?data=${getEncodeSearchParams()}`);

  searchButton.disabled = true;
  const formData = new FormData();
  formData.set('q', keyword);
  formData.set('searchRadius', searchRadius);
  formData.set('mapCenterLat', lat);
  formData.set('mapCenterLng', lng);
  
  document.getElementById('search-result-title').innerText = '搜尋結果';
  searchResults.innerHTML = ''; // '<div class="rotating"><img src="./images/icon-loading.png" width="30" height="30"></div><p style="text-align:center;">正在為您搜尋符合條件的商家...</p>'
  searchButton.disabled = true;

  overlay = generateLoadingOverlay(0, '正在為您搜尋符合條件的商家', '這可能會花費一些時間，請稍候...');
  showInfoBar('');
  document.getElementById('search-locate-button').style.display = 'none';
  markers.clearLayers();  
  if (window.centerMarker) map.removeLayer(window.centerMarker);  
  
  fetch('/struc/search-data.php', {
    method: 'POST',
    credentials: 'same-origin',
    body: formData,
  })
    .then(response => response.json())  // 確保 search-data.php 返回 JSON 格式
    .then(data => {
      if (Array.isArray(data) && data.length > 0) {
        return Promise.all([
          // 處理HTML結果
          new Promise((resolve) => {
            fetch('/struc/search-result.php', {
              method: 'POST',
              credentials: 'same-origin',
              headers: {
                'Content-Type': 'application/json',
                'Cache-Control': 'no-cache'  // 禁用快取
              },
              body: JSON.stringify({ data })  // 傳遞數據到 search-result.php
            })
            .then(response => response.text())
            .then(html => {
              if (html && html.trim() !== "") {
                searchResults.innerHTML = html;
              }
              resolve();
            });
          }),
  
          // 地圖地標處理
          new Promise((resolve) => {
            fetch('/struc/search-landmark.php', {
              method: 'POST',
              credentials: 'same-origin',
              headers: {
                'Content-Type': 'application/json',
                'Cache-Control': 'no-cache'  // 禁用快取
              },
              body: JSON.stringify({ data })
            })
            .then(response => response.json())
            .then(landmarkData => {
              if (Array.isArray(landmarkData) && landmarkData.length > 0) {
                processJsonData(landmarkData);
              }
              resolve();
            });
          })
        ]);
      } else {
        searchResults.innerHTML = "<p style='text-align:center;'>沒有找到相關結果。</p>";
      }
    })
    .catch(error => {
      console.error('查詢過程中產生非預期的錯誤:', error);
    })  
    .finally(() => {
      var mapCenter = getCenter();
      var lat = mapCenter.lat;
      var lng = mapCenter.lng;
      window.centerMarker = L.marker([lat, lng], {
        icon: centerIcon
      }).addTo(map);
      overlay.remove();
      searchButton.disabled = false;
      const count = document.querySelectorAll('.store-body').length;
      document.getElementById('search-result-title').innerText = `前 ${count} 筆搜尋結果`;
      showInfoBar(`搜尋半徑 ${document.getElementById('condition-search-radius-input').value} 公尺`);
    });
  
  
}

function processJsonData(data) {
  markers.clearLayers();
  var latlngs = [];

  var center = map.getCenter();
  setView([center.lat, center.lng], 15);

  if (data.length > 0) {
    for (let i = 0; i < data.length; i++) {
      if (data[i].latitude && data[i].longitude) {
        var latlng = [parseFloat(data[i].latitude), parseFloat(data[i].longitude)];
        if (isFinite(latlng[0]) && isFinite(latlng[1])) {
          latlngs.push(latlng);

          var marker = L.marker(latlng, { icon: storeIcon })
            .bindPopup(
              `<div class="popup-content" style="cursor:default;">
                <img src="${data[i].preview_image}" style="width:200px;height:112.5px;object-fit:cover;object-position:center;"/>
                <div style="font-weight:bold;font-size:16px;margin-top:10px;margin-bottom:10px;overflow:hidden;text-overflow:ellipsis;text-wrap:nowrap;width:200px">${data[i].name}</div>
                <div style="font-size:14px;margin-bottom:5px;color:red;">綜合評分：${data[i].score}</div>
                <div style="font-size:14px;">標籤：${data[i].tag}</div>
              </div>`
            , {
              maxWidth: 193,
              className: 'custom-popup',
              closeButton: false,
              closeOnClick: true,
            }).on('click', function () {
              browseHighlightResult(data[i].id);
            }).on('popupclose', function () {
              // clearHighlightResult();
            });;
          markers.addLayer(marker);
        }
      }
    }

    // 將新的地標加回地圖
    map.addLayer(markers);
    setPlaceCenter(latlngs);
  } else {
    // 如果沒有新資料，則清除所有舊地標並顯示提示
    // console.log('沒有找到符合條件的地標');
    markers.clearLayers(markers); // 確保清除所有舊地標
  }
}
function updateMapMarkers(data) {
  // console.log('更新地圖標記的數據:', data);
  processJsonData(data);
}

function redirectToDetailPage(storeId) {
  window.location.href = `detail?id=${storeId}`
}
function browseHighlightResult(storeId) {
  clearHighlightResult();
  var element = document.querySelector(`.store-body[data-id="${storeId}"]`);
  if (element) {
    element.classList.add('store-card-highlight');
    element.scrollIntoView({ behavior: 'smooth' }); 
  }
}
function clearHighlightResult() {
  var old = document.querySelector(`.store-card-highlight`);
  if (old) old.classList.remove('store-card-highlight');
}

function scrollToStore(storeName, storeId) {
  var storeElement = document.querySelector(`id=${storeId}`);
  if (storeElement) {
    storeElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}