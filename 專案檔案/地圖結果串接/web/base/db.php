<?php if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) { header('Location: /home'); exit;} ?>

<?php
  $pwdFile = $_SERVER['DOCUMENT_ROOT'].'/base/pwd.txt';
  // 建立 MySQL 資料庫連接
  $conn = new mysqli("localhost", "root", trim(file_get_contents($pwdFile)), "mapdb");
  if ($conn->connect_error) die("無法開啟 MySQL 資料庫連接: " . $conn->connect_error);

  $markOptions = [
    '環保' => ['cardType' => 'store-card-environment', 'buttonClass' => 'btn-outline-green', 'tagIcon' => '♻️', 'tagName' => ' (♻️環保)'],
    '客家' => ['cardType' => 'store-card-culture', 'buttonClass' => 'btn-outline-orange', 'tagIcon' => '⭐', 'tagName' => ' (⭐客家)']
  ];

  $serviceMap = [
    'parking' => '停車場',
    'wheelchair_accessible' => '無障礙',
    'vegetarian' => '素食',
    'healthy' => '健康',
    'kids_friendly' => '兒童',
    'pets_friendly' => '狗',
    'gender_friendly' => '性別',
    'delivery' => '外送',
    'takeaway' => '外帶',
    'dine_in' => '內用',
    'breakfast' => '早餐',
    'brunch' => '早午餐',
    'lunch' => '午餐',
    'dinner' => '晚餐',
    'reservation' => '訂位',
    'group_friendly' => '團體',
    'family_friendly' => '闔家',
    'toilet' => '洗手間',
    'wifi' => 'Wi-Fi',
    'cash' => '現金',
    'credit_card' => '信用卡',
    'debit_card' => '金融卡',
    'mobile_payment' => '行動支付'
];