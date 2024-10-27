<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
?>

<!doctype html>
<html lang="zh-TW">
<head>
  <meta charset="utf-8" />
  <title>使用回饋 - 評星宇宙</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="/styles/common/base.css">
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
        <!-- <p>◆ 遇到問題了嗎？提供相關說明，協助我們快速排查問題的原因</p> -->
        <input type="hidden" id="member_id" name="member_id" value="<?php echo $MEMBER_ID; ?>">
        <label for="email">
          <?php if ($SESSION_DATA->success): echo '會員電子郵件'; else: echo '電子郵件 <em style="color:red;font-weight:bold;">*</em>'; endif;?>
        </label>
        <input type="email" id="email" name="email" placeholder="您的電子郵件" <?php if ($SESSION_DATA->success): echo 'disabled value="'.$MEMBER_INFO['email'].'"'; else: echo 'required'; endif; ?>
          style="width:100%;padding:10px;margin-bottom:20px;border:1px solid #ddd;border-radius:5px;font-size:14px;">
        <label for="suggestions">
          您的建議 <em style="color:red;font-weight:bold;">*</em>
        </label>
        <textarea id="suggestions" name="suggestions" placeholder="您的建議" style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; height: 100px; resize: none;"></textarea>
        <label for="thoughts">想對開發團隊說的話</label>
        <textarea id="thoughts" name="thoughts" placeholder="想說的話" style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; height: 100px; resize: none;"></textarea>
        <button type="button" id="submit-btn" style="padding:10px;cursor:pointer;" class="btn btn-solid-windows-blue" onclick="sendFeedback();">提交</button>
      </form>
    </div>
  </section>

  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/scripts/common/base.html';?>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
  <script src="/scripts/feedback.js"></script>
</body>
</html>