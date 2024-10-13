<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/analysis.php';
  global $conn;

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $searchTerm = isset($_POST['q']) ? $_POST['q'] : '';
      $stores = searchByKeyword($searchTerm);
      $memberWeights = getMemberNormalizedWeight();
  }
?>

<div class="carousel-container">
  <div class="carousel-arrow left-arrow" type="button">
    <i class="fi fi-sr-angle-left"></i>
  </div>
  <div class="restaurant-group">
    <?php foreach ($stores as $store) : ?>
      <?php        
        $STORE_ID = $store['id'];
        $storeName = htmlspecialchars($store['name']);
        $previewImage = htmlspecialchars($store['preview_image']);
        $link = htmlspecialchars($store['link']);
        $website = htmlspecialchars($store['website']);
        $tag = htmlspecialchars($store['tag']);
        $mark = $store['mark'];
        $isFavorite = isFavorite($STORE_ID);
        $cardType = $markOptions[$mark]['cardType'] ?? '';
        $tagName = $markOptions[$mark]['tagName'] ?? '';
        $address = htmlspecialchars(getAddress($store));
        $bayesianScore = getBayesianScore($memberWeights, $STORE_ID);
        $targetsInfo = getTargets($STORE_ID);
      ?>
      <div class="card restaurant <?=$cardType?> <?php if($isFavorite): echo 'store-card-favorite'; endif;?>">
          <img src="<?=$previewImage?>" class="card-img-top">
          <div class="card-body">
              <h5 class="card-title"><?=$storeName?></h5>
              <div class="quick-group">
                <div onclick="toggleFavorite(this,<?=$STORE_ID?>);event.stopPropagation();">
                  <img class="search-result-button-icon" src="<?=isFavorite($STORE_ID)?'images/button-favorite-active.png':'images/button-favorite-inactive.png';?>">
                  <h6 class="love-text">收藏</h6>
                </div>
                <a class="map-link" href="<?=htmlspecialchars($link)?>" target="_blank" onclick="event.stopPropagation();">
                  <img class="search-result-button-icon" src="images/button-map.png">
                  <h6 class="map-link-text">地圖</h6>
                </a>
                <?php if (!empty($website)) : ?>
                  <a class="map-link" href="<?=htmlspecialchars($website)?>" target="_blank" onclick="event.stopPropagation();">
                    <img class="search-result-button-icon" src="images/button-browse.png">
                    <h6 class="web-text">官網</h6>
                  </a>
                <?php endif?>
              </div>
              <h5 class="rating"><small class="rating-text"><?=$bayesianScore?> / 綜合評分</small></h5>
              <div class="progress-group-text">
                <?php
                  $categories = [
                    $_ATMOSPHERE => ['weight' => '30', 'color' => '#562B08'],
                    $_PRODUCT => ['weight' => '30', 'color' => '#7B8F60'],
                    $_SERVICE => ['weight' => '30', 'color' => '#5053AF'],
                    $_PRICE => ['weight' => '30', 'color' => '#C19237'],
                  ];
                  uasort($categories, function ($a, $b) {
                    return $b['weight'] <=> $a['weight'];
                  });
                  $rowIndex = 1;
                ?>
                <?php foreach ($categories as $category => $data): ?>
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
              <h6 class="restaurant-style">類別：<?=$tag?><?=$tagName?></h6>
              <h6 class="address">地址：<?=$address?></h6>
          </div>
      </div>
    <?php endforeach; ?>    
  </div>
  <div class="carousel-arrow right-arrow" type="button">
    <i class="fi fi-sr-angle-right"></i>
  </div>
</div>