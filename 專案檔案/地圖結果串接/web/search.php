<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
?>

<!doctype html>
<html lang="zh-TW">

<head>
  <meta charset="utf-8" />
  <title>搜尋結果 - 評星宇宙</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <meta name="keywords" content="評價, google map" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">

  <!-- 載入 leaflet.css -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

  <!-- 載入 leaflet.awesome-markers.css -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.awesome-markers/dist/leaflet.awesome-markers.css" />

  <!-- 載入 MarkerCluster.css -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

  <!-- 載入 /styles/osm-map.css -->
  <link rel="stylesheet" type="text/css" href="./styles/osm-map.css">

  <link rel="stylesheet" href="styles/search.css" />
  <link rel="stylesheet" href="styles/map.css" />
</head>

<body>

  <!-- ### 頁首 ### -->
  <?php require $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>


  <!-- ### 內容 ### -->
  <div class="container-fluid all-content">
    <div class="content-row row">
      <div class="secondary-content col">
        <div class="search">
          <div class="form-floating search-keyword">
            <input type="text" class="form-control" id="keyword" name="keyword" placeholder="關鍵字">
            <label for="keyword">請輸入查詢關鍵字</label>
          </div>
          <button type="button" class="btn btn-secondary mt-3 filter-button" data-bs-toggle="modal"
            data-bs-target="#exampleModal">
            <i class="fi fi-sr-filter filter-img"></i>篩選條件
          </button>

          <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="exampleModalLabel">篩選條件</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <!-- 篩選選項 -->
                  <div class="input-group input-group-sm mb-3">
                    <p class="checkbox-title">搜尋半徑</p>
                    <input type="text" class="form-control" aria-label="Sizing example input"
                      aria-describedby="inputGroup-sizing-sm">
                    <span class="input-group-text" id="inputGroup-sizing-sm">公尺</span>
                  </div>
                  <p class="checkbox-title">個人需求</p>
                  <div class="checkbox-container">
                    <div class="checkbox-item">
                      <input type="checkbox" id="parking" value="">
                      <label for="parking">停車場</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="wheelchair-accessible" value="">
                      <label for="wheelchair-accessible">無障礙</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="vegetarian" value="">
                      <label for="vegetarian">素食料理</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="healthy" value="">
                      <label for="healthy">健康料理</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="kids_friendly" value="">
                      <label for="kids_friendly">兒童友善</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="pets_friendly" value="">
                      <label for="pets_friendly">寵物友善</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="personal7" value="">
                      <label for="personal7">性別友善</label>
                    </div>
                  </div>
                  <p class="checkbox-title">用餐方式</p>
                  <div class="checkbox-container">
                    <div class="checkbox-item">
                      <input type="checkbox" id="delivery" value="">
                      <label for="delivery">外送</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="takeaway" value="">
                      <label for="takeaway">外帶</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="dine-in" value="">
                      <label for="dine-in">內用</label>
                    </div>
                  </div>
                  <p class="checkbox-title">用餐時段</p>
                  <div class="checkbox-container">
                    <div class="checkbox-item">
                      <input type="checkbox" id="breakfast" value="">
                      <label for="breakfast">早餐</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="brunch" value="">
                      <label for="brunch">早午餐</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="lunch" value="">
                      <label for="lunch">午餐</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="dinner" value="">
                      <label for="dinner">晚餐</label>
                    </div>
                  </div>
                  <p class="checkbox-title">營業時間</p>
                  <div class="checkbox-container">
                    <div class="checkbox-item">
                      <input type="checkbox" id="BusinessHours" value="">
                      <label for="BusinessHours">營業中</label>
                    </div>
                  </div>
                  <p class="checkbox-title">用餐氛圍</p>
                  <div class="checkbox-container">
                    <div class="checkbox-item">
                      <input type="checkbox" id="casual" value="">
                      <label for="casual">氣氛悠閒</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="cosy" value="">
                      <label for="cosy">環境舒適</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="ambiance3" value="">
                      <label for="ambiance3">音樂演奏</label>
                    </div>
                  </div>
                  <p class="checkbox-title">用餐規劃</p>
                  <div class="checkbox-container">
                    <div class="checkbox-item">
                      <input type="checkbox" id="reservation" value="">
                      <label for="reservation">接受訂位</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="group_friendly" value="">
                      <label for="group_friendly">適合團體</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="family_friendly" value="">
                      <label for="family_friendly">適合家庭聚餐</label>
                    </div>
                  </div>
                  <p class="checkbox-title">基礎設施</p>
                  <div class="checkbox-container">
                    <div class="checkbox-item">
                      <input type="checkbox" id="toilet" value="">
                      <label for="toilet">洗手間</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="wi-fi" value="">
                      <label for="wi-fi">無線網路</label>
                    </div>
                  </div>
                  <p class="checkbox-title">付款方式</p>
                  <div class="checkbox-container">
                    <div class="checkbox-item">
                      <input type="checkbox" id="cash" value="">
                      <label for="cash">現金</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="credit_card" value="">
                      <label for="credit_card">信用卡</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="debit_card" value="">
                      <label for="debit_card">簽帳金融卡</label>
                    </div>
                    <div class="checkbox-item">
                      <input type="checkbox" id="mobile_payment" value="">
                      <label for="mobile_payment">行動支付</label>
                    </div>
                  </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                    <button type="button" class="btn btn-primary">儲存</button>
                  </div>
                </div>
              </div>
            </div>
            <button type="button" class="btn btn-secondary mt-3 search-button" id="search-button" onclick="searchKeyword()">搜尋</button>
          </div>
        <!--已選擇篩選條件-->
        <div class="filter-container">
          <p class="filter-title"><i class="fi fi-sr-filter"></i>已篩選條件：</p>
          <div class="filter-result">
            <div class="condition">停車場</div>
          </div>
        </div>

        <div id="map" class="map">
          <div id="crosshair"></div>
          <button type="button" id="locateButton" onclick="defaultLocate()">使用您的位置</button>
        </div>
      </div>

      <div class="tertiary-content col">
        <div class="tertiary-title">
          <h1 class="tertiary-text">搜尋結果</h1>
          <div class="title-line"></div>
        </div>
        <div id="searchResults" class="store-display store">
          <!-- 動態生成搜尋結果 -->
        </div>
      </div>
    </div>
  </div>

  <!-- ### 頁尾 ### -->
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>

  <!-- 載入地圖框架 leaflet.js -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

  <!-- 載入 leaflet.awesome-markers.min.js -->
  <script src="https://cdn.jsdelivr.net/npm/leaflet.awesome-markers/dist/leaflet.awesome-markers.min.js"></script>

  <!-- 載入 Font Awesome Kit -->
  <script src="https://kit.fontawesome.com/876a36192d.js" crossorigin="anonymous"></script>

  <!-- 載入 Markercluster.js -->
  <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

  <!-- 載入主程式 -->  
  <script src="./scripts/map.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="scripts/search.js"></script>
  <script src="scripts/ui-interactions.js"></script>

  <!-- 搜尋跳轉功能 -->
  <script>
    document.querySelectorAll('.home-menu').forEach(page => {
      page.removeAttribute('href');
      page.setAttribute('style', 'cursor:default;');
    });
    document.querySelectorAll('.home-page').forEach(page => {
      page.removeAttribute('href');
      page.setAttribute('style', 'cursor:default;');
    });

    // 創建中心位置的圖標
    var centerIcon = L.icon({
      iconUrl: './images/location-target.png',
      iconSize: [30, 30],
      popupAnchor: [0, -20]
    });

    function searchKeyword() {
      var keyword = document.getElementById('keyword').value;
      var searchResults = document.getElementById('searchResults');
      var mapCenter = getCenter(); // 取得地圖中心經緯度
      var lat = document.getElementById('map').getAttribute('data-lat');
      var lng = document.getElementById('map').getAttribute('data-lng');
      document.title = keyword.trim() === "" ? "搜尋結果 - 評星宇宙" : `${keyword}搜尋結果 - 評星宇宙`;
      window.history.replaceState({}, '', `${location.protocol}//${location.host}${location.pathname}?q=${keyword}&lat=${lat}&lng=${lng}`);

      const formData = new FormData();
      formData.set('q', keyword);
      formData.set('mapCenterLat', lat);
      formData.set('mapCenterLng', lng);

      searchResults.innerHTML = '<div class="rotating"><img src="./images/icon-loading.png" width="30" height="30"></div><p>正在為您搜尋符合條件的商家...</p>';
      // 獲取 HTML 搜索結果
      fetch('struc/search_result.php', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
      })
        .then(response => response.text())
        .then(data => {
          if (data && data.trim() !== "") {
            searchResults.innerHTML = data;
          } else {
            searchResults.innerHTML = "<p>沒有找到相關結果。</p>";
          }
        })
        .catch(error => console.error('搜尋餐廳過程中出現錯誤：', error));
        
      markers.clearLayers(); // 清除先前的地標          
      // 取得地圖中心經緯度
      var mapCenter = getCenter();
      var lat = mapCenter.lat;
      var lng = mapCenter.lng;
      // 移除舊的中心標記（如果存在）
      if (window.centerMarker) map.removeLayer(window.centerMarker);
      // 創建新的中心標記並添加到地圖上
      window.centerMarker = L.marker([lat, lng], {
        icon: centerIcon
      }).addTo(map);

      // 獲取 JSON 地標資料並更新地圖標記
      fetch('struc/search_landmark.php', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
      })
        .then(response => response.json())
        .then(data => {          
          // 更新地圖標記
          if (Array.isArray(data) && data.length > 0) processJsonData(data);         
        })
        .catch(error => console.error('地標獲取錯誤：', error));
      // 使用者滑動後取得目前地圖中心經緯度並自動搜尋
      // moveGetCenter();
    }

    // 清除舊地標並處理新的地標資料
    function processJsonData(data) {
      markers.clearLayers(); // 清除先前的地標!
      // console.log("清除現有地標");

      var latlngs = [];

      // 設定搜尋中心（確保不會自動依據地標範圍縮放）
      var center = map.getCenter();
      setView([center.lat, center.lng], 15);
    
      // 如果有新地標資料，處理並添加到地圖
      if (data.length > 0) {
        for (let i = 0; i < data.length; i++) {
          if (data[i].latitude && data[i].longitude) {
            var latlng = [parseFloat(data[i].latitude), parseFloat(data[i].longitude)];
            if (isFinite(latlng[0]) && isFinite(latlng[1])) {
              latlngs.push(latlng);

              var marker = L.marker(latlng, { icon: storeIcon })
                .bindPopup(
                  `<div class="popup-content" style="cursor: pointer;">
                  <img src="${data[i].preview_image}" style="width: 200px; height: 112.5px; object-fit: cover; object-position: center;"/>
                  <div style="font-weight: bold; font-size: 16px; margin-top: 10px; margin-bottom: 10px;">${data[i].name}</div>
                  <div style="font-size: 14px; margin-bottom: 5px;">評分：${data[i].rating}</div>
                  <div style="font-size: 14px; margin-bottom: 5px;">評論數：${data[i].total_withcomments}/${data[i].sample_ratings}</div>
                  <div style="font-size: 14px; margin-bottom: 5px;">標籤：${data[i].tag}</div>
                  <button style="font-size:12px; right:0; cursor: pointer;" onclick="redirectToDetailPage('${data[i].id}')">詳細資訊</button>
                  </div>`
                );
              markers.addLayer(marker);
            }
          }
        }

        // 將新的地標加回地圖
        map.addLayer(markers);

        // 如果有多個地標，調整地圖的範圍
        if (latlngs.length > 1) {
          setPlaceCenter(latlngs);
        } else if (latlngs.length === 1) {
          setView(latlngs[0], 16); // 如果只有一個標記，放大地圖並設置中心點
        }
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
  </script>

  <!-- 跳轉詳細頁面 -->
  <script>    
    function redirectToDetailPage(storeId) {
      window.location.href = `detail?id=${storeId}`
    }
  </script>

  <script>
    // 頁面載入後檢查 URL 中是否有搜尋關鍵字，並自動執行搜尋
    window.onload = function () {
      const urlParams = new URLSearchParams(window.location.search);
      const keyword = urlParams.get('q');
      const lat = urlParams.get('lat');
      const lng = urlParams.get('lng');
      if (lat && lng) {
        setView([lat, lng], 16);
        defaultLocate(false);
        if (keyword) document.getElementById('keyword').value = keyword;
        document.getElementById('search-button').click();
      } else {
        defaultLocate();
        if (keyword) document.getElementById('keyword').value = keyword;
        document.getElementById('search-button').click();
      }   
    }
  </script>

  <!-- 滾動到商家項目(未成功) -->
  <script>
    function scrollToStore(storeName, storeId) {
      var storeElement = document.querySelector(`id=${storeId}`);
      if (storeElement) {
        storeElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }
  </script>
</body>


</html>