<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/queries.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/analysis.php';

  $memberWeights = getMemberNormalizedWeight();

  $keyword = array_key_exists('q', $_POST) ? htmlspecialchars($_POST['q']) : null;
  $mapCenterLat = isset($_POST['mapCenterLat']) ? floatval($_POST['mapCenterLat']) : null;
  $mapCenterLng = isset($_POST['mapCenterLng']) ? floatval($_POST['mapCenterLng']) : null;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if (is_null($keyword)) return;
      $stores = searchByLocation($keyword, $mapCenterLat, $mapCenterLng, $RESULT_LIMIT);
      $storeData = [];
      foreach ($stores as $store) {
          $storeId = $store['id'];
          $bayesianScore = getBayesianScore($memberWeights, $storeId);
          $storeData[] = [
              'store' => $store,
              'bayesianScore' => $bayesianScore
          ];
      }
      // 根據Bayesian Score從高到低進行排序
      usort($storeData, function ($a, $b) {
          return $b['bayesianScore'] <=> $a['bayesianScore'];
      });
  } else {
      exit;
  }
?>

<?php if (!empty($storeData)) : ?>
  <?php foreach ($storeData as $storeItem) : ?>      
    <?php
      $store = $storeItem['store'];
      $storeId = $store['id'];
      $bayesianScore = $storeItem['bayesianScore'];

      $targetsInfo = getTargets($storeId);
      $distance = normalizeDistance($store['distance']);      
      $tag = htmlspecialchars($store['tag']);
      $storeName = htmlspecialchars($store['name']);
      $preview_image = htmlspecialchars($store['preview_image']);
      $location = htmlspecialchars(getAddress($store));

      $mark = $store['mark'];
      $cardType = $markOptions[$mark]['cardType'] ?? '';
      $tagName = $markOptions[$mark]['tagName'] ?? '';
      
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
    <div class="container-fluid store-body" data-id="<?=$storeId?>" onclick="redirectToDetailPage('<?=$storeId?>')">
        <div class="row <?=$cardType?>">
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
                      <?php foreach ($categories as $category => $data): ?>
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
                        <a class="love" href="#"><img class="love-img" src="images/button-favorite.png"><h6 class="love-text">最愛</h6></a>
                        <a class="map-link" href="<?=htmlspecialchars($store['link'])?>" target="_blank" onclick="event.stopPropagation();"><img class="map-link-img" src="images/button-map.png"><h6 class="map-link-text">地圖</h6></a>
                        <a class="web" href="<?=htmlspecialchars($store['website'])?>" target="_blank" onclick="event.stopPropagation();"><img class="web-img" src="images/button-browse.png"><h6 class="web-text">官網</h6></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
  <?php endforeach?>
<?php else : ?>
    <p style="text-align:center;">沒有找到相關結果。</p>
<?php endif?>