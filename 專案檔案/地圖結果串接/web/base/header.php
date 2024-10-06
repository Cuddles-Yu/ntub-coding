<link rel="stylesheet" href="/styles/header.css" />
<link rel="stylesheet" href="/styles/form.css" />
<link rel="stylesheet" href="/styles/common.css" />

<header>

  <div id="alert-box" class="alert"></div>
  <div id="web_name"></div>

  <div id="nav_menu1">
      <a class="link_text home-page page-item" href="../home">網站首頁</a>
      <div class="vertical-line"></div>
      <a class="link_text use-page page-item" style="color:lightgray;cursor:default;">使用說明</a> <!-- !!!需要更新!!! -->      
      <div class="vertical-line"></div>
      <a class="link_text feedback-page page-item" href="../feedback">使用回饋</a> <!-- href="https://forms.gle/t7CfCTF7phHKU9yJ8" target="_blank" -->
      <div class="vertical-line"></div>
      <a class="link_text team-page page-item" href="../team">成員介紹</a>
      <?php if($SESSION_DATA->success): ?>
        <div class="vertical-line"></div>
        <a class="link_text member-page page-item" href="../member/info">👑會員專區</a>
      <?php endif; ?>
  </div>

  <div id="user_icon" <?php if($SESSION_DATA->success): ?>style="display:flex;"<?php endif; ?>>
    <img src="/images/icon-member.jpg" id="user_icon_logo">
  </div>  
  <div id="login_button" <?php if($SESSION_DATA->success): ?>style="display:none;"<?php endif; ?>>
    <button id="login" type="button" data-bs-toggle="modal" data-bs-target="#loginModal">登入</button>
    <button id="signup" type="button" data-bs-toggle="modal" data-bs-target="#signupModal1">註冊</button>
  </div>  

  <button id="hamburger_btn" class="hamburger">&#9776;</button>
  <div id="overlay"></div>
  <nav id="nav_menu2">
      <a class="link_text page-menu home-menu" href="../home">網站首頁</a>
      <a class="link_text page-menu use-menu" style="color:lightgray;cursor:default;">使用說明</a>
      <a class="link_text page-menu feedback-menu" href="../feedback">使用回饋</a>
      <a class="link_text page-menu team-menu" href="../team">成員介紹</a> 
      
      <span class="display-before-login menu-separator"></span>
      <a class="display-before-login link_text close-menu" id="login-nav" data-bs-toggle="modal" data-bs-target="#loginModal">登入</a>
      <a class="display-before-login link_text close-menu" id="signup-nav" data-bs-toggle="modal" data-bs-target="#signupModal1">註冊</a>

      <span class="display-after-login menu-separator"></span>
      <a class="display-after-login link_text close-menu" id="member-info-nav">基本資料</a>
      <a class="display-after-login link_text close-menu" id="member-preference-nav">偏好設定</a>
      <a class="display-after-login link_text close-menu" id="member-weight-nav">權重設定</a>
      <a class="display-after-login link_text close-menu" id="member-favorite-nav">收藏商家</a>
      <span class="display-after-login menu-separator"></span>
      <a class="link_text close-menu" id="member-logout-nav">登出</a>
  </nav>
    
  <div id="dropdownMenu" class="dropdown-menu">
    <a href="../member/info?tab=info">基本資料</a>
    <a href="../member/info?tab=preference">偏好設定</a>
    <a href="../member/info?tab=weight">權重設定</a>
    <a href="../member/info?tab=favorite">收藏商家</a>
    <a href="" data-bs-toggle="modal" data-bs-target="#logoutModal">登出</a>
  </div>
  <hr>

</header>

<!-- ### 登出提示 ### -->
<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered logout-modal" style="justify-content:center;">
    <div class="modal-content" style="width:auto">
      <div class="modal-body logout-modal-body">

        <form novalidate style="margin-top:15px;margin-left:20px;margin-right:20px;">         
          <h2>會員登出</h2>
          <p>您會遺失所有未保存的資料，並返回主頁</p>
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="logout-cancel-button" data-bs-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary" id="logout-confirm-button" onclick="logoutRequest()">登出</button>
      </div>
    </div>
  </div>
