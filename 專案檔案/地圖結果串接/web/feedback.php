<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
?>

<!doctype html>
<html lang="zh-TW">

<head>
  <meta charset="utf-8" />
  <title>使用回饋 - 評星宇宙</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="styles/feedback.css" />
</head>

<body>

  <!-- ### 頁首 ### -->
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>


  <!-- ### 內容 ### -->
  <section class="primary-conent">
    <div style="display:grid;margin-top:70px;">
      <form novalidate style="width:600px;place-self:center;display:grid">
        <p>若您有任何建議或想法，您可以透過這個表單向開發團隊提出！</p>
        <p>◆ 將回饋敘述的越詳細並附上影像輔助說明，越容易瞭解您的想法並推出更新</p>
        <p>◆ 向開發團隊提出建議表示您同意能以任何方式 (包含但不限於重製或修改) 使用該建議作為功能，且您的建議與最終推出的更新可能有些許落差</p>
        <p>◆ 遇到問題了嗎？提供相關截圖或說明，協助快速排查問題的原因</p>

        <label for="email">電子郵件 *</label>
        <input type="email" id="email" name="email" placeholder="請輸入您的電子郵件" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">

        <label for="suggestions">您的建議</label>
        <textarea id="suggestions" name="suggestions" placeholder="請輸入您的建議" style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; height: 100px; resize: none;"></textarea>

        <label for="thoughts">想對開發團隊說的話</label>
        <textarea id="thoughts" name="thoughts" placeholder="想說的話" style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; height: 100px; resize: none;"></textarea>

        <button type="button" id="submit-btn" style="width: 100%; padding: 10px; background-color: #4285f4; border: none; color: white; font-size: 16px; cursor: pointer; border-radius: 5px;">提交</button>
      </form>
    </div>    
  </section>

  <!-- ### 頁尾 ### -->
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>

  <!-- 載入主程式 -->  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
      document.getElementById('submit-btn').addEventListener('click', function() {
          // 這裡放你的自訂送出處理邏輯
          alert('自訂送出處理！');
          // 例如，你可以使用 fetch 或 XMLHttpRequest 來手動處理表單送出
      });
      document.querySelectorAll('.feedback-menu').forEach(page => {
        page.removeAttribute('href');
        page.setAttribute('style', 'cursor:default;');
      });
      document.querySelectorAll('.feedback-page').forEach(page => {
        page.removeAttribute('href');
        page.setAttribute('style', 'cursor:default;');
      });
  </script>
  
</body>
</html>