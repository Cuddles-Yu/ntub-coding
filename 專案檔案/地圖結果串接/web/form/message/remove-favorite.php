<?php
  $formName = 'removeFavorite';
?>
<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="<?=$formName?>Modal" tabindex="-1" aria-labelledby="<?=$formName?>ModalLabel" aria-hidden="true">
  <div class="modal-dialog form-dialog modal-dialog-centered" style="justify-content:center;">
    <div class="modal-content" style="width:auto">
      <div class="modal-body">
        <form novalidate style="margin-top:15px;margin-left:20px;margin-right:20px;">
          <div style="display: flex; align-items: center;">
            <!-- <img src="/images/.png" alt="Image" style="height:50px;margin-right:15px;margin-bottom:10px;"> -->
            <i class="fi fi-sr-trash" style="font-size: 40px; margin-right: 15px; color: red;"></i>
            <div>
              <h2>刪除收藏</h2>
              <p>按下刪除後將無法進行還原</p>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-solid-gray keydown-by-esc" id="remove-favorite-cancel-button" data-bs-dismiss="modal" onclick="removeTargetFavorite()">取消</button>
        <button type="button" class="btn btn-solid-red keydown-by-enter" id="remove-favorite-confirm-button" data-bs-dismiss="modal" onclick="removeFavorite()">刪除</button>
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

  function removeFavorite() {
    const targetFavorite = document.querySelector('.favorite-target');
    targetId = targetFavorite.getAttribute('data-id');
    if (!targetId) {
      paramAlert();
      return;
    }
    const formData = new FormData();
    formData.set('id', targetId);
    fetch('/member/handler/remove-favorite.php', {
      method: 'POST',
      credentials: 'same-origin',
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showAlert('orange', data.message);
          targetFavorite.remove();
          checkFavorite();
        } else {
          showAlert('red', data.message);
        }
      })
      .catch(() => {
        exceptionAlert('移除收藏餐廳');
    });
  }

  function removeTargetFavorite() {
  for (const row of document.querySelectorAll('.favorite-target')) {
    row.classList.remove('favorite-target');
  }
}
</script>