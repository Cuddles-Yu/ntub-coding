<?php
  $pageTitle = "無效的參數";
  $icon = "/images/website-error.png";
  $errorMessage = "無效的參數";
  $errorDescription = "您所尋找的餐廳編號不存在或已被移除。";
  $suggestion = "請檢查餐廳編號是否正確，或點擊下方按鈕返回首頁。";
  $link = "/home";
  $linkLabel = "返回首頁";

  require_once $_SERVER['DOCUMENT_ROOT'].'/struc/error-template.php';
