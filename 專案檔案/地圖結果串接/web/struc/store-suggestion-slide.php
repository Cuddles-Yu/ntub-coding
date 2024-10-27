<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/analysis.php';
  global $conn;
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $memberWeights = getMemberNormalizedWeight();
      $stores = searchByRandom();
  } else {return;}
?>

<div class="slider-container">
  <button class="slider-control prev" onclick="goToPrev();">‹</button>
  <div class="slider">
    <?php foreach ($stores as $store) : ?>
        <?php
          $storeId = $store['id'];
          $storeName = htmlspecialchars($store['name']);
          $previewImage = htmlspecialchars($store['preview_image']);
          $link = htmlspecialchars($store['link']);
          $website = htmlspecialchars($store['website']);
          $tag = htmlspecialchars($store['tag']);
          $isFavorite = isFavorite($storeId);
          $mark = $store['mark'];
          $markItem = $markOptions[$mark]??null;
          $address = htmlspecialchars(getAddress($store));
          $bayesianScore = getBayesianScore($memberWeights, $storeId);
          $targetsInfo = getTargets($storeId);
        ?>
        <div class="slide card restaurant <?=$markItem['cardType']??''?> <?php if($isFavorite): echo 'store-card-favorite'; endif;?>" onclick="goToDetailPage(<?=$storeId?>)">
          <?php if($markItem):?>
            <div class="left-icon-display" style="background-color:<?=$markItem['tagColor']?>;"><?=$markItem['tagName']?></div>
          <?php endif;?>
          <link rel="preload" href="<?=$previewImage?>" as="image">
          <img src="<?=$previewImage?>" class="card-img-top">
          <div class="card-body">
              <h5 class="card-title"><?=$storeName?></h5>
              <div class="quick-group">
                <div onclick="toggleFavorite(this,<?=$storeId?>);event.stopPropagation();" draggable="false">
                  <img class="search-result-button-icon" src="<?=isFavorite($storeId)?'images/button-favorite-active.png':'images/button-favorite-inactive.png';?>">
                  <h6 class="love-text">收藏</h6>
                </div>
                <a class="map-link" href="<?=htmlspecialchars($link)?>" target="_blank" onclick="event.stopPropagation();" draggable="false">
                  <img class="search-result-button-icon" src="images/button-map.png">
                  <h6 class="map-link-text">地圖</h6>
                </a>
                <?php if (!empty($website)) : ?>
                  <a class="map-link" href="<?=htmlspecialchars($website)?>" target="_blank" onclick="event.stopPropagation();" draggable="false">
                    <img class="search-result-button-icon" src="images/button-browse.png">
                    <h6 class="web-text">官網</h6>
                  </a>
                <?php endif?>
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
              <h6 class="restaurant-style">類別：<?=$tag?></h6>
              <h6 class="address">地址：<?=$address?></h6>
            </div>
        </div>
    <?php endforeach; ?>
  </div>
  <button class="slider-control next" onclick="goToNext();">›</button>
</div>