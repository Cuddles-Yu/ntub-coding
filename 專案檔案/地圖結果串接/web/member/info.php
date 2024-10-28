<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/queries.php';
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
  global $MEMBER_INFO;
  $FAVORITE_STORES = getFavoriteStores();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
    <title>會員專區 - 評星宇宙</title>
    <link rel="stylesheet" href="/styles/common/base.css">
    <link rel="stylesheet" href="/styles/member.css">
</head>
<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/header.php'; ?>
  <main>
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
      <div id="info_main_area">
          <div class="item_name">
              <span class="item_text item-text-right">帳號</span>
              <input type="text" class="form-input-member" id="email" name="email" value="<?=$MEMBER_INFO['email']?>" disabled>
          </div>
          <div class="item_name">
              <span class="item_text item-text-right">名稱</span>
              <input type="text" class="form-input-member" id="user_name" name="user_name" value="<?=$MEMBER_INFO['name']?>" disabled>
              <i id="change_user_name" class="fi fi-sr-pencil edit_info1" data-bs-toggle="modal" data-bs-target="#modifyNameModal">修改</i>
          </div>
          <div class="item_name">
              <span class="item_text item-text-right">密碼</span>
              <input type="password" class="form-input-member" id="password" name="password" value="········" disabled>
              <i id="change_password" class="fi fi-sr-pencil edit_info2" data-bs-toggle="modal" data-bs-target="#modifyPasswordModal">修改</i>
          </div>
          <div class="item_name">
              <span class="item_text item-text-right">加入時間</span>
              <input type="text" class="form-input-member" id="create_time" name="create_time" value="<?=$MEMBER_INFO['create_time']?>" disabled>
          </div>
      </div>
      <div id="preference_main_area">
          <form class="preference_item_container">
            <div class="type_title">搜尋模式 <em style="color:red;font-weight:bold;">*</em></div>
            <div style="display:flex;align-items:center;margin-top:10px;">
              <input type="radio" id="member-distance-radio" class="checkbox" name="search-type" value="distance" style="margin-right:5px;"
                <?php if($SESSION_DATA->success && $MEMBER_INFO['search_mode'] === 'distance'): echo 'checked'; endif;?> disabled>
              <label for="member-distance-radio">
                <p class="checkbox-title" style="margin-bottom:2px;margin-left:5px;" id="member-distance-title">中心距離</p>
              </label>
            </div>
            <div id="comments-order-bar" class="input-group member-input-box mb-3 sort-button" style="margin-top:1vh;margin-bottom:1vh;">
              <span class="input-group-text member-input-box-title" id="basic-addon1">搜尋半徑</span>
              <input id="member-search-radius-input" type="text" class="form-control member-input-box-main field" style="max-width:182px;"
                aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"
                value="<?php if($SESSION_DATA->success): echo $MEMBER_INFO['search_radius']; else: echo '1500'; endif;?>" disabled>
              <span class="input-group-text" id="inputGroup-sizing-sm">公尺</span>
            </div>
            <div style="display:flex;align-items:center;margin-top:10px;">
              <input type="radio" id="member-geo-radio" class="checkbox" name="search-type" value="geo" style="margin-right:5px;"
                <?php if($SESSION_DATA->success && $MEMBER_INFO['search_mode'] === 'geo'): echo 'checked'; endif;?> disabled>
              <label for="member-geo-radio">
                <p class="checkbox-title" style="margin-bottom:2px;margin-left:5px;
                  <?php if(!($SESSION_DATA->success && $MEMBER_INFO['city'])): echo 'color:gray;'; endif; ?>" id="member-geo-title">地理位置</p>
              </label>
            </div>
              <div id="comments-order-bar" class="input-group member-input-box mb-3 sort-button" style="margin-top:1vh;margin-bottom:1vh;">
                <span class="input-group-text member-input-box-title" id="basic-addon1">縣市區域</span>
                <select class="form-select member-input-box-main field" aria-label="Default select example" id="member-city-select"
                  style="max-width:120px;" onclick="updateRadio('member')" onchange="updateArea('member')" disabled>
                  <option value="" selected>(無限制)</option>
                  <?php
                    $selectedCity = $MEMBER_INFO['city']??'';
                    $selectedDist = $MEMBER_INFO['dist']??'';
                    $dists = getDists($selectedCity);
                  ?>
                  <?php foreach($ALL_CITIES as $city): ?>
                    <option value="<?=$city?>"
                      <?php if($SESSION_DATA->success&&$selectedCity===$city): echo ' selected'; endif;?>><?=$city?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <select class="form-select member-input-box-main field" aria-label="Default select example" style="max-width:120px;" id="member-dist-select" disabled>
                  <?php if ($selectedCity === ''): ?>
                    <option value="" selected></option>
                  <?php else: ?>
                    <option value="" selected>(無限制)</option>
                    <?php foreach($dists as $dist): ?>
                      <option value="<?=$dist?>"
                        <?php if($SESSION_DATA->success&&$selectedDist===$dist): echo ' selected'; endif;?>><?=$dist?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
              <?php
                $checkboxGroups = [
                  'open_hour_button' => [
                    'index' => 4,
                    'title' => '營業時間 <em style="color:red;font-weight:bold;">*</em>',
                    'select' => 'select_open_hour',
                    'class' => 'open_hour',
                    'items' => [
                      'member-will-open' => '即將營業',
                      'member-open-now' => '營業中',
                      'member-will-close' => '即將打烊',
                      'member-close-now' => '已打烊',
                    ]
                  ],
                  'personal_button' => [
                    'index' => 1,
                    'title' => '個人需求',
                    'select' => 'select_personal',
                    'class' => 'personal_service',
                    'items' => [
                      'member-parking' => '停車場',
                      'member-wheelchair-accessible' => '無障礙',
                      'member-vegetarian' => '素食料理',
                      'member-healthy' => '健康料理',
                      'member-kids-friendly' => '兒童友善',
                      'member-pets-friendly' => '寵物友善',
                      'member-gender-friendly' => '性別友善'
                    ]
                  ],
                  'method_button' => [
                    'index' => 2,
                    'title' => '用餐方式',
                    'select' => 'select_method',
                    'class' => 'meal_method',
                    'items' => [
                      'member-delivery' => '外送',
                      'member-takeaway' => '外帶',
                      'member-dine-in' => '內用'
                    ]
                  ],
                  'time_button' => [
                    'index' => 3,
                    'title' => '用餐時段',
                    'select' => 'select_time',
                    'class' => 'meal_time',
                    'items' => [
                      'member-breakfast' => '早餐',
                      'member-brunch' => '早午餐',
                      'member-lunch' => '午餐',
                      'member-dinner' => '晚餐'
                    ]
                  ],
                  'plan_button' => [
                    'index' => 5,
                    'title' => '用餐規劃',
                    'select' => 'select_plan',
                    'class' => 'meal_plan',
                    'items' => [
                      'member-reservation' => '接受訂位',
                      'member-group-friendly' => '適合團體',
                      'member-family-friendly' => '適合家庭聚餐'
                    ]
                  ],
                  'facility_button' => [
                    'index' => 6,
                    'title' => '基礎設施',
                    'select' => 'select_facility',
                    'class' => 'basic_facility',
                    'items' => [
                      'member-toilet' => '洗手間',
                      'member-wifi' => '無線網路'
                    ]
                  ],
                  'payment_button' => [
                    'index' => 7,
                    'title' => '付款方式',
                    'select' => 'select_payment',
                    'class' => 'payment',
                    'items' => [
                      'member-cash' => '現金',
                      'member-credit-card' => '信用卡',
                      'member-debit-card' => '簽帳金融卡',
                      'member-mobile-payment' => '行動支付'
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
                        <input type="checkbox" class="<?=$group['select']?> checkbox" id="<?=$item?>" name="<?=$item?>" autocomplete="on" disabled
                          <?=transformToPreference($item)?>>
                        <label for="<?=$item?>"><?=$value?></label>
                      </div>
                  <?php endforeach; ?>
                </div>
              <?php endforeach; ?>
          </form>
        <div class="button_area">
          <button id="preference_edit_button" class="edit-button btn-solid-gray" onclick="editPreference()">修改</button>
          <button id="preference_cancel_button" class="cancel-button btn-solid-gray" onclick="restorePreference()" style="display: none;">取消</button>
          <button id="preference_save_button" class="save-button btn-solid-windows-blue" onclick="savePreference()" style="display: none;">完成</button>
        </div>
      </div>
      <div id="weight_main_area">
        <div class="description_text">
          <p>請調整每項指標的權重，以反映您對餐廳需求的重視程度。</p>
          <p>權重越高，表示您越重視該指標的表現，為您推薦最符合期望的餐廳。</p>
        </div>
        <div class="slider-container">
          <label for="atmosphere"><?=$_ATMOSPHERE?></label>
          <input type="range" id="atmosphere" name="atmosphere" min="0" max="100" value="<?=$MEMBER_INFO['atmosphere_weight']?>" oninput="updateLabelValue('atmosphere')" disabled>
          <span id="atmosphere-value"><?=$MEMBER_INFO['atmosphere_weight']?></span>
        </div>
        <div class="slider-container">
          <label for="product"><?=$_PRODUCT?></label>
          <input type="range" id="product" name="product" min="0" max="100" value="<?=$MEMBER_INFO['product_weight']?>" oninput="updateLabelValue('product')" disabled>
          <span id="product-value"><?=$MEMBER_INFO['product_weight']?></span>
        </div>
        <div class="slider-container">
          <label for="service"><?=$_SERVICE?></label>
          <input type="range" id="service" name="service" min="0" max="100" value="<?=$MEMBER_INFO['service_weight']?>" oninput="updateLabelValue('service')" disabled>
          <span id="service-value"><?=$MEMBER_INFO['service_weight']?></span>
        </div>
        <div class="slider-container">
          <label for="price"><?=$_PRICE?></label>
          <input type="range" id="price" name="price" min="0" max="100" value="<?=$MEMBER_INFO['price_weight']?>" oninput="updateLabelValue('price')" disabled>
          <span id="price-value"><?=$MEMBER_INFO['price_weight']?></span>
        </div>
        <div class="button_area">
        <button id="weight_edit_button" class="edit-button btn-solid-gray" onclick="editWeight()">修改</button>
          <button id="weight_cancel_button"  class="cancel-button btn-solid-gray" onclick="restoreWeight()" style="display: none;">取消</button>
          <button id="weight_save_button"  class="save-button btn-solid-windows-blue" onclick="saveWeight()" style="display: none;">完成</button>
        </div>
      </div>
      <div id="favorite_main_area" style="position:absolute;top:150px;">
        <div class="first_line">
          <div class="mark_store">
            <span style="margin-left:8px;font-weight:bold;">名稱</span>
          </div>
          <div class="mark_score">
            <span style="margin-left:2px;font-weight:bold;">評分</span>
          </div>
          <div class="mark_category">
            <span style="margin-left:0px;font-weight:bold;">類別</span>
          </div>
          <div class="mark_time">
            <span style="margin-left:-4px;font-weight:bold;">收藏時間</span>
          </div>
          <div class="mark_button">
          <span style="margin-left:0px;font-weight:bold;">操作功能</span>
          </div>
        </div>
        <div class="content_row_container">
          <?php if (!empty($FAVORITE_STORES)): ?>
            <?php foreach($FAVORITE_STORES as $store): ?>
              <?php
                $id = $store['id'];
                $name = htmlspecialchars($store['name']);
                $previewImage = htmlspecialchars($store['preview_image']);
                $tag = htmlspecialchars($store['tag']);
                $createTime = htmlspecialchars($store['create_time']);
                $mark = htmlspecialchars($store['mark']);
                $markName = $markOptions[$mark]['tagName'] ?? '';
                $score = getBayesianScore(getMemberNormalizedWeight(), $store['id']);
              ?>
              <div class="content_row" data-id="<?=$id?>" onclick="goToDetailPage(<?=$id?>)">
                <div class="mark_store">
                  <div class="mark_img">
                    <img src="<?=$previewImage?>">
                  </div>
                  <span class="store_name"><?=$name?></span>
                </div>
                <div class="mark_score">
                  <span><?=$score?></span>
                </div>
                <div class="mark_category">
                  <span><?=$tag?><?=$markName?></span>
                </div>
                <div class="mark_time">
                  <span style="margin-right: 5px;"><?=$createTime?></span>
                </div>
                <div class="mark_button mark_button2">
                  <div class="sort_button_wrapper" onclick="preventMultipleClick(event);targetFavorite(this);" data-bs-toggle="modal" data-bs-target="#removeFavoriteModal">
                    <i class="fi fi-sr-trash trans-red-button"></i>
                  </div>
                  <div class="sort_button_wrapper" onclick="preventMultipleClick(event);shareStore('<?=$id?>');">
                    <i class="fi fi-sr-share trans-blue-button"></i>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
    </div>
  </main>

  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/remove-favorite.php'; ?>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/modify-name.php'; ?>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/form/modify-password.php'; ?>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/scripts/common/base.html';?>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/base/footer.php'; ?>
  <script src="/scripts/member.js" defer></script>
</body>
</html>