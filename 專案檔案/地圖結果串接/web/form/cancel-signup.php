<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="cancelSignupModal" tabindex="-1" aria-labelledby="cancelSignupModalLabel" aria-hidden="true">
  <div class="modal-dialog form-dialog modal-dialog-centered cancel-signup-modal" style="justify-content:center;">
    <div class="modal-content" style="width:auto">
      <div class="modal-body cancel-signup-modal-body">
        <form novalidate style="margin-top:15px;margin-left:20px;margin-right:20px;">         
          <h2>取消註冊</h2>
          <p>關閉流程後您會遺失所有已輸入的註冊資料</p>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-solid-gray" id="cancel-signup-cancel-button" onclick="restoreSignup()">取消</button>
        <button type="button" class="btn btn-primary" id="cancel-signup-confirm-button" onclick="cancelModal()">確定</button>
      </div>
    </div>
  </div>
</div>