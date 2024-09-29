<?php
  $pwdFile = $_SERVER['DOCUMENT_ROOT'].'/base/pwd.txt';
  // 建立 MySQL 資料庫連接
  $conn = new mysqli("localhost", "root", trim(file_get_contents($pwdFile)), "mapdb");
  if ($conn->connect_error) die("無法開啟 MySQL 資料庫連接: " . $conn->connect_error);