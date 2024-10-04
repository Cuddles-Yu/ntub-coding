<link rel="stylesheet" href="styles/header.css" />

<header>

  <div id="web_name">      
      <!-- 動態生成頁首圖示 -->
  </div>

  <div id="nav_menu1">
      <a class="link_text" href="../home">網站首頁</a>
      <div class="vertical-line"></div>
      <a class="link_text" style="color:lightgray;cursor:default;">使用說明</a> <!-- !!!需要更新!!! -->
      <div class="vertical-line"></div>
      <a class="link_text" href="https://forms.gle/t7CfCTF7phHKU9yJ8" target="_blank">使用回饋</a>
      <div class="vertical-line"></div>
      <a class="link_text" href="../team">成員介紹</a>
  </div>

  <div id="user_icon" href="../member/info" <?php if($SESSION_DATA->success): ?>style="display:flex;"<?php endif; ?>>
    <img src="images/icon-member.jpg" id="user_icon_logo">
  </div>  
  <div id="login_button" <?php if($SESSION_DATA->success): ?>style="display:none;"<?php endif; ?>>
    <button id="login" type="button" data-bs-toggle="modal" data-bs-target="#loginModal">登入</button>
    <button id="signup" type="button" data-bs-toggle="modal" data-bs-target="#signupModal">註冊</button>
  </div>  

  <button id="hamburger_btn" class="hamburger">&#9776;</button>
  <div id="overlay"></div>
  <nav id="nav_menu2">
      <a class="link_text" href="../home">網站首頁</a>
      <a class="link_text">使用說明</a>
      <a class="link_text" href="https://forms.gle/t7CfCTF7phHKU9yJ8" target="_blank">使用回饋</a>
      <a class="link_text" href="../team">成員介紹</a>
      <a class="link_text close-menu" id="login-nav" data-bs-toggle="modal" data-bs-target="#loginModal">登入</a>
      <a class="link_text close-menu" id="signup-nav" data-bs-toggle="modal" data-bs-target="#signupModal">註冊</a>
  </nav>
  <hr>

</header>

<!-- ### 登入 ### -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered login-modal">
    <div class="modal-content">
      <div class="modal-body login-modal-body">
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
            <img src="images/password-hide.png" alt="password" id="toggle-password">
          </div> 
          <div class="remember_container" style="margin-top: 10px">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember" id="remember_text">記住我</label>
            <div class="forget_pwd">忘記密碼？</div>
          </div>
          <div class="divider_container">
            <div class="divider"></div><p class="divider_text">or</p><div class="divider"></div>
          </div>
          <div class="login_area">
            <button type="button" class="btn btn-outline-secondary register-button">
              <img src="images/icon-google.png" class="text-icon" alt="google_icon" id="google_icon"> Google登入
            </button>                  
          </div>  
          <div class="login_area">
            <button type="button" class="btn btn-outline-secondary register-button" data-bs-toggle="modal" data-bs-target="#signupModal">
              <img src="images/logo-blue+.png" class="text-icon" alt="logo_icon" id="logo_icon" width="20px" height="20px"> 註冊新會員
            </button>                  
          </div>  
          <div class="agree">ⓘ登入即表示您同意我們的服務條款</div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="login-cancel-button" data-bs-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary" id="login-submit-button" onclick="loginRequest()">登入</button>
      </div>
    </div>
  </div>
</div>

