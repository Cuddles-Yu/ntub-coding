<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="signupModal1" aria-hidden="true" tabindex="-1" aria-labelledby="signupModal1Label" >
  <div class="modal-dialog form-dialog modal-dialog-centered login-modal">
    <div class="modal-content">
      <div class="progress signup-progress">
        <div class="progress-bar signup-progress-bar" role="progressbar" style="width:33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100">
          目前進度: 1 / 3
        </div>
      </div>
      <div class="modal-body login-modal-body" style="height:450px">
        <h2 class="form-h2">會員註冊</h2>
        <div class='form-message-popup' id="signup-message" style="display:none">
          <div id="signupError" class="form-error-message-popup" style="display:block; text-align:center"></div>
        </div>
        <form novalidate>
          <div>
            <input type='email' id='signup-email' class='form-input-popup signup-input' placeholder='帳號（電子郵件）' autocomplete="email" required>
          </div>
          <div>
            <input type='text' id='signup-name' class='form-input-popup signup-input' placeholder='使用者名稱' autocomplete="name" required>
          </div>
          <div style="margin-bottom: -25px">
            <input type='password' id="signup-password" class='form-input-popup password-input signup-input' placeholder='密碼' required>
            <img src="images/password-hide.png" alt="password" id="signup-toggle-password" class="toggle-password">
          </div>
          <div style="margin-bottom: -25px">
            <input type='password' id="signup-check-password" class='form-input-popup password-input signup-input' placeholder='確認密碼' required>
            <img src="images/password-hide.png" alt="password" id="signup-toggle-check-password" class="toggle-password">
          </div>
          <div class="password-requirements" id="password-requirements" style="margin-top:10px;">
            <ul>
              <li id="length-condition" style="color:red;">密碼長度介於8-20個字元</li>
              <li id="uppercase-lowercase-condition" style="color:red;">必須包含至少一個大寫字母和一個小寫字母</li>
              <li id="number-condition" style="color:red;">必須包含至少一個數字</li>
              <!-- <li id="special-char-condition" style="color:red;">必須包含至少一個特殊字元（如：!@#$%^&*）</li> -->
            </ul>
          </div>
          <div class="checkbox-set">
            <input type="checkbox" id="signup-consent" name="signup-consent" style="cursor:pointer;">
            <label for="signup-consent" class="checkbox-set-text">我已詳細閱讀並同意服務條款</label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-solid-gray" id="signup1-cancel-button" onclick="cancelSignup('signupModal1')">取消</button>
        <button type="button" class="btn btn-primary" id="signup1-next-button" onclick="accountVerifyRequest()">下一步</button>
      </div>
    </div>
  </div>
</div>