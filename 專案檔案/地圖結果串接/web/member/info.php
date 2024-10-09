<?php 
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php'; 
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/analysis.php';
  if (!$SESSION_DATA->success) {    
    echo "
      <script>
        localStorage.setItem('tryToLogin', 'true');
        window.location.replace('/home');
      </script>
    ";
    exit();
  }  
  $memberInfo = getMemberInfo();
  $favoriteStores = getFavoriteStores();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員專區 - 評星宇宙</title>
    <link rel="stylesheet" href="../styles/member.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.4.2/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
</head>
<body>
    
  <!-- ### 頁首 ### -->
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>

  <!-- ### 登入 ### -->
  <div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="modifyPasswordModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered login-modal">
      <div class="modal-content">
        <div class="modal-body login-modal-body" style="height:270px;">
          <h2 class="form-h2">修改密碼</h2>
          <div class='form-message-popup' id="login-message" style="display:none">
            <div id="loginError" class="form-error-message-popup" style="display:block; text-align:center"></div>
          </div>

          <!-- ### 填寫 ### -->
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
          <button type="button" class="btn btn-secondary" id="login-cancel-button" onclick="cancelModal()">取消</button>
          <button type="button" class="btn btn-primary" id="login-submit-button" onclick="loginRequest()">修改</button>
        </div>
      </div>
    </div>
  </div>

  <hr>
  <main>
      <!-- 側欄 -->
      <div class="side_area">
          <div class="page_button" id="info" style="color: #8234ff;" onclick="switchTo('info')">
              <i class="fi fi-sr-interrogation logo" id="info_logo" style="color: #8234ff;"></i>
              <p>基本資料</p>
          </div>
          <div class="page_button" id="preference" onclick="switchTo('preference')">
              <i class="fi fi-sr-settings-sliders logo" id="preference_logo"></i>
              <p>偏好設定</p>
          </div>
          <div class="page_button" id="weight" onclick="switchTo('weight')">
              <i class="fi fi-sr-bars-progress logo" id="weight_logo"></i>
              <p>權重設定</p>
          </div>
          <div class="page_button" id="favorite" onclick="switchTo('favorite')">
              <i class="fi fi-sr-comment-heart logo" id="favorite_logo"></i>
              <p>收藏餐廳</p>
          </div>
      </div>

      <!-- 基本資料 -->
      <div id="info_main_area">
          <div class="item_name">
              <span class="item_text item-text-right">帳號</span>
              <input type="text" class="form-input-member" id="email" name="email" value="<?=$memberInfo['email']?>" readonly>
          </div>
          <div class="item_name">
              <span class="item_text item-text-right">名稱</span>
              <input type="text" class="form-input-member" id="user_name" name="user_name" value="<?=$memberInfo['name']?>" readonly>
              <i id="change_user_name" class="fi fi-sr-pencil edit_info1">修改</i>
              <i id="done_user_name" class="fi fi-sr-check edit_info1" style="display: none;"></i>
              <i id="cancel_user_name" class="fi fi-sr-undo edit_info1" style="display: none;"></i>
          </div>          
          <div class="item_name">
              <span class="item_text item-text-right">密碼</span>
              <input type="password" class="form-input-member" id="password" name="password" value="········" readonly>
              <i id="change_password" class="fi fi-sr-pencil edit_info2" data-bs-toggle="modal" data-bs-target="#modifyPasswordModal">修改</i>
          </div>
          <div class="item_name">
              <span class="item_text item-text-right">加入時間</span>
              <input type="text" class="form-input-member" id="create_time" name="create_time" value="<?=$memberInfo['create_time']?>" readonly>
          </div>
      </div>

      <!-- 偏好設定 -->
      <div id="preference_main_area">
          <form class="preference_item_container">
              <div class="input-group input-group-sm mb-3" style="width:230px;">
                <p class="checkbox-title">搜尋半徑</p>
                <input id="search_radius" name="search_radius" type="text" class="form-control" 
                  aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" value="<?=$memberInfo['search_radius']?>">
                <span class="input-group-text" id="inputGroup-sizing-sm">公尺</span>
              </div>
              <?php
                $checkboxGroups = [
                  'open_hour_button' => [
                    'index' => 4,
                    'title' => '營業時間',
                    'select' => 'select_open_hour',
                    'class' => 'open_hour',
                    'items' => [
                      'open_now' => '營業中'
                    ]
                  ],
                  'personal_button' => [
                    'index' => 1,
                    'title' => '個人需求',
                    'select' => 'select_personal',
                    'class' => 'personal_service',
                    'items' => [
                      'parking' => '停車場',
                      'wheelchair_accessible' => '無障礙',
                      'vegetarian' => '素食料理',
                      'healthy' => '健康料理',
                      'kids_friendly' => '兒童友善',
                      'pets_friendly' => '寵物友善',
                      'gender_friendly' => '性別友善'
                    ]
                  ],
                  'method_button' => [
                    'index' => 2,
                    'title' => '用餐方式',
                    'select' => 'select_method',
                    'class' => 'meal_method',
                    'items' => [
                      'delivery' => '外送',
                      'takeaway' => '外帶',
                      'dine_in' => '內用'
                    ]
                  ],
                  'time_button' => [
                    'index' => 3,
                    'title' => '用餐時段',
                    'select' => 'select_time',
                    'class' => 'meal_time',
                    'items' => [
                      'breakfast' => '早餐',
                      'brunch' => '早午餐',
                      'lunch' => '午餐',
                      'dinner' => '晚餐'
                    ]
                  ],
                  'plan_button' => [
                    'index' => 5,
                    'title' => '用餐規劃',
                    'select' => 'select_plan',
                    'class' => 'meal_plan',
                    'items' => [
                      'reservation' => '接受訂位',
                      'group_friendly' => '適合團體',
                      'family_friendly' => '適合家庭聚餐'
                    ]
                  ],
                  'facility_button' => [
                    'index' => 6,
                    'title' => '基礎設施',
                    'select' => 'select_facility',
                    'class' => 'basic_facility',
                    'items' => [
                      'toilet' => '洗手間',
                      'wifi' => '無線網路'
                    ]
                  ],
                  'payment_button' => [
                    'index' => 7,
                    'title' => '付款方式',
                    'select' => 'select_payment',
                    'class' => 'payment',
                    'items' => [
                      'cash' => '現金',
                      'credit_card' => '信用卡',
                      'debit_card' => '簽帳金融卡',
                      'mobile_payment' => '行動支付'
                    ]
                  ]
                ];
              ?>
              <?php foreach($checkboxGroups as $key => $group): ?>
                <div class="title_text" id="<?=$key?>">
                  <div class="type_title"><?=$group['title']?></div>
                  <i id="select_all_icon<?=$group['index']?>" class="fi fi-sr-checkbox select_icon"></i>
                  <i id="deselect_all_icon<?=$group['index']?>" class="fi fi-sr-square deselect_icon"></i>
                  <i id="mixed_icon<?=$group['index']?>" class="fi fi-sr-square-minus mixed_icon"></i>
                </div>                
                <div class="checkbox_container <?=$group['class']?>">
                  <?php foreach($group['items'] as $item => $value): ?>
                      <div class="checkbox_item">
                        <input type="checkbox" class="<?=$group['select']?>" id="<?=$item?>" name="<?=$item?>" autocomplete="on" disabled 
                          <?php if($memberInfo[$item]): echo 'checked'; endif; ?>>
                        <label for="<?=$item?>"><?=$value?></label>
                      </div>
                  <?php endforeach; ?>                
                </div>
              <?php endforeach; ?>
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
              <input type="range" id="atmosphere" name="atmosphere" min="0" max="100" value="<?=$memberInfo['atmosphere_weight']?>" oninput="updateLabelValue('atmosphere')" disabled>
              <span id="atmosphere_value"><?=$memberInfo['atmosphere_weight']?></span>
          </div>
          
          <div class="slider-container">
              <label for="product">產品</label>
              <input type="range" id="product" name="product" min="0" max="100" value="<?=$memberInfo['product_weight']?>" oninput="updateLabelValue('product')" disabled>
              <span id="product_value"><?=$memberInfo['product_weight']?></span>
          </div>
          
          <div class="slider-container">
              <label for="service">服務</label>
              <input type="range" id="service" name="service" min="0" max="100" value="<?=$memberInfo['service_weight']?>" oninput="updateLabelValue('service')" disabled>
              <span id="service_value"><?=$memberInfo['service_weight']?></span>
          </div>
          
          <div class="slider-container">
              <label for="price">售價</label>
              <input type="range" id="price" name="price" min="0" max="100" value="<?=$memberInfo['price_weight']?>" oninput="updateLabelValue('price')" disabled>
              <span id="price_value"><?=$memberInfo['price_weight']?></span>
          </div>

          <div class="button_area">
              <button id="weight_edit_button" onclick="toggleEditMode2()">修改</button>
              <button id="weight_cancel_button" onclick="cancelEditMode2()" style="display: none;">取消</button>
          </div>
      </div>

      <!-- 收藏餐廳 -->
      <div id="favorite_main_area">
          <div>
            <div class="search">  
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
              <div class="mark_time">
                  <span style="margin-right: 5px;">收藏時間</span>
                  <i class="fi fi-sr-arrow-down" id="store_down_arrow5"></i>
                  <i class="fi fi-sr-arrow-up" id="store_up_arrow5" style="display: none;"></i>
              </div>
              <div class="mark_button">
                  <div class="sort_button">操作</div>
              </div>
          </div>

          <div class="content_row_container">
            <?php $index=1; ?>
            <?php foreach($favoriteStores as $store): ?>
              <?php
                $name = htmlspecialchars($store['name']);
                $preview_image = htmlspecialchars($store['preview_image']);
                $tag = htmlspecialchars($store['tag']);
                $createTime = htmlspecialchars($store['create_time']);
                $mark = htmlspecialchars($store['mark']);
                $markName = $markOptions[$mark]['tagName'] ?? '';                
              ?>
              <div class="content_row">
                <div class="mark_number">
                  <span><?=$index?></span>
                </div>
                <div class="mark_store">
                  <div class="mark_img">
                    <img src="<?=$preview_image?>">
                  </div>
                  <span class="store_name"><?=$name?></span>
                </div>
                <div class="mark_score">
                  <span>00</span>
                </div>
                <div class="mark_category">
                  <span><?=$tag?><?=$markName?></span>
                </div>
                <div class="mark_time">
                  <span style="margin-right: 5px;"><?=$createTime?></span>
                </div>
                <div class="mark_button mark_button2">
                  <i class="fi fi-sr-bookmark sort_button"></i>
                  <i class="fi fi-sr-share sort_button"></i>
                </div>
              </div>
              <?php $index++; ?>
            <?php endforeach; ?>
          </div>
      </div>
  </main>

  <!-- ### 頁尾 ### -->
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>

  <script src="../scripts/member.js"></script>  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
</body>
</html>