<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
?>

<!doctype html>
<html lang="zh-TW">
<head>
  <meta charset="utf-8" />
  <title>使用說明 - 評星宇宙</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="/styles/common/base.css?v=<?=$VERSION?>">
  <link rel="stylesheet" href="/styles/instruction.css?v=<?=$VERSION?>" />
</head>

<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <section class="main-content">
    <div style="display:grid;height:100%;">
      <iframe class="pdf-container" src="/pdf/instruction.pdf#zoom=page-width" style="width:100%;height:100%;"></iframe>
    </div>
  </section>

  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/scripts/common/base.html';?>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
  <script src="/scripts/instruction.js?v=<?=$VERSION?>" defer></script>
</body>
</html>