<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="signupModal2" aria-hidden="true" tabindex="-1" aria-labelledby="signupModal2Label">
  <div class="modal-dialog form-dialog modal-dialog-centered modal-dialog-scrollable login-modal">
  <div class="modal-content">
      <div class="progress signup-progress">
        <div class="progress-bar signup-progress-bar" role="progressbar" style="width:66%;" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100">
          目前進度: 2 / 3
        </div>
      </div>
      <h2 class="form-h2-title" style="padding-top:20px;">偏好設定</h2>
      <p class="signup-explain" style="margin-top:0;padding-left:20px;padding-right:20px;">為了提供您客製化的推薦餐廳，我們需要瞭解您的具體需求 [您可以隨時進行設定及修改]</p>
      <div class="modal-body login-modal-body" style="height:322px;padding-top:0;padding-right:10px;">
        <p class="checkbox-title">個人需求</p>
        <div class="checkbox-container">
          <div class="checkbox-item">
            <input type="checkbox" id="signup-parking" value="">
            <label for="signup-parking">停車場</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-wheelchair-accessible" value="">
            <label for="signup-wheelchair-accessible">無障礙</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-vegetarian" value="">
            <label for="signup-vegetarian">素食料理</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-healthy" value="">
            <label for="signup-healthy">健康料理</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-kids-friendly" value="">
            <label for="signup-kids-friendly">兒童友善</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-pets-friendly" value="">
            <label for="signup-pets-friendly">寵物友善</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-gender-friendly" value="">
            <label for="signup-gender-friendly">性別友善</label>
          </div>
        </div>
        <p class="checkbox-title">用餐方式</p>
        <div class="checkbox-container">
          <div class="checkbox-item">
            <input type="checkbox" id="signup-delivery" value="">
            <label for="signup-delivery">外送</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-takeaway" value="">
            <label for="signup-takeaway">外帶</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-dine-in" value="">
            <label for="signup-dine-in">內用</label>
          </div>
        </div>
        <p class="checkbox-title">用餐時段</p>
        <div class="checkbox-container">
          <div class="checkbox-item">
            <input type="checkbox" id="signup-breakfast" value="">
            <label for="signup-breakfast">早餐</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-brunch" value="">
            <label for="signup-brunch">早午餐</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-lunch" value="">
            <label for="signup-lunch">午餐</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-dinner" value="">
            <label for="signup-dinner">晚餐</label>
          </div>
        </div>
        <p class="checkbox-title">用餐規劃</p>
        <div class="checkbox-container">                
          <div class="checkbox-item">
            <input type="checkbox" id="signup-reservation" value="">
            <label for="signup-reservation">接受訂位</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-group-friendly" value="">
            <label for="signup-group-friendly">適合團體</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-family-friendly" value="">
            <label for="signup-family-friendly">適合家庭聚餐</label>
          </div>
        </div>
        <p class="checkbox-title">基礎設施</p>
        <div class="checkbox-container">
          <div class="checkbox-item">
            <input type="checkbox" id="signup-toilet" value="">
            <label for="signup-toilet">洗手間</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-wifi" value="">
            <label for="signup-wifi">無線網路</label>
          </div>
        </div>
        <p class="checkbox-title">付款方式</p>
        <div class="checkbox-container">
          <div class="checkbox-item">
            <input type="checkbox" id="signup-cash" value="">
            <label for="signup-cash">現金</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-credit-card" value="">
            <label for="signup-credit-card">信用卡</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-debit-card" value="">
            <label for="signup-debit-card">簽帳金融卡</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="signup-mobile-payment" value="">
            <label for="signup-mobile-payment">行動支付</label>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-solid-gray" id="signup2-cancel-button" onclick="cancelSignup('signupModal2')">取消</button>
        <button type="button" class="btn btn-solid-gray" id="signup2-previus-button" data-bs-target="#signupModal1" data-bs-toggle="modal">上一步</button>
        <button type="button" class="btn btn-primary" id="signup2-next-button" data-bs-target="#signupModal3" data-bs-toggle="modal">下一步</button>
      </div>
    </div>
  </div>
</div>