</div>

<!-- ### 取消註冊提示 ### -->
<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="cancelSignupModal" tabindex="-1" aria-labelledby="cancelSignupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered cancel-signup-modal" style="justify-content:center;">
    <div class="modal-content" style="width:auto">
      <div class="modal-body cancel-signup-modal-body">

        <form novalidate style="margin-top:15px;margin-left:20px;margin-right:20px;">         
          <h2>取消註冊</h2>
          <p>關閉流程後您會遺失所有已輸入的註冊資料</p>
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="cancel-signup-cancel-button" onclick="restoreSignup()">取消</button>
        <button type="button" class="btn btn-primary" id="cancel-signup-confirm-button" onclick="cancelModal()">確定</button>
      </div>
    </div>
  </div>
</div>

<!-- ### 登入 ### -->
<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered login-modal">
    <div class="modal-content">
      <div class="modal-body login-modal-body" style="height:450px;">
        <h2 class="form-h2">會員登入</h2>
        <div class='form-message-popup' id="login-message" style="display:none">
          <div id="loginError" class="form-error-message-popup" style="display:block; text-align:center"></div>
        </div>

        <!-- ### 填寫 ### -->
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
            <!-- <div class="forget_pwd">忘記密碼？</div> -->
          </div>
          <div class="divider_container">
            <div class="divider"></div><p class="divider_text">or</p><div class="divider"></div>
          </div>
          <div class="login_area">
            <button type="button" class="btn btn-outline-secondary signup-button" data-bs-toggle="modal" data-bs-target="#signupModal1">
              <img src="images/logo-blue+.png" class="text-icon" alt="signup-google-icon"> 註冊新會員
            </button>                  
          </div> 
          <div class="login_area">
            <button type="button" class="btn btn-outline-secondary signup-button signup-button-disabled" disabled>
              <img src="/images/icon-google.png" class="text-icon" alt="signup-google-icon"> Google登入 (尚未開放)
            </button>                  
          </div>           
          <div class="agree-checkbox">ⓘ登入即表示您同意我們的服務條款</div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="login-cancel-button" onclick="cancelModal()">取消</button>
        <button type="button" class="btn btn-primary" id="login-submit-button" onclick="loginRequest()">登入</button>
      </div>
    </div>
  </div>
</div>

<!-- ### 註冊(1/3) ### -->
<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="signupModal1" tabindex="-1" aria-labelledby="signupModal1Label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered login-modal">        
    <div class="modal-content">
      <div class="progress signup-progress">
        <div class="progress-bar signup-progress-bar" role="progressbar" style="width:33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100">
          目前進度: 1 / 3
        </div>
      </div>
      <div class="modal-body login-modal-body" style="height:350px">        
        <h2 class="form-h2">會員註冊</h2>
        <div class='form-message-popup' id="signup-message" style="display:none">
          <div id="signupError" class="form-error-message-popup" style="display:block; text-align:center"></div>
        </div>

        <!-- ### 填寫 ### -->
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
          <div class="checkbox-set">
            <input type="checkbox" id="signup-consent" name="signup-consent" style="cursor:pointer;">
            <label for="signup-consent" class="checkbox-set-text">我已詳細閱讀並同意服務條款</label>
          </div>
        </form>

      </div>
      <div class="modal-footer"> <!-- 先不去判斷輸入內容正確性 -->
        <button type="button" class="btn btn-secondary" id="signup1-cancel-button" onclick="cancelSignup('signupModal1')">取消</button>
        <button type="button" class="btn btn-primary" id="signup1-next-button" onclick="accountVerifyRequest()">下一步</button>
      </div>
    </div>
  </div>
</div>

