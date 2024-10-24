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
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-solid-rounded/css/uicons-solid-rounded.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-bold-rounded/css/uicons-bold-rounded.css'>
  <link rel="stylesheet" href="/styles/suggestion.css" />
</head>

<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <section class="tertiary-content" id="tertiary-content" style="padding-top:130px;">
    <ul class="nav nav-tabs" style="place-content:center;font-size:20px;">
    <li class="nav-item" id="random-nav-item">
      <a id="tab-button-random" style="cursor:pointer;" class="nav-link active" data-tab="tab-content-random">隨機推薦 <i class="fi fi-br-refresh" style="font-size:14px"></i></a>
    </li>
    <li class="nav-item">
      <a id="tab-button-most-browse" class="nav-link" data-tab="tab-content-most-browse">最多人瀏覽</a>
    </li>
    <li class="nav-item">
      <a id="tab-button-most-favorite" class="nav-link" data-tab="tab-content-most-favorite">最多人收藏</a>
    </li>
  </ul>

  <div class="tab-content-random active" id="tab-content-random"></div>
  <div class="tab-content-most-browse" id="tab-content-most-browse"></div>
  <div class="tab-content-most-favorite" id="tab-content-most-favorite"></div>

  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
  <script src="https://kit.fontawesome.com/876a36192d.js" crossorigin="anonymous"></script>
  <script src="/scripts/suggestion.js"></script>

  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
</body>
</html>
