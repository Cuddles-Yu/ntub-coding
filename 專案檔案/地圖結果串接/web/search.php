<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
?>

<!doctype html>
<html lang="zh-TW">
<head>
  <meta charset="utf-8" />
  <title>搜尋結果 - 評星宇宙</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.4.2/uicons-solid-rounded/css/uicons-solid-rounded.css'>  
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.awesome-markers/dist/leaflet.awesome-markers.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
  <link rel="stylesheet" href="/styles/search.css" />
  <link rel="stylesheet" href="/styles/map.css" />
</head>

<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <!-- <select id="citySelect">
    <option value="">請選擇一個城市</option>
    <option value="new-taipei-city">新北市</option>
    <option value="taipei-city">台北市</option>
  </select> -->

  <div class="container-fluid all-content">
    <div class="content-row row">
      <div class="secondary-content col">
        <div class="search">
          <div class="form-floating search-keyword">
            <input type="text" class="form-control" id="keyword" name="keyword" placeholder="關鍵字">
            <label for="keyword">查詢關鍵字</label>
          </div>
          <button type="button" class="btn btn-solid-gray mt-3 filter-button" data-bs-toggle="modal" data-bs-target="#condition2Modal">
            <i class="fi fi-sr-filter filter-img button-text-icon"></i>篩選條件
          </button>
          <button type="button" class="btn btn-solid-windows-blue mt-3 search-button" id="search-button" onclick="searchStoresByKeyword()">搜尋</button>
        </div>        
        <div class="filter-container">
          <p class="filter-title"><i class="fi fi-sr-filter"></i>已篩選條件：</p>
          <div class="filter-result">
            <div class="condition">停車場</div>
          </div>
        </div>
        <div id="map" class="map">
          <div id="crosshair" style="display:none;"></div>
        </div>
      </div>
      <div class="tertiary-content col">
        <div class="tertiary-title">
          <h1 class="tertiary-text" id="search-result-title">搜尋結果</h1>
          <div class="title-line"></div>
        </div>
        <div id="searchResults" class="store-display store">
          <!-- 動態生成搜尋結果 -->
        </div>
      </div>
    </div>
  </div>

  <?php 
    $modalTitle = '篩選條件';
    $modalId = 'condition2';
    require_once $_SERVER['DOCUMENT_ROOT'].'/form/condition.php'; 
  ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
  <script src="https://kit.fontawesome.com/876a36192d.js" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script src="https://cdn.jsdelivr.net/npm/leaflet.awesome-markers/dist/leaflet.awesome-markers.min.js"></script>
  <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
  <script src="/scripts/search.js" defer></script>
  <script src="/scripts/map.js"></script>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
</body>
</html>