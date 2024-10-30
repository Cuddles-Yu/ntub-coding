<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
?>

<!doctype html>
<html lang="zh-TW">
<head>
  <meta charset="utf-8" />
  <title>搜尋結果 - 評星宇宙</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="/styles/common/base.css?v=<?=$VERSION?>">
  <link rel="stylesheet" href="/styles/common/map.css?v=<?=$VERSION?>">
  <link rel="stylesheet" href="/styles/search.css?v=<?=$VERSION?>" />
</head>

<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <div class="container-fluid all-content">
    <div class="content-row row">
      <div class="secondary-content col">
        <div class="search">
          <div class="form-floating search-keyword">
            <input type="text" class="form-control" id="keyword" name="keyword" placeholder="關鍵字">
            <label for="keyword">查詢關鍵字</label>
          </div>
          <button type="button" class="btn btn-solid-gray mt-3 filter-button" data-bs-toggle="modal" data-bs-target="#navigationModal">
            <i class="fi fi-sr-marker filter-img button-text-icon"></i>快速定位
          </button>
          <button type="button" class="btn btn-solid-gray mt-3 filter-button" data-bs-toggle="modal" data-bs-target="#conditionModal">
            <i class="fi fi-sr-filter filter-img button-text-icon"></i>篩選條件
          </button>
          <button type="button" class="btn btn-solid-windows-blue mt-3 search-button" id="search-button" onclick="searchStoresByKeyword()">搜尋</button>
        </div>
        <div id="filter-container" class="filter-container">
          <p class="filter-title">條件</p>
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
        <div id="searchResults" class="store-display store"></div>
      </div>
    </div>
  </div>

  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/condition.php';?>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/navigation.php';?>

  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/scripts/common/base.html';?>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/scripts/common/map.html';?>
  <?php $mrtStations = true; require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
  <script src="/scripts/search.js?v=<?=$VERSION?>" defer></script>
</body>
</html>