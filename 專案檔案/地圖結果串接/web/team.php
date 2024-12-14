<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
?>

<!doctype html>
<html lang="zh-TW">
<head>
  <meta charset="utf-8" />
  <title>成員介紹 - 評星宇宙</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="/styles/common/base.css?v=<?=$VERSION?>">
  <link rel="stylesheet" href="/styles/team.css?v=<?=$VERSION?>" />
</head>

<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <section class="main-content main-container">

    <div class="member-group">
      <div class="member-card">
        <h4 class="member-title">組長</h4>
        <img class="member-picture" src="images/member1.png">
        <h2 class="member-name">余奕博</h2>
        <div class="email-group">
          <img class="arrow" src="images/list-point.png">
          <div class="text-group">
            <h6 class="email-title">Email</h6>
            <h6 class="email">11236018@ntub.edu.tw</h6>
          </div>
        </div>
      </div>
      <div class="member-card">
        <h4 class="member-title">組員</h4>
        <img class="member-picture" src="images/member2.png">
        <h2 class="member-name">邱綺琳</h2>
        <div class="email-group">
          <img class="arrow" src="images/list-point.png">
          <div class="text-group">
            <h6 class="email-title">Email</h6>
            <h6 class="email">11236019@ntub.edu.tw</h6>
          </div>
        </div>
      </div>
      <div class="member-card">
        <h4 class="member-title">組員</h4>
        <img class="member-picture" src="images/member3.png">
        <h2 class="member-name">鄧惠中</h2>
        <div class="email-group">
          <img class="arrow" src="images/list-point.png">
          <div class="text-group">
            <h6 class="email-title">Email</h6>
            <h6 class="email">11236007@ntub.edu.tw</h6>
          </div>
        </div>
      </div>
      <div class="member-card">
        <h4 class="member-title">組員</h4>
        <img class="member-picture" src="images/member4.png">
        <h2 class="member-name">陳彥瑾</h2>
        <div class="email-group">
          <img class="arrow" src="images/list-point.png">
          <div class="text-group">
            <h6 class="email-title">Email</h6>
            <h6 class="email">11236037@ntub.edu.tw</h6>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/scripts/common/base.html';?>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
  <script src="/scripts/team.js?v=<?=$VERSION?>"></script>
</body>

</html>