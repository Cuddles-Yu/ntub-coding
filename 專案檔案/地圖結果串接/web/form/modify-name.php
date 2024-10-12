<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="modifyNameModal" tabindex="-1" aria-labelledby="modifyNameLabel" aria-hidden="true">
  <div class="modal-dialog form-dialog modal-dialog-centered login-modal">
    <div class="modal-content">
      <div class="modal-body login-modal-body" style="height:180px;">
        <h2 class="form-h2">修改名稱</h2>
        <div class='form-message-popup' id="login-message" style="display:none">
          <div id="loginError" class="form-error-message-popup" style="display:block; text-align:center"></div>
        </div>
        <form novalidate>
          <div style="margin-bottom: -25px">
            <input type='text' id='new-name' class='form-input-popup login-input' placeholder='新名稱' autocomplete="name" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-solid-gray" id="login-cancel-button" onclick="cancelModal()">取消</button>
        <button type="button" class="btn btn-solid-windows-blue" id="login-submit-button" onclick="cancelModal()">修改</button>
      </div>
    </div>
  </div>
</div>