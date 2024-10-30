<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/analysis.php';
  global $conn;
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode']??'';
    $memberWeights = getMemberNormalizedWeight();
    if ($mode === 'random') {
      $stores = searchByRandom();
      $targetMode = '隨機推薦';
    } else if ($mode === 'most-browse') {
      $stores = searchByBrowse();
      $targetMode = '最多人瀏覽';
    } else if ($mode === 'most-favorite') {
      $stores = searchByFavorite();
      $targetMode = '最多人收藏';
    }
  } else {return;}
?>

<div class="carousel-container">
  <div class="carousel-arrow left-arrow" type="button"><i class="fi fi-sr-angle-left"></i></div>
  <div class="store-suggestion-group grab-container">
    <?php if (empty($stores)) : ?>
      <div style="width:100%;">
        <p style="text-align:center;">數據不足，暫時沒有可以推薦給您<?=$targetMode?>的餐廳</p>
      </div>
    <?php else: ?>
      <?php foreach ($stores as $store) : ?>
        <?php
          $storeId = $store['id'];
          $storeName = htmlspecialchars($store['name']);
          $previewImage = htmlspecialchars($store['preview_image']);
          $link = htmlspecialchars($store['link']);
          $website = htmlspecialchars($store['website']);
          $tag = htmlspecialchars($store['tag']);
          $isFavorite = isFavorite($storeId);
          $suggestCount = $store['count']??null;
          $mark = $store['mark'];
          $markItem = $markOptions[$mark]??null;
          $address = htmlspecialchars(getAddress($store));
          $bayesianScore = getBayesianScore($memberWeights, $storeId);
          $targetsInfo = getTargets($storeId);
        ?>
        <div class="clickable-shadow clickable-transform card full-store-card <?=$markItem['cardType']??''?> <?php if($isFavorite): echo 'store-card-favorite'; endif;?>" onclick="goToDetailPage(<?=$storeId?>)">
          <?php if($markItem):?>
            <div class="left-icon-display" style="background-color:<?=$markItem['tagColor']?>;"><?=$markItem['tagName']?></div>
          <?php elseif($mode === 'most-favorite'):?>
            <div class="left-icon-display" style="background-color:goldenrod;font-size:14px!important;font-weight:normal">被 <?=$suggestCount?> 個會員收藏</div>
          <?php elseif($mode === 'most-browse'):?>
            <div class="left-icon-display" style="background-color:steelblue;font-size:14px!important;font-weight:normal">被瀏覽 <?=$suggestCount?> 次</div>
          <?php endif;?>
          <link rel="preload" href="<?=$previewImage?>" as="image">
          <img src="<?=$previewImage?>" class="card-img-top">
          <div class="card-body">
              <h5 class="card-title"><?=$storeName?></h5>
              <div class="small-toolbar">
                <div class="clickable-overlay" onclick="preventMultipleClick(event);toggleFavorite(this,<?=$storeId?>);">
                <i class="small-toolbar-button fi <?=$isFavorite?'fi-sr-bookmark':'fi-br-bookmark'?>"></i>
                  <h6 class="small-toolbar-text">收藏</h6>
                </div>
                <div class="clickable-overlay" onclick="preventMultipleClick(event);shareStore(<?=$storeId?>);">
                <i class="small-toolbar-button fi fi-sr-share"></i>
                  <h6 class="small-toolbar-text">分享</h6>
                </div>
              </div>
              <h5 class="rating"><small style="font-size:25px;"><?=$bayesianScore?></small><small class="rating-text">/ 綜合評分</small></h5>
              <div class="progress-group-text">
                <?php
                  $normalizedWeights = [
                    $_ATMOSPHERE => ['weight' => '30', 'color' => '#562B08'],
                    $_PRODUCT => ['weight' => '30', 'color' => '#7B8F60'],
                    $_SERVICE => ['weight' => '30', 'color' => '#5053AF'],
                    $_PRICE => ['weight' => '30', 'color' => '#C19237'],
                  ];
                  uasort($normalizedWeights, function ($a, $b) {
                    return $b['weight'] <=> $a['weight'];
                  });
                  $rowIndex = 1;
                ?>
                <?php foreach ($normalizedWeights as $category => $data): ?>
                  <?php
                    $result = getProportionScore($category);
                    $proportion = $result['proportion'];
                    $score = $result['score'];
                  ?>
                  <tr class="row<?= $rowIndex ?>">
                    <div class="progress-group">
                      <div class="progress-text" style="color: <?=$data['color']?>;"><?=$category?></div>
                      <div class="progress" role="progressbar" aria-label="Warning example" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                        style="--bs-progress-bg:lightgray;">
                        <div class="progress-bar overflow-visible" style="width: <?=$proportion?>%; background-color: <?=$data['color']?>;"></div>
                      </div>
                      <div class="progress-score" style="color: <?=$data['color']?>;"><?=$score?></div>
                    </div>
                  </tr>
                  <?php $rowIndex++; ?>
                <?php endforeach; ?>
              </div>
              <h6 class="store-card-text">類別：<?=$tag?></h6>
              <h6 class="store-card-text">地址：<?=$address?></h6>
            </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <div class="carousel-arrow right-arrow" type="button"><i class="fi fi-sr-angle-right"></i></div>
</div>