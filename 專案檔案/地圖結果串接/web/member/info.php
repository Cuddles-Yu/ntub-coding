<?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php'; ?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員資訊頁面</title>
    <link rel="stylesheet" href="styles/member.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.4.2/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
</head>
<body>
    
  <!-- ### 頁首 ### -->
  <?php require $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <hr>

  <main>
      <!-- 側欄 -->
      <div class="side_area">
          <div class="page_button" id="info" style="color: #8234ff;" onclick="switchToInfo()"> <!-- info_main_area -->
              <i class="fi fi-sr-interrogation logo"></i>
              <p>基本資料</p>
          </div>
          <div class="page_button" id="preference" onclick="switchToPreference()"> <!-- preference_main_area -->
              <i class="fi fi-sr-settings-sliders logo"></i>
              <p>偏好設定</p>
          </div>
          <div class="page_button" id="weight" onclick="switchToWeight()"> <!-- weight_main_area -->
              <i class="fi fi-sr-bars-progress logo"></i>
              <p>權重設定</p>
          </div>
          <div class="page_button" id="place" onclick="switchToPlace()"> <!-- place_main_area -->
              <i class="fi fi-sr-comment-heart logo"></i>
              <p>收藏地點</p>
          </div>
      </div>

      <!-- 基本資料 -->
      <div id="info_main_area">
          <div id="user_icon_logo2_container">
              <img src="/images/icon-member.jpg" id="user_icon_logo2">
              <i class="fi fi-sr-pen-clip edit_info1">修改</i>
          </div>
          <div class="item_name">
              <span class="item_text">使用者名稱</span>
              <input type="text" class="item_text2" id="user_name" name="user_name" value="評星宇宙" readonly> <!-- readonly: 唯讀狀態 -->
          </div>
          <div class="item_name">
              <span class="item_text">電子郵件</span>
              <input type="text" class="item_text2" id="email" name="email" value="113206@ntub.edu.tw" readonly>
          </div>
          <div class="item_name">
              <span class="item_text">密碼</span>
              <input type="password" class="item_text2" id="password" name="password" value="12345678" readonly>
              <!-- <span id="change_password" class="edit_info2">修改</span> -->
              <i id="change_password" class="fi fi-sr-pen-clip edit_info2">修改</i>
          </div>
          <!-- 修改密碼小視窗 -->
          <div id="password_modal" class="modal">
              <div class="modal-content">
                  <span class="close-button" onclick="closeModal()">&times;</span>
                  <div id="password_title">修改密碼</div>
                  <form id="password_form">
                      <label for="current_password">目前密碼</label>
                      <input type="password" id="current_password" name="current_password" required></br>
                      <label for="new_password">新密碼</label>
                      <input type="password" id="new_password" name="new_password" required></br>
                      <label for="confirm_password">確認新密碼</label>
                      <input type="password" id="confirm_password" name="confirm_password" required>
                      <button type="submit">確認</button>
                  </form>
                  <p class="success-message" id="success_message">密碼已修改成功！</p>
              </div>
          </div>
      </div>

      <!-- 偏好設定 -->
      <div id="preference_main_area">
          <form class="preference_item_container">
              <div class="checkbox_container2">
                  <span class="type_title">搜尋半徑</span>
                  <input type="number" id="search_radius" name="search_radius" value="1500" readonly>
                  <span class="type_title" id="meter">公尺</span>
                  <span class="type_title">最低綜合評分（0~100）</span>
                  <input type="number" id="lower_score" name="lower_score" value="80" min="0" max="100" readonly>
                  <span class="type_title">分</span>
              </div>

              <div class="checkbox_container2">
                  <span class="type_title">是否需在營業時間內</span>
                  <div class="radio_item">
                      <input type="radio" id="service_time_y" name="service_time" value="yes" disabled>
                      <label for="service_time_y">是</label>
                  </div>
                  <div class="radio_item">
                      <input type="radio" id="service_time_n" name="service_time" value="no" disabled>
                      <label for="service_time_n">否</label>
                  </div>
              </div>

              <div class="title_text" id="personal_button">
                  <div class="type_title">個人需求</div>
                  <button class="select_all_button" onclick="selectAllPersonal(event)" style="display: none;">全選</button>
                  <button class="deselect_all_button" onclick="deselectAllPersonal(event)" style="display: none;">取消全選</button>
              </div>
              <div class="checkbox_container personal_service">
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_personal" id="personal_service1" name="personal_service1" autocomplete="on" disabled>
                      <label for="personal_service1">停車場</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_personal" id="personal_service2" name="personal_service2" autocomplete="on" disabled>
                      <label for="personal_service2">無障礙</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_personal" id="personal_service3" name="personal_service3" autocomplete="on" disabled>
                      <label for="personal_service3">素食料理</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_personal" id="personal_service4" name="personal_service4" autocomplete="on" disabled>
                      <label for="personal_service4">健康料理</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_personal" id="personal_service5" name="personal_service5" autocomplete="on" disabled>
                      <label for="personal_service5">兒童友善</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_personal" id="personal_service6" name="personal_service6" autocomplete="on" disabled>
                      <label for="personal_service6">寵物友善</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_personal" id="personal_service7" name="personal_service7" autocomplete="on" disabled>
                      <label for="personal_service7">性別友善</label>
                  </div>
              </div>

              <div class="title_text" id="method_button">
                  <div class="type_title">用餐方式</div>
                  <button class="select_all_button" onclick="selectAllMethod(event)" style="display: none;">全選</button>
                  <button class="deselect_all_button" onclick="deselectAllMethod(event)" style="display: none;">取消全選</button>
              </div>
              <div class="checkbox_container meal_method">
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_method" id="restaurant_service1" name="restaurant_service1" autocomplete="on" disabled>
                      <label for="restaurant_service1">外送</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_method" id="restaurant_service2" name="restaurant_service2" autocomplete="on" disabled>
                      <label for="restaurant_service2">外帶</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_method" id="restaurant_service3" name="restaurant_service3" autocomplete="on" disabled>
                      <label for="restaurant_service3">內用</label>
                  </div>
              </div>

              <div class="title_text" id="time_button">
                  <div class="type_title">用餐時段</div>
                  <button class="select_all_button" onclick="selectAllTime(event)" style="display: none;">全選</button>
                  <button class="deselect_all_button" onclick="deselectAllTime(event)" style="display: none;">取消全選</button>
              </div>
              <div class="checkbox_container meal_time">
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_time" id="restaurant_service4" name="restaurant_service4" autocomplete="on" disabled>
                      <label for="restaurant_service4">早餐</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_time" id="restaurant_service5" name="restaurant_service5" autocomplete="on" disabled>
                      <label for="restaurant_service5">早午餐</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_time" id="restaurant_service6" name="restaurant_service6" autocomplete="on" disabled>
                      <label for="restaurant_service6">午餐</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_time" id="restaurant_service7" name="restaurant_service7" autocomplete="on" disabled>
                      <label for="restaurant_service7">晚餐</label>
                  </div>
              </div>

              <div class="title_text" id="atmosphere_button">
                  <div class="type_title">用餐氛圍</div>
                  <button class="select_all_button" onclick="selectAllAtmosphere(event)" style="display: none;">全選</button>
                  <button class="deselect_all_button" onclick="deselectAllAtmosphere(event)" style="display: none;">取消全選</button>
              </div>
              <div class="checkbox_container meal_atmosphere">
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_atmosphere" id="restaurant_service8" name="restaurant_service8" autocomplete="on" disabled>
                      <label for="restaurant_service8">氣氛悠閒</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_atmosphere" id="restaurant_service9" name="restaurant_service9" autocomplete="on" disabled>
                      <label for="restaurant_service9">環境舒適</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_atmosphere" id="restaurant_service10" name="restaurant_service10" autocomplete="on" disabled>
                      <label for="restaurant_service10">音樂演奏</label>
                  </div>
              </div>

              <div class="title_text" id="plan_button">
                  <div class="type_title">用餐規劃</div>
                  <button class="select_all_button" onclick="selectAllPlan(event)" style="display: none;">全選</button>
                  <button class="deselect_all_button" onclick="deselectAllPlan(event)" style="display: none;">取消全選</button>
              </div>
              <div class="checkbox_container meal_plan">
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_plan" id="restaurant_service11" name="restaurant_service11" autocomplete="on" disabled>
                      <label for="restaurant_service11">接受訂位</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_plan" id="restaurant_service12" name="restaurant_service12" autocomplete="on" disabled>
                      <label for="restaurant_service12">適合團體</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_plan" id="restaurant_service13" name="restaurant_service13" autocomplete="on" disabled>
                      <label for="restaurant_service13">適合家庭聚餐</label>
                  </div>
              </div>

              <div class="title_text" id="facility_button">
                  <div class="type_title">基礎設施</div>
                  <button class="select_all_button" onclick="selectAllFacility(event)" style="display: none;">全選</button>
                  <button class="deselect_all_button" onclick="deselectAllFacility(event)" style="display: none;">取消全選</button>
              </div>
              <div class="checkbox_container basic_facility">
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_facility" id="restaurant_service14" name="restaurant_service14" autocomplete="on" disabled>
                      <label for="restaurant_service14">洗手間</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_facility" id="restaurant_service15" name="restaurant_service15" autocomplete="on" disabled>
                      <label for="restaurant_service15">無線網路</label>
                  </div>
              </div>

              <div class="title_text" id="payment_button">
                  <div class="type_title">付款方式</div>
                  <button class="select_all_button" onclick="selectAllPayment(event)" style="display: none;">全選</button>
                  <button class="deselect_all_button" onclick="deselectAllPayment(event)" style="display: none;">取消全選</button>
              </div>
              <div class="checkbox_container payment">
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_payment" id="restaurant_service16" name="restaurant_service16" autocomplete="on" disabled>
                      <label for="restaurant_service16">現金</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_payment" id="restaurant_service17" name="restaurant_service17" autocomplete="on" disabled>
                      <label for="restaurant_service17">信用卡</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_payment" id="restaurant_service18" name="restaurant_service18" autocomplete="on" disabled>
                      <label for="restaurant_service18">簽帳金融卡</label>
                  </div>
                  <div class="checkbox_item">
                      <input type="checkbox" class="select_payment" id="restaurant_service19" name="restaurant_service19" autocomplete="on" disabled>
                      <label for="restaurant_service19">行動支付</label>
                  </div>
              </div>
          </form>

          <div class="button_area">
              <button id="preference_edit_button" onclick="editSettings()">修改</button>
              <button id="preference_save_button" onclick="saveSettings()" style="display: none;">完成</button>
              <button id="preference_cancel_button" onclick="cancelEdit()" style="display: none;">取消</button>
          </div>
      </div>

      <!-- 權重設定 -->
      <div id="weight_main_area"> 
          <div class="description_text">請依據您的重視程度，填寫適當的指標權重，以找尋更符合您需求的商家。</div>
          
          <div class="slider-container">
              <label for="atmosphere">氛圍</label>
              <input type="range" id="atmosphere" name="atmosphere" min="0" max="100" value="50" oninput="updateValue('atmosphere')" disabled>
              <span id="atmosphere_value">50%</span>
          </div>
          
          <div class="slider-container">
              <label for="product">產品</label>
              <input type="range" id="product" name="product" min="0" max="100" value="50" oninput="updateValue('product')" disabled>
              <span id="product_value">50%</span>
          </div>
          
          <div class="slider-container">
              <label for="service">服務</label>
              <input type="range" id="service" name="service" min="0" max="100" value="50" oninput="updateValue('service')" disabled>
              <span id="service_value">50%</span>
          </div>
          
          <div class="slider-container">
              <label for="price">售價</label>
              <input type="range" id="price" name="price" min="0" max="100" value="50" oninput="updateValue('price')" disabled>
              <span id="price_value">50%</span>
          </div>
          
          <div class="slider-container">
              <label for="popularity">熱門度</label>
              <input type="range" id="popularity" name="popularity" min="0" max="100" value="50" oninput="updateValue('popularity')" disabled>
              <span id="popularity_value">50%</span>
          </div>

          <div class="button_area">
              <button id="weight_edit_button" onclick="toggleEditMode2()">修改</button>
              <button id="weight_cancel_button" onclick="cancelEditMode2()" style="display: none;">取消</button>
          </div>
      </div>

      <!-- 收藏地點 -->
      <div id="place_main_area">
          <div>
              <div class="search">
                  <div class="form-floating search-city">
                      <select class="form-select" id="location" name="location" aria-label="Floating label select example">
                      <option class="city" selected value="台北市">臺北市</option>
                      <option class="city" selected value="台北市">新北市</option>
                      </select>
                      <label for="location">城市</label>
                  </div>
  
                  <div class="form-floating search-keyword">
                      <input type="text" class="form-control" id="keyword" name="keyword" placeholder="關鍵字">
                      <label for="keyword">搜尋關鍵字</label>
                  </div>
                  <button type="submit" class="btn btn-secondary mt-3 search-button">搜尋</button>
                </div>
          </div>
          
          <div class="first_line">
              <div class="mark_number">
                  <span>#</span>
              </div>
              <div class="mark_store">
                  <span style="margin-right: 5px;">商家名稱</span>
                  <i class="fi fi-sr-arrow-down" id="store_down_arrow1"></i>
                  <i class="fi fi-sr-arrow-up" id="store_up_arrow1" style="display: none;"></i>
              </div>
              <div class="mark_score">
                  <span style="margin-right: 5px;">評分</span>
                  <i class="fi fi-sr-arrow-down" id="store_down_arrow2"></i>
                  <i class="fi fi-sr-arrow-up" id="store_up_arrow2" style="display: none;"></i>
              </div>
              <div class="mark_category">
                  <span style="margin-right: 5px;">類別</span>
                  <i class="fi fi-sr-arrow-down" id="store_down_arrow3"></i>
                  <i class="fi fi-sr-arrow-up" id="store_up_arrow3" style="display: none;"></i>
              </div>
              <div class="mark_distance">
                  <span style="margin-right: 5px;">距離</span>
                  <i class="fi fi-sr-arrow-down" id="store_down_arrow4"></i>
                  <i class="fi fi-sr-arrow-up" id="store_up_arrow4" style="display: none;"></i>
              </div>
              <div class="mark_time">
                  <span style="margin-right: 5px;">收藏時間</span>
                  <i class="fi fi-sr-arrow-down" id="store_down_arrow5"></i>
                  <i class="fi fi-sr-arrow-up" id="store_up_arrow5" style="display: none;"></i>
              </div>
              <div class="mark_button">
                  <div class="sort_button">操作</div>
              </div>
          </div>

          <div class="content_row">
              <div class="mark_number">
                  <span>1</span>
              </div>
              <div class="mark_store">
                  <div class="mark_img">
                      <img src="../images/store-map.JPG">
                  </div>
                  <span class="store_name">波赫士領地精品咖啡館 昌吉店 BorgesPlace 提拉米蘇 千層蛋糕</span>
              </div>
              <div class="mark_score">
                  <span>85</span>
              </div>
              <div class="mark_category">
                  <span>餃子</span>
              </div>
              <div class="mark_distance">
                  <span>1 km</span>
              </div>
              <div class="mark_time">
                  <span style="margin-right: 5px;">2023-10-01</span>
              </div>
              <div class="mark_button mark_button2">
                  <i class="fi fi-sr-bookmark sort_button"></i>
                  <i class="fi fi-sr-share sort_button"></i>
              </div>
          </div>
      </div>
  </main>

  <!-- ### 頁尾 ### -->
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>

  <script src="../scripts/member.js"></script>
  
</body>
</html>