<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="signupModal3" aria-hidden="true" tabindex="-1" aria-labelledby="signupModal3Label">
  <div class="modal-dialog form-dialog modal-dialog-centered modal-dialog-scrollable login-modal">
    <div class="modal-content">
      <div class="progress signup-progress">
        <div class="progress-bar signup-progress-bar" role="progressbar" style="width:100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
          目前進度: 3 / 3
        </div>
      </div>
      <div class="modal-body login-modal-body" style="height:450px">
        <h2 class="form-h2-title">權重設定</h2>
        <p class="signup-explain">為了計算您客製化的餐廳分數，請依據您的需求調整下列四個項目的權重 [您可以隨時進行設定及修改]</p>
        <div class="slider-container">
          <label for="signup-atmosphere"><?=$_ATMOSPHERE?></label>
          <input type="range" class="slider-bar" id="signup-atmosphere" name="signup-atmosphere" min="0" max="100" value="50" oninput="updateLabelValue('signup-atmosphere')">
          <span id="signup-atmosphere-value">50</span>
        </div>
        <div class="slider-container">
          <label for="signup-product"><?=$_PRODUCT?></label>
          <input type="range" class="slider-bar" id="signup-product" name="signup-product" min="0" max="100" value="50" oninput="updateLabelValue('signup-product')">
          <span id="signup-product-value">50</span>
        </div>
        <div class="slider-container">
          <label for="signup-service"><?=$_SERVICE?></label>
          <input type="range" class="slider-bar" id="signup-service" name="signup-service" min="0" max="100" value="50" oninput="updateLabelValue('signup-service')">
          <span id="signup-service-value">50</span>
        </div>
        <div class="slider-container">
          <label for="signup-price"><?=$_PRICE?></label>
          <input type="range" class="slider-bar" id="signup-price" name="signup-price" min="0" max="100" value="50" oninput="updateLabelValue('signup-price')">
          <span id="signup-price-value">50</span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-solid-gray" id="signup3-cancel-button" onclick="cancelSignup('signupModal3')">取消</button>
        <button type="button" class="btn btn-solid-gray" id="signup3-previus-button" data-bs-target="#signupModal2" data-bs-toggle="modal">上一步</button>
        <button type="button" class="btn btn-primary" id="signup-submit-button" onclick="signupRequest()">註冊</butto>
      </div>
    </div>
  </div>
</div>