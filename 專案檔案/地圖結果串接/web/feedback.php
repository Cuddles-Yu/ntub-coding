<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
?>

<!doctype html>
<html lang="zh-TW">
<head>
  <meta charset="utf-8" />
  <title>使用回饋 - 評星宇宙</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.4.2/uicons-solid-rounded/css/uicons-solid-rounded.css'>  
  <link rel="stylesheet" href="/styles/feedback.css" />
</head>

<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <section class="primary-conent">
    <div style="display:grid;margin-top:70px;">
      <form novalidate style="width:600px;place-self:center;display:grid">
        <p>若您有任何建議或想法，您可以透過這個表單向開發團隊提出！</p>
        <p>◆ 將回饋敘述的越詳細並附上影像輔助說明，越容易瞭解您的想法並推出更新</p>
        <p>◆ 向開發團隊提出建議表示您同意能以任何方式 (包含但不限於重製或修改) 使用該建議作為功能，且您的建議與最終推出的更新可能有些許落差</p>
        <p>◆ 遇到問題了嗎？提供相關截圖或說明，協助快速排查問題的原因</p>

        <label for="email">電子郵件 *</label>
        <input type="email" id="email" name="email" placeholder="您的電子郵件" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">

        <label for="suggestions">您的建議</label>
        <textarea id="suggestions" name="suggestions" placeholder="您的建議" style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; height: 100px; resize: none;"></textarea>

        <label for="thoughts">想對開發團隊說的話</label>
        <textarea id="thoughts" name="thoughts" placeholder="想說的話" style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; height: 100px; resize: none;"></textarea>

        <button type="button" id="submit-btn" style="width: 100%; padding: 10px; background-color: #4285f4; border: none; color: white; font-size: 16px; cursor: pointer; border-radius: 5px;">提交</button>
      </form>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
  <script src="https://kit.fontawesome.com/876a36192d.js" crossorigin="anonymous"></script>
  <script src="/scripts/feedback.js"></script>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
</body>
</html>