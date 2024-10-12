<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn;
?>

<!doctype html>
<html lang="zh-TW">
<head>
  <meta charset="utf-8" />
  <title>餐廳推薦 - 評星宇宙</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.4.2/uicons-solid-rounded/css/uicons-solid-rounded.css'>  
  <link rel="stylesheet" href="/styles/suggestion.css" />
  <link rel="stylesheet" href="/styles/map.css" />
</head>

<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <section class="tertiary-content" id="tertiary-content" style="padding-top:100px;">
    <span id="tab-1" class="tab-1">熱門推薦</span>
    <span id="tab-2" class="tab-2">偏好推薦</span>
    <span id="tab-3" class="tab-3">隨機推薦</span>
    <div id="tab">
      <ul>
        <li><a id="tab-button-1" class="title-text-2" data-tab="tab-content-1">熱門推薦</a></li>
        <li><a id="tab-button-2" class="title-text-2" data-tab="tab-content-2">偏好推薦</a></li>
        <li><a id="tab-button-3" class="title-text-2" data-tab="tab-content-3">隨機推薦</a></li>
      </ul>
      <div class="tab-content-1 active" id="tab-content-1">
      </div>
      <div class="tab-content-2" id="tab-content-2"> 
      </div>
      <div class="tab-content-3" id="tab-content-3">
      </div>
    </div>
  </section>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
  <script src="https://kit.fontawesome.com/876a36192d.js" crossorigin="anonymous"></script>
  <script src="/scripts/suggestion.js"></script>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
</body>
</html>
