<?php 
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
?>

<!doctype html>
<html lang="zh-TW">
  
<head>
  <meta charset="utf-8" />
  <title>成員介紹 - 評星宇宙</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <meta name="keywords" content="評價, google map" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="styles/team-member.css" />
</head>

<body>

  <!-- ### 頁首 ### -->
  <?php require $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>
  

  <!-- ### 內容 ### -->
  <section class="primary-conent">

    <div class="member-group">  
      <div class="member-card">
        <h4 class="member-title">組長</h4>
        <img class="member-picture" src="images/member1.png">
        <h2 class="member-name">余奕博</h2>
        <div class="email-group">
          <img class="arrow" src="images/list-point.jpg">
          <div class="text-group">
            <h6 class="email-title">Email</h6>
            <h6 class="email">11236018@ntub.edu.tw</h6>
          </div>
        </div>
      </div>
      <div class="member-card">
        <h4 class="member-title">組員</h4>
        <img class="member-picture" src="images/member2.jpg">
        <h2 class="member-name">邱綺琳</h2>
        <div class="email-group">
          <img class="arrow" src="images/list-point.jpg">
          <div class="text-group">
            <h6 class="email-title">Email</h6>
            <h6 class="email">11236019@ntub.edu.tw</h6>
          </div>
        </div>
      </div>
      <div class="member-card">
        <h4 class="member-title">組員</h4>
        <img class="member-picture" src="images/member3.jpg">
        <h2 class="member-name">鄧惠中</h2>
        <div class="email-group">
          <img class="arrow" src="images/list-point.jpg">
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
          <img class="arrow" src="images/list-point.jpg">
          <div class="text-group">
            <h6 class="email-title">Email</h6>
            <h6 class="email">11236037@ntub.edu.tw</h6>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- ### 頁尾 ### -->
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.querySelectorAll('.team-menu').forEach(page => {
      page.removeAttribute('href');
      page.setAttribute('style', 'cursor:default;');
    });
    document.querySelectorAll('.team-page').forEach(page => {
      page.removeAttribute('href');
      page.setAttribute('style', 'cursor:default;');
    });
  </script>

</body>

</html>