<!-- ### 註冊(2/3) ### -->
<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="signupModal2" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalToggleLabel2">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable login-modal">
  <div class="modal-content">
      <div class="progress signup-progress">
        <div class="progress-bar signup-progress-bar" role="progressbar" style="width:66%;" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100">
          目前進度: 2 / 3
        </div>
      </div>
      <h2 class="form-h2-title" style="padding-right:20px;padding-top:20px;">偏好設定</h2>        
      <p class="signup-explain" style="margin-top:0;padding-left:20px;padding-right:20px;">為了提供您客製化的推薦餐廳，我們需要瞭解您的具體需求 [您可以隨時進行設定及修改]</p>
      <div class="modal-body login-modal-body" style="height:350px;padding-top:0;padding-right:10px;">
        
        <!-- ### 填寫 ### -->
        <div class="input-group input-group-sm mb-3" style="width:230px;">
          <p class="checkbox-title">搜尋半徑</p>
          <input id="signup-search-radius-input" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" value="1500">
          <span class="input-group-text" id="inputGroup-sizing-sm">公尺</span>
        </div>

        <p class="checkbox-title">營業時間</p>
        <div class="checkbox-container">                
          <div class="checkbox-item">
            <input type="checkbox" id="signup-open-now" value="">
            <label for="signup-open-now">營業中</label>
          </div>                                      
        </div>
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
        <button type="button" class="btn btn-secondary" id="signup2-cancel-button" onclick="cancelSignup('signupModal2')">取消</button>
        <button type="button" class="btn btn-secondary" id="signup2-previus-button" data-bs-target="#signupModal1" data-bs-toggle="modal">上一步</button>
        <button type="button" class="btn btn-primary" id="signup2-next-button" data-bs-target="#signupModal3" data-bs-toggle="modal">下一步</button>
      </div>
    </div>
  </div>
</div>
<!-- ### 註冊(3/3) ### -->
<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="signupModal3" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalToggleLabel3">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable login-modal">
    <div class="modal-content">
      <div class="progress signup-progress">
        <div class="progress-bar signup-progress-bar" role="progressbar" style="width:100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
          目前進度: 3 / 3
        </div>
      </div>
      <div class="modal-body login-modal-body" style="height:350px">
        <h2 class="form-h2-title">權重設定</h2>
        <p class="signup-explain">為了計算您客製化的餐廳分數，請依據您的需求調整下列四個項目的權重 [您可以隨時進行設定及修改]</p>

        <!-- ### 填寫 ### -->        
        <div class="slider-container">
          <label for="signup-atmosphere">氛圍</label>
          <input type="range" class="slider-bar" id="signup-atmosphere" name="signup-atmosphere" min="0" max="100" value="50" oninput="updateLabelValue('signup-atmosphere')">
          <span id="signup-atmosphere-value">50</span>
        </div>        
        <div class="slider-container">
          <label for="signup-product">產品</label>
          <input type="range" class="slider-bar" id="signup-product" name="signup-product" min="0" max="100" value="50" oninput="updateLabelValue('signup-product')">
          <span id="signup-product-value">50</span>
        </div>        
        <div class="slider-container">
          <label for="signup-service">服務</label>
          <input type="range" class="slider-bar" id="signup-service" name="signup-service" min="0" max="100" value="50" oninput="updateLabelValue('signup-service')">
          <span id="signup-service-value">50</span>
        </div>        
        <div class="slider-container">
          <label for="signup-price">售價</label>
          <input type="range" class="slider-bar" id="signup-price" name="signup-price" min="0" max="100" value="50" oninput="updateLabelValue('signup-price')">
          <span id="signup-price-value">50</span>
        </div>

      </div>
      <div class="modal-footer">          
        <button type="button" class="btn btn-secondary" id="signup3-cancel-button" onclick="cancelSignup('signupModal3')">取消</button>
        <button type="button" class="btn btn-secondary" id="signup3-previus-button" data-bs-target="#signupModal2" data-bs-toggle="modal">上一步</button>
        <button type="button" class="btn btn-primary" id="signup-submit-button" onclick="signupRequest()">註冊</button>          
      </div>
    </div>
  </div>
</div>

<script src="/scripts/header.js"></script>
<script src="/scripts/request.js"></script>

<script>
  const SESSION_DATA = <?=json_encode($SESSION_DATA)?>;
  if (!SESSION_DATA.success && 'showMessage' in SESSION_DATA) showAlert('danger', SESSION_DATA.showMessage);
</script>