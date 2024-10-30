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
  <title>餐廳名冊 - 評星宇宙</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="/styles/common/base.css?v=<?=$VERSION?>">
  <link rel="stylesheet" href="/styles/roster.css?v=<?=$VERSION?>" />
</head>

<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <section class="tertiary-content" id="tertiary-content" style="padding-top:130px;">
    <ul class="nav nav-tabs" style="place-content:center;font-size:20px;">
    <li class="nav-item">
      <a id="tab-button-environmental" class="green nav-link active" data-tab="tab-content-environmental">環保餐廳 <i class="fi fi-br-recycle" style="font-size:14px"></i></a>
    </li>
    <li class="nav-item">
      <a id="tab-button-hakka" class="orange nav-link" data-tab="tab-content-hakka">客家餐廳 <i class="fi fi-sr-star" style="font-size:14px"></i></a>
    </li>
  </ul>

  <div class="tab-content tab-content-environmental active" id="tab-content-environmental"></div>
  <div class="tab-content tab-content-hakka" id="tab-content-hakka"></div>

  </section>

  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/scripts/common/base.html';?>
  <?php $specialRestaurant = true; require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
  <script src="/scripts/roster.js?v=<?=$VERSION?>"></script>
</body>
</html>