<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
?>

<!doctype html>
<html lang="zh-TW">
<head>
  <meta charset="utf-8" />
  <title>成員介紹 - 評星宇宙</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-solid-rounded/css/uicons-solid-rounded.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-bold-rounded/css/uicons-bold-rounded.css'>
  <link rel="stylesheet" href="/styles/team.css" />
</head>

<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <section class="primary-conent">

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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
  <script src="https://kit.fontawesome.com/876a36192d.js" crossorigin="anonymous"></script>
  <script src="/scripts/team.js"></script>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
</body>

</html>