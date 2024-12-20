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
      $storeId = $storeItem['id'];
      $bayesianScore = $storeItem['score'];

      $targetsInfo = getTargets($storeId);
      $isFavorite = isFavorite($storeId);
      $distance = $storeItem['distance']?normalizeDistance($storeItem['distance']):null;
      $tag = htmlspecialchars($storeItem['tag']);
      $storeName = htmlspecialchars($storeItem['name']);
      $previewImage = htmlspecialchars($storeItem['preview_image']);
      $location = htmlspecialchars(getAddress($storeItem));
      $link = htmlspecialchars($storeItem['link']);
      $website = htmlspecialchars($storeItem['website']);

      $mark = $storeItem['mark'];
      $markItem = $markOptions[$mark] ?? null;

      $normalizedWeights = getMemberNormalizedWeight();
      $rowIndex = 1;
    ?>
    <div class="clickable-shadow clickable-transform container-fluid store-body <?=$markItem['cardType']??''?> <?php if($isFavorite): echo 'store-card-favorite'; endif;?>" data-id="<?=$storeId?>" onclick="redirectToDetailPage(<?=$storeId?>)">
        <div class="row">
            <div class="store-img-group col-3">
              <!-- <div class="service-match-counter">符合四項</div> -->
              <!-- <img scr="/struc/store-image.php?id=4744"class="store-img"> -->
              <?php if($markItem):?>
                <div class="left-icon-display" style="background-color:<?=$markItem['tagColor']?>;"><?=$markItem['tagName']?></div>
              <?php endif;?>
              <link rel="preload" href="<?=$previewImage?>" as="image">
              <img src="<?=$previewImage?>" class="store-img">
            </div>
            <div class="store-right col">
            <div class="distance-name-display">
                <?php if($distance):?>
                  <div class="distance"><?=$distance?></div>
                <?php endif;?>
                <h5 class="store-name"><?=$storeName?></h5>
              </div>
                <div class="store-information row">
                    <div class="col-6">
                      <h5 class="rating"><?=$bayesianScore?><small class="rating-text"> / 綜合評分</small></h5>
                      <h6 class="store-card-text">類別：<?=$tag?></h6>
                      <h6 class="store-card-text">地址：<?=$location?></h6>
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
                      <div class="clickable-overlay" onclick="preventMultipleClick(event);toggleFavorite(this,<?=$storeId?>);">
                        <i class="small-toolbar-button fi <?=$isFavorite?'fi-sr-heart':'fi-br-heart'?>"></i>
                        <h6 class="small-toolbar-text">收藏</h6>
                      </div>
                      <div class="clickable-overlay" onclick="preventMultipleClick(event);shareStore(<?=$storeId?>);">
                        <i class="small-toolbar-button fi fi-sr-share"></i>
                        <h6 class="small-toolbar-text">分享</h6>
                      </div>
                      <div class="clickable-overlay" onclick="preventMultipleClick(event);highlightMarkerById(<?=$storeId?>);">
                        <img class="search-result-button-icon" style="right:-5px;top:1px;" src="/images/location-mark1.png">
                        <h6 class="small-toolbar-text">地標</h6>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  <?php endforeach?>
<?php else : ?>
    <p style="text-align:center;">沒有找到相關結果。</p>
<?php endif?>
