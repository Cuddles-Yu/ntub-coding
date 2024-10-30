<?php
  $formName = 'cancelSignup';
?>
<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="<?=$formName?>Modal" tabindex="-1" aria-labelledby="<?=$formName?>ModalLabel" aria-hidden="true">>
  <div class="modal-dialog form-dialog modal-dialog-centered" style="justify-content:center;">
    <div class="modal-content" style="width:auto">
      <div class="modal-body">
        <form novalidate style="margin-top:15px;margin-left:20px;margin-right:20px;">
          <div style="display: flex; align-items: center;">
            <!-- <img src="/images/.png" alt="Image" style="height:50px;margin-right:15px;margin-bottom:10px;"> -->
            <i class="fi fi-sr-document-circle-wrong" style="font-size: 40px; margin-right: 15px; color: #0078D7;"></i>
            <div>
              <h2>取消註冊</h2>
              <p>關閉流程後您會遺失所有已輸入的註冊資料</p>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-solid-gray keydown-by-esc" id="<?=$formName?>-cancel-button">取消</button>
        <button type="button" class="btn btn-solid-windows-blue keydown-by-enter" id="<?=$formName?>-confirm-button" onclick="cancelModal()">確定</button>
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
</script>