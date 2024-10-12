<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="modifyPasswordModal" tabindex="-1" aria-labelledby="modifyPasswordLabel" aria-hidden="true">
  <div class="modal-dialog form-dialog modal-dialog-centered login-modal">
    <div class="modal-content">
      <div class="modal-body login-modal-body" style="height:273px;">
        <h2 class="form-h2">修改密碼</h2>
        <div class='form-message-popup' id="login-message" style="display:none">
          <div id="loginError" class="form-error-message-popup" style="display:block; text-align:center"></div>
        </div>
        <form novalidate>
          <div style="margin-bottom: -25px">
            <input type='password' id='old-password' class='form-input-popup login-input' placeholder='舊密碼' autocomplete="current-password" required>
            <img src="/images/password-hide.png" alt="password" id="old-toggle-password" class="toggle-password">
          </div>
          <div style="margin-bottom: -25px">
            <input type='password' id='new-password' class='form-input-popup login-input' placeholder='新密碼' autocomplete="current-password" required>
            <img src="/images/password-hide.png" alt="password" id="new-toggle-password" class="toggle-password">
          </div>
          <div style="margin-bottom: -25px">
            <input type='password' id='check-password' class='form-input-popup login-input' placeholder='確認新密碼' autocomplete="current-password" required>
            <img src="/images/password-hide.png" alt="password" id="check-toggle-password" class="toggle-password">
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