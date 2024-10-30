<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/analysis.php';
  global $conn;
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode']??'';
    $memberWeights = getMemberNormalizedWeight();
    if ($mode === 'hakka') {
      $stores = searchByStoreMark('客家');
    } elseif ($mode === 'environmental') {
      $stores = searchByStoreMark('環保');
    } else {return;}
  } else {return;}
?>

<div class="carousel-container">
  <div class="store-roster-group">
    <?php if (!empty($stores)) : ?>
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
        <div class="clickable-shadow clickable-transform card half-store-card <?php if($isFavorite): echo 'store-card-favorite'; endif;?>" onclick="goToDetailPage(<?=$storeId?>)">
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
              <h6 class="store-card-text">類別：<?=$tag?></h6>
              <h6 class="store-card-text">地址：<?=$address?></h6>
            </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>