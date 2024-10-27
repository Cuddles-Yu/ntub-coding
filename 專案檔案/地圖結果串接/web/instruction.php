<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
?>

<!doctype html>
<html lang="zh-TW">
<head>
  <meta charset="utf-8" />
  <title>使用說明 - 評星宇宙</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="/styles/common/base.css">
  <link rel="stylesheet" href="/styles/instruction.css" />
</head>

<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <section class="primary-conent">
    <div style="display:grid;margin-top:75px;">
      <iframe class="pdf-container" src="/pdf/instruction.pdf#zoom=page-width" style="width:80%;height:75vh;justify-self:center;"></iframe>
    </div>
  </section>

  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/scripts/common/base.html';?>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
  <script src="/scripts/instruction.js" defer></script>
</body>
</html>