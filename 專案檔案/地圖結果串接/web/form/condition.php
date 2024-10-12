<?php
  global $modalTitle, $modalId;

  $checkboxGroups = [
    'open_hour_button' => [
      'title' => '營業時間(至少選擇一項)',
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
      <div class="modal-body login-modal-body" style="height:350px;padding-top:10px;padding-right: 0;">
        <p class="checkbox-title" style="font-weight:normal;background:gray;color:white;text-align:center;">基本條件</p>
        <p class="checkbox-title">搜尋半徑(介於100-10000之間)</p>
        <div class="input-group input-group-sm mb-3" style="width:120px;margin-top:10px;">          
          <input id="<?=$modalId?>-search-radius-input" type="text" class="form-control" 
            aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" 
            value="<?php if($SESSION_DATA->success): echo $MEMBER_INFO['search_radius']; else: echo '1500'; endif;?>">
          <span class="input-group-text" id="inputGroup-sizing-sm">公尺</span>
        </div>
        <?php $index=1;?>
        <?php foreach($checkboxGroups as $key => $group): ?>
          <p class="checkbox-title"><?=$group['title']?></p>
          <div class="checkbox-container">
            <?php foreach($group['items'] as $item => $label): ?>
              <div class="checkbox-item">
                <input type="checkbox" id="<?=$item?>" value=""                    
                  <?php if($SESSION_DATA->success): echo transformToPreference($item); else: echo $label['default']??''; endif;?>>
                <label for="<?=$item?>"><?=$label['name']?></label>
              </div>
            <?php endforeach; ?>
          </div>
          <?php if($index === 1):?>
            <p class="checkbox-title" style="font-weight:normal;background:gray;color:white;text-align:center;">服務項目</p>
          <?php endif;?>
          <?php $index++;?>
        <?php endforeach; ?>
      </div>
      <div class="modal-footer">
        <?php if($SESSION_DATA->success): ?>
          <button type="button" class="btn btn-solid-gray" id="<?=$modalId?>-sync-button" onclick="syncToPreferences()">同步至偏好</button>
        <?php endif; ?>
        <button type="button" class="btn btn-solid-windows-blue" id="<?=$modalId?>-confirm-button" onclick="closeOpenedModal()">完成</button>
      </div>
    </div>
  </div>
</div>