<?php if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) { header('Location: /home'); exit;} ?>

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
    $stmt->bind_param($type, ...$params);
    return $stmt;
  }

  function transformToPreference($item) {
    global $MEMBER_INFO;
    $item = str_replace('condition-', '', $item);
    $item = str_replace('signup-', '', $item);
    $item = str_replace('member-', '', $item);
    $item = str_replace('-', '_', $item);
    return $MEMBER_INFO[$item] ? 'checked' : '';
  }