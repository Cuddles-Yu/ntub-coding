<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog form-dialog modal-dialog-centered login-modal">
    <div class="modal-content">
      <div class="modal-body login-modal-body" style="height:450px;">
        <h2 class="form-h2">會員登入</h2>
        <div class='form-message-popup' id="login-message" style="display:none">
          <div id="loginError" class="form-error-message-popup" style="display:block; text-align:center"></div>
        </div>
        <form novalidate>
          <div>
            <input type='email' id='login-email' class='form-input-popup login-input' placeholder='帳號（電子郵件）' autocomplete="email" required>
          </div>
          <div style="margin-bottom: -25px">
            <input type='password' id='login-password' class='form-input-popup login-input' placeholder='密碼' autocomplete="current-password" required>
            <img src="images/password-hide.png" alt="password" id="login-toggle-password" class="toggle-password">
          </div>
          <div class="checkbox-set">
            <input type="checkbox" id="remember" name="remember" style="cursor:pointer;">
            <label for="remember" class="checkbox-set-text">保持登入狀態</label>
            <!-- <div class="forget_pwd">忘記密碼？</div>-->
          </div>
          <div class="divider_container">
            <div class="divider"></div><p class="divider_text">or</p><div class="divider"></div>
          </div>
          <div class="login_area">
            <button type="button" class="btn btn-outline-secondary signup-button" data-bs-toggle="modal" data-bs-target="#signupModal1">
              <img src="images/logo-blue+.png" class="text-icon" alt="signup-google-icon"> 註冊新會員
            </button>
          </div>
          <!-- <div class="login_area">
            <button type="button" class="btn btn-outline-secondary signup-button signup-button-disabled" disabled>
              <img src="/images/icon-google.png" class="text-icon" alt="signup-google-icon"> Google登入 (尚未開放)
            </button>
          </div>-->
          <div class="agree-checkbox">ⓘ登入即表示您同意我們的服務條款</div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-solid-gray" id="login-cancel-button" onclick="cancelModal()">取消</button>
        <button type="button" class="btn btn-primary" id="login-submit-button" onclick="loginRequest()">登入</button>
      </div>
    </div>
  </div>
</div>