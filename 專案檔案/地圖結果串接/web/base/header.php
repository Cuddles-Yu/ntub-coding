<?php
  if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    header('Location: /home');
    exit;
  }
?>
<link rel="stylesheet" href="/styles/header.css?v=<?=$VERSION?>" />
<link rel="stylesheet" href="/styles/elem/form.css?v=<?=$VERSION?>" />
<link rel="stylesheet" href="/styles/elem/solid-button.css?v=<?=$VERSION?>" />
<link rel="stylesheet" href="/styles/elem/outline-button.css?v=<?=$VERSION?>" />
<link rel="stylesheet" href="/styles/elem/alert.css?v=<?=$VERSION?>" />
<link rel="stylesheet" href="/styles/elem/store-card.css?v=<?=$VERSION?>" />

<div id="alert-box" class="alert"></div>
<header id="commentspace-header">
  <div id="web_name"></div>
  <div id="nav_menu1">
      <a class="link_text page-item home-page" href="/home" draggable="false">餐廳搜尋</a>
      <div class="vertical-line"></div>
      <a class="link_text page-item suggestion-page" href="/suggestion" draggable="false">餐廳推薦</a>
      <div class="vertical-line"></div>
      <a class="link_text page-item instruction-page" href="/instruction" draggable="false">使用說明</a>
      <div class="vertical-line"></div>
      <a class="link_text page-item feedback-page" href="/feedback" draggable="false">聯絡我們</a>
      <div class="vertical-line"></div>
      <a class="link_text page-item team-page" href="/team" draggable="false">成員介紹</a>
      <div class="vertical-line"></div>
      <a class="link_text page-item roster-page" href="/roster" draggable="false">餐廳名冊</a>
      <?php if($SESSION_DATA->success): ?>
        <div class="vertical-line"></div>
        <a class="link_text page-item member-page" href="/member/info?tab=info" draggable="false"><img src="/images/icon-crown.png" class="text-icon">會員專區</a>
      <?php endif; ?>
  </div>
  <?php if($SESSION_DATA->success): ?>
    <div class="btn-member">
      <img src="/images/icon-member.png" class="img-member">
    </div>
  <?php else: ?>
    <div id="login_button">
      <button id="login" type="button" data-bs-toggle="modal" data-bs-target="#loginModal">登入</button>
      <button id="signup" type="button" data-bs-toggle="modal" data-bs-target="#signupModal1">註冊</button>
    </div>
  <?php endif; ?>
  <button class="btn-more-pages">&#9776;</button>
  <div id="overlay"></div>
  <nav id="nav_menu2">
      <a class="link_text page-menu home-menu" href="/home" draggable="false">餐廳搜尋</a>
      <a class="link_text page-menu suggestion-menu" href="/suggestion" draggable="false">餐廳推薦</a>
      <a class="link_text page-menu instruction-menu" href="/instruction" draggable="false">使用說明</a>
      <a class="link_text page-menu feedback-menu" href="/feedback" draggable="false">聯絡我們</a>
      <a class="link_text page-menu team-menu" href="/team" draggable="false">成員介紹</a>
      <a class="link_text page-menu roster-menu" href="/roster" draggable="false">餐廳名冊</a>
      <?php if($SESSION_DATA->success): ?>
        <span class="display-after-login menu-separator"></span>
        <a class="link_text page-menu member-menu" onclick="toggleMenu()">會員專區<img src="/images/button-expand-arrow.png" class="text-icon" id="expand-arrow" draggable="false"></a>
        <div id="member-menu-items" class="page-menu" style="display: none;">
          <a class="display-after-login link_text close-menu" href="/member/info?tab=info" id="member-info-nav" draggable="false">基本資料</a>
          <a class="display-after-login link_text close-menu" href="/member/info?tab=preference" id="member-preference-nav" draggable="false">偏好設定</a>
          <a class="display-after-login link_text close-menu" href="/member/info?tab=weight" id="member-weight-nav" draggable="false">權重設定</a>
          <a class="display-after-login link_text close-menu" href="/member/info?tab=favorite" id="member-favorite-nav" draggable="false">收藏餐廳</a>
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
    <a draggable="false" href="/member/info?tab=info">基本資料</a>
    <a draggable="false" href="/member/info?tab=preference">偏好設定</a>
    <a draggable="false" href="/member/info?tab=weight">權重設定</a>
    <a draggable="false" href="/member/info?tab=favorite">收藏餐廳</a>
    <a data-bs-toggle="modal" data-bs-target="#logoutModal" onclick="closeMemberMenu()">登出</a>
  </div>
  <hr class="header-separator">
</header>

<?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/message/logout.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/message/check-navigation.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/message/external-link.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/message/cancel-signup.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/login.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/signup1.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/signup2.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/signup3.php'; ?>

<script src="/scripts/function.js?v=<?=$VERSION?>" defer></script>
<script src="/scripts/header.js?v=<?=$VERSION?>" defer></script>
<script src="/scripts/request.js?v=<?=$VERSION?>"></script>

<script>
  const SESSION_DATA = <?=json_encode($SESSION_DATA)?>;
  if (!SESSION_DATA.success && 'showMessage' in SESSION_DATA) showAlert('red', SESSION_DATA.showMessage);
</script>