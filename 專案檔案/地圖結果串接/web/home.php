<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn, $MEMBER_INFO;
?>

<!doctype html>
<html lang="zh-TW">
<head>
  <meta charset="utf-8" />
  <title>餐廳搜尋 - 評星宇宙</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />  
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-solid-rounded/css/uicons-solid-rounded.css'>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.awesome-markers/dist/leaflet.awesome-markers.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
  <link rel="stylesheet" href="/styles/home.css" />
  <link rel="stylesheet" href="/styles/map.css" />
</head>

<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <section class="main-content">
    <h2 class="title-text">美食餐廳搜尋</h2>
    <div class="search">
      <div class="form-floating search-keyword">
          <input type="text" class="form-control" id="keyword" name="keyword" placeholder="關鍵字" style="padding-inline:14px;">
          <label for="keyword">查詢關鍵字</label>
      </div>
      <button type="button" class="btn btn-solid-gray mt-3 filter-button" data-bs-toggle="modal" data-bs-target="#conditionModal">
        <i class="fi fi-sr-filter filter-img button-text-icon"></i>篩選條件
      </button>
      <button type="button" class="btn btn-solid-windows-blue mt-3 search-button" id="search-button" onclick="toSearchPage()">搜尋</button>
    </div>    
    <div id="filter-container" class="filter-container">
      <p class="filter-title">條件</p>
    </div>
    <div id="map" class="map">
      <div id="crosshair" style="display:none;"></div>
    </div>
  </section>

  <?php 
    $modalTitle = '篩選條件';
    $modalId = 'condition';
    require_once $_SERVER['DOCUMENT_ROOT'].'/form/condition.php';
  ?>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>  
  <script src="https://kit.fontawesome.com/876a36192d.js" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script src="https://cdn.jsdelivr.net/npm/leaflet.awesome-markers/dist/leaflet.awesome-markers.min.js"></script>
  <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
  <script src="/scripts/home.js" defer></script>
  <script src="/scripts/map.js"></script>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
</body>