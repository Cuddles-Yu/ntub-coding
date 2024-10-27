<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn, $MEMBER_INFO;

  $RANDOM_SLOGANS = array(
    "隨手一查，發現附近的美味驚喜！",
    "探索你的味蕾冒險，從這裡開始！",
    "隨時隨地發現附近的隱藏美食。",
    "讓美食帶你走遍每個角落，發現新驚喜！",
    "不一樣的餐廳，不一樣的美味，隨時為你呈現。",
    "發現你身邊最愛的餐廳，隨時準備好品嚐。",
    "附近的美食藏不住，一鍵就能找到！",
    "探索你不知道的美食世界，就在你的身邊。",
    "一鍵查詢，打開你專屬的美食地圖。",
    "餐廳地圖就在這裡，發現你身邊的每一口美味！",
    "今天吃點不一樣的，發現你附近的新選擇！",
    "隨時探索你心中最愛的餐廳！",
    "輕鬆找到你的下一頓美味餐點！",
    "美味的驚喜，就藏在你身邊！",
    "發掘你周邊的隱藏美食，從未如此簡單！",
    "今天想吃什麼？讓我們為你推薦附近的好味道！",
    "美食地圖在手，隨時準備好冒險！",
    "用心感受每一口，找到你最愛的餐廳！",
    "美食新發現，總有一間適合你！",
    "讓我們帶你找到每一口值得期待的美味！",
    "即刻開始你的美食探索之旅！",
    "你的下一頓美味，隨手可得！",
    "讓美食點亮你的一天，從這裡開始。",
    "無論早午晚餐，這裡都有你想要的！",
    "無論何時，發現附近最棒的餐廳就是這麼簡單！",
    "讓我們為你找到符合心情的餐廳！"
  );
?>

<!doctype html>
<html lang="zh-TW">
<head>
  <meta charset="utf-8" />
  <title>餐廳搜尋 - 評星宇宙</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="/styles/common/base.css">
  <link rel="stylesheet" href="/styles/common/map.css">
  <link rel="stylesheet" href="/styles/home.css" />
</head>

<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <section class="main-content">
    <h2 class="title-text"><?=$RANDOM_SLOGANS[array_rand($RANDOM_SLOGANS)];?></h2>
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

  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/condition.php';?>

  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/scripts/common/base.html';?>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/scripts/common/map.html';?>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
  <script src="/scripts/home.js" defer></script>
</body>