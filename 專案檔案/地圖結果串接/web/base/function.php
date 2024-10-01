<?php
  function normalizeDistance($distance) {
    if ($distance < 1000.0) {
        return number_format($distance, 1) . ' 公尺';
    } else {
        return number_format($distance / 1000, 1) . ' 公里';
    }
  }

  function coloredEcho($color, $title, $text) {
    echo "<div style='color:$color; font-weight: bold; margin-bottom: 10px;'>[$title] $text</div>";
  }  

  function generateToken($length) {
    #排除容易混淆的字元
    $characters = 'abcdefghjkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  function bindPrepare($conn, $sql, $type, ...$params) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("SQL語法錯誤：" . $conn->error);
    $stmt->bind_param($type, ...$params);
    return $stmt;
  }