<!-- ### 註冊(1/3) ### -->
<div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered login-modal">
    <div class="modal-content signup-body">
      <div class="modal-body login-modal-body">
        <h2 class="form-h2">會員註冊(1/3)</h2>
        <div class='form-message-popup' id="register1-message" style="display:none">
          <div id="loginError" class="form-error-message-popup" style="display:block; text-align:center"></div>
        </div>

        <!-- ### 填寫 ### -->
        <form novalidate>
          <div>
            <input type='text' id='register-name' class='form-input-popup' placeholder='使用者名稱' autocomplete="name" required>
          </div>
          <div>
            <input type='email' id='register-email' class='form-input-popup' placeholder='帳號（電子郵件）' autocomplete="email" required>
          </div>
          <div style="margin-bottom: -25px">
            <input type='password' id="register-password" class='form-input-popup password-input' placeholder='密碼' autocomplete="new-password" required>
            <img src="images/password-hide.png" alt="password" class="toggle-password">
          </div>
          <div style="margin-bottom: -25px">
            <input type='password' id="register-check-password" class='form-input-popup password-input' placeholder='確認密碼' autocomplete="new-password" required>
            <img src="images/password-hide.png" alt="password" class="toggle-password">
          </div>
          <div class="remember_container" style="margin-top: 10px">
            <input type="checkbox" id="consent" name="consent">
            <label for="consent" id="consent_check">我已詳細閱讀並同意服務條款</label>
          </div>
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="register1-cancel-button" data-bs-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary" id="register1-next-button" data-bs-target="#signupModal2" data-bs-toggle="modal">下一步</button>
      </div>
    </div>
  </div>
</div>

<!-- ### 註冊(2/3) ### -->
<div class="modal fade" id="signupModal2" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalToggleLabel2">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable login-modal">
    <div class="modal-content signup-body">
      <div class="modal-body login-modal-body">
        <h2 class="form-h2">會員註冊(2/3)</h2>
        <div class='form-message-popup' id="register2-message" style="display:none">
          <div id="loginError" class="form-error-message-popup" style="display:block; text-align:center"></div>
        </div>          
        <p class="signup-explain1">此設定是為了提供您個性化的推薦，所有資訊僅用於提升服務品質</p>
        <p class="signup-explain2">資訊可在會員資訊頁面進行設定及修改</p>

        <!-- ### 填寫 ### -->
        <div class="input-group input-group-sm mb-3">
          <p class="checkbox-title">搜尋半徑</p>
          <input id="register-search-radius-input" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
          <span class="input-group-text" id="inputGroup-sizing-sm">公尺</span>
        </div>
        <p class="checkbox-title">個人需求</p>
        <div class="checkbox-container">                
          <div class="checkbox-item">
            <input type="checkbox" id="register-parking" value="">
            <label for="parking">停車場</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-wheelchair-accessible" value="">
            <label for="wheelchair-accessible">無障礙</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-vegetarian" value="">
            <label for="vegetarian">素食料理</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-healthy" value="">
            <label for="healthy">健康料理</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-kids_friendly" value="">
            <label for="kids_friendly">兒童友善</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-pets_friendly" value="">
            <label for="pets_friendly">寵物友善</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-personal7" value="">
            <label for="personal7">性別友善</label>
          </div>                    
        </div>
        <p class="checkbox-title">用餐方式</p>
        <div class="checkbox-container">                
          <div class="checkbox-item">
            <input type="checkbox" id="register-delivery" value="">
            <label for="delivery">外送</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-takeaway" value="">
            <label for="takeaway">外帶</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-dine-in" value="">
            <label for="dine-in">內用</label>
          </div>                  
        </div>
        <p class="checkbox-title">用餐時段</p>
        <div class="checkbox-container">                
          <div class="checkbox-item">
            <input type="checkbox" id="register-breakfast" value="">
            <label for="breakfast">早餐</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-brunch" value="">
            <label for="brunch">早午餐</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-lunch" value="">
            <label for="lunch">午餐</label>
          </div>  
          <div class="checkbox-item">
            <input type="checkbox" id="register-dinner" value="">
            <label for="dinner">晚餐</label>
          </div>                  
        </div>
        <p class="checkbox-title">營業時間</p>
        <div class="checkbox-container">                
          <div class="checkbox-item">
            <input type="checkbox" id="register-BusinessHours" value="">
            <label for="BusinessHours">營業中</label>
          </div>                                      
        </div>
        <p class="checkbox-title">用餐氛圍</p>
        <div class="checkbox-container">                
          <div class="checkbox-item">
            <input type="checkbox" id="register-casual" value="">
            <label for="casual">氣氛悠閒</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-cosy" value="">
            <label for="cosy">環境舒適</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-ambiance3" value="">
            <label for="ambiance3">音樂演奏</label>
          </div>                                     
        </div>
        <p class="checkbox-title">用餐規劃</p>
        <div class="checkbox-container">                
          <div class="checkbox-item">
            <input type="checkbox" id="register-reservation" value="">
            <label for="reservation">接受訂位</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-group_friendly" value="">
            <label for="group_friendly">適合團體</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-family_friendly" value="">
            <label for="family_friendly">適合家庭聚餐</label>
          </div>                                     
        </div>
        <p class="checkbox-title">基礎設施</p>
        <div class="checkbox-container">                
          <div class="checkbox-item">
            <input type="checkbox" id="register-toilet" value="">
            <label for="toilet">洗手間</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-wi-fi" value="">
            <label for="wi-fi">無線網路</label>
          </div>                                                        
        </div>
        <p class="checkbox-title">付款方式</p>
        <div class="checkbox-container">                
          <div class="checkbox-item">
            <input type="checkbox" id="register-cash" value="">
            <label for="cash">現金</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-credit_card" value="">
            <label for="credit_card">信用卡</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-debit_card" value="">
            <label for="debit_card">簽帳金融卡</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="register-mobile_payment" value="">
            <label for="mobile_payment">行動支付</label>
          </div>                                                        
        </div>
      </div>

      <div class="modal-footer">          
        <button type="button" class="btn btn-secondary" id="" data-bs-target="#signupModal" data-bs-toggle="modal">上一頁</button>
        <button type="button" class="btn btn-secondary" id="" data-bs-target="#signupModal3" data-bs-toggle="modal">略過</button>
        <button type="button" class="btn btn-primary" id="" data-bs-target="#signupModal3" data-bs-toggle="modal">下一步</button>
      </div>
    </div>
  </div>
