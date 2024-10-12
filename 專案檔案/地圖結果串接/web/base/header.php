<?php if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) { header('Location: /home'); exit;} ?>

<link rel="stylesheet" href="/styles/header.css" />
<link rel="stylesheet" href="/styles/elem/form.css" />
<link rel="stylesheet" href="/styles/common.css" />
<link rel="stylesheet" href="/styles/elem/solid-button.css" />
<link rel="stylesheet" href="/styles/elem/outline-button.css" />
<link rel="stylesheet" href="/styles/elem/alert.css" />
<link rel="stylesheet" href="/styles/elem/store-card.css" />

<div id="alert-box" class="alert"></div>

<header>
  
  <div id="web_name"></div>

  <div id="nav_menu1">
      <a class="link_text page-item home-page" href="/home">餐廳搜尋</a><!-- <img src="/images/icon-member.png" class="text-icon"> -->
      <div class="vertical-line"></div>
      <a class="link_text page-item suggestion-page" href="/suggestion">餐廳推薦</a>
      <div class="vertical-line"></div>
      <a class="link_text page-item use-page" style="color:lightgray;cursor:default;">使用說明</a><!-- !!!需要更新!!! -->      
      <div class="vertical-line"></div>
      <a class="link_text page-item feedback-page" href="/feedback">使用回饋</a><!-- href="https://forms.gle/t7CfCTF7phHKU9yJ8" target="_blank" -->
      <div class="vertical-line"></div>
      <a class="link_text page-item team-page" href="/team">成員介紹</a>
      <?php if($SESSION_DATA->success): ?>
        <div class="vertical-line"></div>
        <a class="link_text page-item member-page" href="/member/info?tab=info"><img src="/images/icon-crown.png" class="text-icon">會員專區</a>
      <?php endif; ?>
  </div>

  <div id="user_icon" <?php if($SESSION_DATA->success): ?>style="display:flex;"<?php endif; ?>>
    <img src="/images/icon-member.jpg" id="user_icon_logo">
  </div>
  <div id="login_button" <?php if($SESSION_DATA->success): ?>style="display:none;"<?php endif; ?>>
    <button id="login" type="button" data-bs-toggle="modal" data-bs-target="#loginModal">登入</button>
    <button id="signup" type="button" data-bs-toggle="modal" data-bs-target="#signupModal1">註冊</button>
  </div>

  <button id="hamburger_btn" class="hamburger">&#9776;</button>
  <div id="overlay"></div>
  <nav id="nav_menu2">
      <a class="link_text page-menu home-menu" href="/home">餐廳搜尋</a>
      <a class="link_text page-menu suggestion-menu" href="/suggestion">餐廳推薦</a>
      <a class="link_text page-menu use-menu" style="color:lightgray;cursor:default;">使用說明</a>
      <a class="link_text page-menu feedback-menu" href="/feedback">使用回饋</a>
      <a class="link_text page-menu team-menu" href="/team">成員介紹</a>
      <?php if($SESSION_DATA->success): ?>
        <span class="display-after-login menu-separator"></span>
        <a class="link_text page-menu member-menu" href="javascript:void(0);" onclick="toggleMenu()">會員專區<img src="/images/button-expand-arrow.png" class="text-icon" id="expand-arrow"></a>
        <div id="member-menu-items" class="page-menu" style="display: none;">
          <a class="display-after-login link_text close-menu" href="/member/info?tab=info" id="member-info-nav">基本資料</a>
          <a class="display-after-login link_text close-menu" href="/member/info?tab=preference" id="member-preference-nav">偏好設定</a>
          <a class="display-after-login link_text close-menu" href="/member/info?tab=weight" id="member-weight-nav">權重設定</a>
          <a class="display-after-login link_text close-menu" href="/member/info?tab=favorite" id="member-favorite-nav">收藏商家</a>
        </div>
        <span class="display-after-login menu-separator"></span>
        <a class="link_text close-menu" id="member-logout-nav" data-bs-toggle="modal" data-bs-target="#logoutModal">登出</a>
      <?php else: ?>
        <span class="display-before-login menu-separator"></span>
        <a class="display-before-login link_text close-menu" id="login-nav" data-bs-toggle="modal" data-bs-target="#loginModal">登入</a>
        <a class="display-before-login link_text close-menu" id="signup-nav" data-bs-toggle="modal" data-bs-target="#signupModal1">註冊</a>
      <?php endif; ?>
  </nav>
    
  <div id="dropdownMenu" class="dropdown-menu">
    <a href="/member/info?tab=info">基本資料</a>
    <a href="/member/info?tab=preference">偏好設定</a>
    <a href="/member/info?tab=weight">權重設定</a>
    <a href="/member/info?tab=favorite">收藏商家</a>
    <a href="" data-bs-toggle="modal" data-bs-target="#logoutModal">登出</a>
  </div>
  <hr class="header-separator">

</header>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/logout.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/login.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/cancel-signup.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/signup1.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/signup2.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/signup3.php'; ?>

<script src="/scripts/function.js"></script>
<script src="/scripts/header.js"></script>
<script src="/scripts/request.js"></script>

<script>
  const SESSION_DATA = <?=json_encode($SESSION_DATA)?>;
  if (!SESSION_DATA.success && 'showMessage' in SESSION_DATA) showAlert('red', SESSION_DATA.showMessage);
</script>