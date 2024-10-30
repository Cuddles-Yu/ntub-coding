<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/queries.php';
  $landmarkCategories = getLandmarkCategories();
  $modalTitle = '<i class="fi fi-sr-marker filter-img button-text-icon"></i>快速定位';
  $modalId = 'navigation';
?>

<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="<?=$modalId?>Modal" aria-hidden="true" tabindex="-1" aria-labelledby="<?=$modalId?>ModalLabel">
  <div class="modal-dialog form-dialog modal-dialog-centered modal-dialog-scrollable login-modal">
    <div class="modal-content">
      <h2 class="form-h2-title" style="padding-top:20px;"><?=$modalTitle?></h2>
      <div class="modal-body login-modal-body" style="padding-top:10px;">
        <div id="comments-order-bar" class="input-group navigation-input-box mb-3 sort-button">
          <span class="input-group-text condition-input-box-title" id="basic-addon1">捷運車站</span>
          <select class="form-select condition-input-box-main" aria-label="Default select example" id="navigation-category-select" onchange="updateLandmark()">
            <option value="" selected>(未選擇)</option>
            <?php foreach($landmarkCategories as $category): ?>
              <option value="<?=$category?>"><?=$category?>
              </option>
            <?php endforeach; ?>
          </select>
          <select class="form-select condition-input-box-main" aria-label="Default select example" id="navigation-landmark-select" onchange="checkLandmark()" disabled>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-solid-gray" id="<?=$modalId?>-cancel-button" data-bs-dismiss="modal" onclick="showCondition();">關閉</button>
        <button type="button" class="btn btn-solid-green" id="<?=$modalId?>-confirm-button" data-bs-dismiss="modal" onclick="setLandmark();showCondition();" disabled>定位</button>
        <button type="button" class="btn btn-solid-windows-blue" id="<?=$modalId?>-search-button" data-bs-dismiss="modal" onclick="setLandmark();saveCondition(1000);" disabled>搜尋</button>
      </div>
    </div>
  </div>
</div>

<script src="/scripts/condition.js?v=<?=$VERSION?>" defer></script>