</div>
<!-- ### 註冊(3/3) ### -->
<div class="modal fade" id="signupModal3" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalToggleLabel3">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable login-modal">
    <div class="modal-content signup-body">
      <div class="modal-body login-modal-body">
        <h2 class="form-h2">會員註冊(3/3)</h2>
        <div class='form-message-popup' id="register3-message" style="display:none">
          <div id="loginError" class="form-error-message-popup" style="display:block; text-align:center"></div>
        </div>
        <p class="signup-explain1">此設定是為了提供您個性化的推薦，所有資訊僅用於提升服務品質</p>
        <p class="signup-explain2">可在會員資訊頁面進行設定及修改</p>

        <!-- ### 填寫 ### -->        
        <div class="slider-container">
          <label for="environment">氛圍</label>
          <input type="range" id="environment" name="environment" min="0" max="100" value="50" oninput="updateValue('environment')">
          <span id="environment_value">50%</span>
        </div>        
        <div class="slider-container">
          <label for="product">產品</label>
          <input type="range" id="product" name="product" min="0" max="100" value="50" oninput="updateValue('product')">
          <span id="product_value">50%</span>
        </div>        
        <div class="slider-container">
          <label for="service">服務</label>
          <input type="range" id="service" name="service" min="0" max="100" value="50" oninput="updateValue('service')">
          <span id="service_value">50%</span>
        </div>        
        <div class="slider-container">
          <label for="price">售價</label>
          <input type="range" id="price" name="price" min="0" max="100" value="50" oninput="updateValue('price')">
          <span id="price_value">50%</span>
        </div>

      </div>
      <div class="modal-footer">          
        <button type="button" class="btn btn-secondary" id="" data-bs-target="#signupModal2" data-bs-toggle="modal">上一頁</button>
        <button type="button" class="btn btn-primary" id="" onclick="">註冊</button>          
      </div>
    </div>
  </div>
</div>

<script src="scripts/header.js"></script>