<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/queries.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/analysis.php';

  $inputJSON = file_get_contents('php://input');
  $input = json_decode($inputJSON, true);
  $storeData = $input['data'] ?? [];
?>

<?php if (!empty($storeData)) : ?>
  <?php foreach ($storeData as $storeItem) : ?>      
    <?php
      $STORE_ID = $storeItem['id'];
      $bayesianScore = $storeItem['score'];

      $targetsInfo = getTargets($STORE_ID);
      $isFavorite = isFavorite($STORE_ID);
      $distance = normalizeDistance($storeItem['distance']);      
      $tag = htmlspecialchars($storeItem['tag']);
      $storeName = htmlspecialchars($storeItem['name']);
      $preview_image = htmlspecialchars($storeItem['preview_image']);
      $location = htmlspecialchars(getAddress($storeItem));
      $link = htmlspecialchars($storeItem['link']);
      $website = htmlspecialchars($storeItem['website']);

      $mark = $storeItem['mark'];
      $cardType = $markOptions[$mark]['cardType'] ?? '';
      $tagName = $markOptions[$mark]['tagName'] ?? '';
      
      $normalizedWeights = getMemberNormalizedWeight();
      $rowIndex = 1;
    ?>
    <div class="container-fluid store-body <?=$cardType?> <?php if($isFavorite): echo 'store-card-favorite'; endif;?>" data-id="<?=$STORE_ID?>" onclick="redirectToDetailPage('<?=$STORE_ID?>')">
        <div class="row">
            <div class="store-img-group col-3">
              <img class="store-img" src="<?=$preview_image?>">
            </div>
            <div class="store-right col">
            <div class="distance-name-display">
                <!--距離--><div class="distance"><?=$distance?></div>
                <!--名稱--><h5 class="store-name"><?=$storeName?></h5>
              </div>
                <div class="store-information row">
                    <div class="col-6">                                     
                      <!--綜合評分--><h5 class="rating"><?=$bayesianScore?><small class="rating-text"> / 綜合評分</small></h5>                                            
                      <!--餐廳分類--><h6 class="restaurant-style">類別：<?=$tag?><?=$tagName?></h6>
                      <!--餐廳地址--><h6 class="address">地址：<?=$location?></h6>
                    </div>
                    <div class="progress-group-text col">
                      <?php foreach ($normalizedWeights as $category => $data): ?>
                        <?php
                          $result = getProportionScore($category);
                          $proportion = $result['proportion'];
                          $score = $result['score'];
                        ?>
                        <tr class="row<?=$rowIndex?>"></tr>
                          <div class="progress-group">
                            <div class="progress-text" style="color: <?=$data['color']?>;"><?=$category?></div>
                            <div class="progress" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar overflow-visible" style="width: <?=$proportion.'%'?>; background-color: <?=$data['color']?>;"></div>
                            </div>
                            <div class="progress-score" style="color: <?=$data['color']?>;"><?=number_format($proportion,1).'分'?></div>
                          </div>
                        </tr>
                        <?php $rowIndex++?>
                      <?php endforeach?>
                    </div>
                    <div class="quick-group col-2">
                      <div onclick="toggleFavorite(this,<?=$STORE_ID?>);event.stopPropagation();">
                        <img class="search-result-button-icon" src="<?=$isFavorite?'images/button-favorite-active.png':'images/button-favorite-inactive.png';?>">
                        <h6 class="love-text">收藏</h6>
                      </div>
                      <a class="map-link" href="<?=$link?>" target="_blank" onclick="event.stopPropagation();">
                        <img class="search-result-button-icon" src="images/button-map.png">
                        <h6 class="map-link-text">地圖</h6>
                      </a>
                      <?php if (!empty($website)) : ?>
                        <a class="map-link" href="<?=$website?>" target="_blank" onclick="event.stopPropagation();">
                          <img class="search-result-button-icon" src="images/button-browse.png">
                          <h6 class="web-text">官網</h6>
                        </a>
                      <?php endif?>
                    </div>
                </div>
            </div>
        </div>
    </div>
  <?php endforeach?>
<?php else : ?>
    <p style="text-align:center;">沒有找到相關結果。</p>
<?php endif?>
