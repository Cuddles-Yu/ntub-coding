<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/queries.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  global $modalTitle, $modalId, $ALL_CITIES;

  $checkboxGroups = [
    'open_hour_button' => [
      'title' => '營業時間',
      'items' => [
        'condition-will-open' => ['name' => '即將營業', 'default' => 'checked'],
        'condition-open-now' => ['name' => '營業中(不含即將打烊)', 'default' => 'checked'],
        'condition-will-close' => ['name' => '即將打烊', 'default' => 'checked'],
        'condition-close-now' => ['name' => '已打烊'],
    ]],
    'personal_button' => [
      'title' => '個人需求',
      'items' => [
        'condition-parking' => ['name' => '停車場'],
        'condition-wheelchair-accessible' => ['name' => '無障礙'],
        'condition-vegetarian' => ['name' => '素食料理'],
        'condition-healthy' => ['name' => '健康料理'],
        'condition-kids-friendly' => ['name' => '兒童友善'],
        'condition-pets-friendly' => ['name' => '寵物友善'],
        'condition-gender-friendly' => ['name' => '性別友善'],
    ]],
    'method_button' => [
      'title' => '用餐方式',
      'items' => [
        'condition-delivery' => ['name' => '外送'],
        'condition-takeaway' => ['name' => '外帶'],
        'condition-dine-in' => ['name' => '內用'],
    ]],
    'time_button' => [
      'title' => '用餐時段',
      'items' => [
        'condition-breakfast' => ['name' => '早餐'],
        'condition-brunch' => ['name' => '早午餐'],
        'condition-lunch' => ['name' => '午餐'],
        'condition-dinner' => ['name' => '晚餐'],
    ]],
    'plan_button' => [
      'title' => '用餐規劃',
      'items' => [
        'condition-reservation' => ['name' => '接受訂位'],
        'condition-group-friendly' => ['name' => '適合團體'],
        'condition-family-friendly' => ['name' => '適合家庭聚餐'],
    ]],
    'facility_button' => [
      'title' => '基礎設施',
      'items' => [
          'condition-toilet' => ['name' => '洗手間'],
          'condition-wifi' => ['name' => '無線網路'],
    ]],
    'payment_button' => [
      'title' => '付款方式',
      'items' => [
        'condition-cash' => ['name' => '現金'],
        'condition-credit-card' => ['name' => '信用卡'],
        'condition-debit-card' => ['name' => '簽帳金融卡'],
        'condition-mobile-payment' => ['name' => '行動支付'],
    ]]
  ];
?>

<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="<?=$modalId?>Modal" aria-hidden="true" tabindex="-1" aria-labelledby="<?=$modalId?>ModalLabel">
  <div class="modal-dialog form-dialog modal-dialog-centered modal-dialog-scrollable login-modal">
    <div class="modal-content">
      <h2 class="form-h2-title" style="padding-top:20px;"><?=$modalTitle?></h2>
      <div class="modal-body login-modal-body" style="height:400px;padding-top:10px;padding-right:0;">
        <p class="checkbox-title" style="font-weight:normal;background:gray;color:white;text-align:center;">搜尋模式</p>
        <div style="display:flex;align-items:center;margin-top:10px;">
          <input type="radio" id="condition-distance-radio" name="search-type" value="distance"
            style="margin-right:5px;" <?php if($SESSION_DATA->success && $MEMBER_INFO['search_mode'] === 'distance'): echo 'checked'; endif;?>>
          <label for="condition-distance-radio">
            <p class="checkbox-title" style="cursor:pointer;margin-bottom:2px;color:#663399;" id="condition-distance-title">中心距離</p>
          </label>
        </div>
        <div id="comments-order-bar" class="input-group condition-input-box mb-3 sort-button" style="margin-bottom:-1px !important;">
          <span class="input-group-text condition-input-box-title" id="basic-addon1">搜尋半徑</span>
          <input id="<?=$modalId?>-search-radius-input" type="text" class="form-control condition-input-box-main"
            aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"
            value="<?php if($SESSION_DATA->success):echo $MEMBER_INFO['search_radius']; else:echo '1500'; endif;?>">
          <span class="input-group-text" id="inputGroup-sizing-sm">公尺</span>
        </div>
        <div style="display:flex;align-items:center;margin-top:10px;">
          <input type="radio" id="condition-geo-radio" name="search-type" value="geo" style="margin-right:5px;"
            <?php if($SESSION_DATA->success && $MEMBER_INFO['search_mode'] === 'geo'): echo 'checked'; else: echo 'disabled'; endif;?>>
          <label for="condition-geo-radio">
            <p class="checkbox-title" style="margin-bottom:2px;color:gray;" id="condition-geo-title">地理位置</p>
          </label>
        </div>
        <div id="comments-order-bar" class="input-group condition-input-box mb-3 sort-button">
          <span class="input-group-text condition-input-box-title" id="basic-addon1">縣市區域</span>
          <select class="form-select condition-input-box-main" aria-label="Default select example" id="condition-city-select" onclick="updateRadio('condition')" onchange="updateArea('condition')">
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
          <select class="form-select condition-input-box-main" aria-label="Default select example" id="condition-dist-select" disabled>
            <?php if ($selectedCity === ''): ?>
              <option value="nothing" selected></option>
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
        <p class="checkbox-title" style="font-weight:normal;background:gray;color:white;text-align:center;">營業狀態</p>
        <?php $index=1; $serviceMark=false;?>
        <?php foreach($checkboxGroups as $key => $group): ?>
          <p class="checkbox-title"><?=$group['title']?></p>
          <div class="checkbox-container">
            <?php foreach($group['items'] as $item => $label): ?>
              <div class="checkbox-item">
                <input <?php if($serviceMark): echo 'class="service-mark"'; endif;?> type="checkbox" id="<?=$item?>" value=""
                  <?php if($SESSION_DATA->success): echo transformToPreference($item); else: echo $label['default']??''; endif;?>>
                <label for="<?=$item?>"><?=$label['name']?></label>
              </div>
            <?php endforeach; ?>
          </div>
          <?php if($index === 1):?>
            <p class="checkbox-title" style="font-weight:normal;background:gray;color:white;text-align:center;">服務項目</p>
          <?php endif;?>
          <?php $index++; $serviceMark=true;?>
        <?php endforeach; ?>
      </div>
      <div class="modal-footer">
        <?php if($SESSION_DATA->success): ?>
          <button type="button" class="btn btn-solid-gray" id="<?=$modalId?>-sync-button" onclick="syncToPreferences()">同步至偏好</button>
        <?php endif; ?>
        <button type="button" class="btn btn-solid-gray" id="<?=$modalId?>-confirm-button" onclick="showCondition()">完成</button>
        <button type="button" class="btn btn-solid-windows-blue" id="<?=$modalId?>-search-button" onclick="saveCondition()">搜尋</button>
      </div>
    </div>
  </div>
</div>

<script src="/scripts/condition.js" defer></script>