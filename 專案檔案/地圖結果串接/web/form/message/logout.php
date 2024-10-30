<?php
  $formName = 'logout';
?>
<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="<?=$formName?>Modal" tabindex="-1" aria-labelledby="<?=$formName?>ModalLabel" aria-hidden="true">
  <div class="modal-dialog form-dialog modal-dialog-centered" style="justify-content:center;">
    <div class="modal-content" style="width:auto">
      <div class="modal-body">
        <form novalidate style="margin-top:15px;margin-left:20px;margin-right:20px;">
          <div style="display: flex; align-items: center;">
            <!-- <img src="/images/.png" alt="Image" style="height:50px;margin-right:15px;margin-bottom:10px;"> -->
            <i class="fi fi-br-sign-out-alt" style="font-size: 40px; margin-right: 15px; color: #0078D7;"></i>
            <div>
              <h2>會員登出</h2>
              <p>您會遺失所有未保存的資料</p>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-solid-gray keydown-by-esc" id="<?=$formName?>-cancel-button" onclick="cancelModal()">取消</button>
        <button type="button" class="btn btn-solid-windows-blue keydown-by-enter" id="<?=$formName?>-confirm-button" onclick="logoutRequest()">登出</button>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById('<?=$formName?>Modal').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
      document.getElementById('<?=$formName?>Modal').querySelector('.keydown-by-enter').click();
    } else if (event.key === 'Escape') {
      document.getElementById('<?=$formName?>Modal').querySelector('.keydown-by-esc').click();
    }
  });

  function logoutRequest() {
    fetch('/member/handler/logout.php', {
      method: 'POST',
      credentials: 'same-origin',
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          cancelModal();
          generateLoadingOverlay();
          localStorage.setItem('justLoggedOut', 'true');
          setTimeout(function() {
            window.location.reload(true);
          }, LOADING_DURATION);
        }
      })
      .catch(() => {showAlert('red', '登出失敗，請稍後再試');});
  }
</script>