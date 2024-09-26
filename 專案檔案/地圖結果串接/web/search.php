<?php
require_once 'db.php';
require_once 'queries.php';
require_once 'analysis.php';

function searchStores($keyword, $userLat, $userLng)
{
    global $conn;
    $keyword = "'%" . $keyword  . "%'";
    $sql = "SELECT DISTINCT s.id, s.name, s.preview_image, s.link, s.website, r.avg_ratings, r.total_reviews, 
            l.city, l.dist, l.details, s.tag, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude, 
            (6371000*acos(cos(radians($userLat)) * cos(radians(l.latitude)) * cos(radians(l.longitude)-radians($userLng)) + sin(radians($userLat)) * sin(radians(l.latitude)))) AS distance
            FROM stores AS s
            INNER JOIN keywords AS k ON s.id = k.store_id
            INNER JOIN rates AS r ON s.id = r.store_id
            INNER JOIN locations AS l ON s.id = l.store_id
            INNER JOIN tags AS t ON s.tag = t.tag
            WHERE (s.name LIKE $keyword OR t.category LIKE $keyword OR s.tag LIKE $keyword OR k.word LIKE $keyword) AND s.crawler_state IN ('成功', '完成', '超時')
            HAVING distance <= 1500
            ORDER BY distance, r.avg_ratings DESC, r.total_reviews DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $stores = [];
    while ($row = $result->fetch_assoc()) {
        // 確保經緯度數據有效
        if (is_numeric($row['latitude']) && is_numeric($row['longitude'])) {
            $stores[] = $row;
        }
    }
    return $stores;
}
function getAddress($store) {
    return $store['city'].$store['dist'].$store['details'];
}

global $conn;
$userId = '';
$keyword = isset($_POST['q']) ? $_POST['q'] : '';
$userLat = isset($_POST['userLat']) ? floatval($_POST['userLat']) : null;
$userLng = isset($_POST['userLng']) ? floatval($_POST['userLng']) : null;

$stores = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($keyword)) {
    $stores = searchStores($keyword, $userLat, $userLng);
}
?>

<?php if (!empty($stores)) : ?>
  <?php foreach ($stores as $store) : ?>      
    <?php 
      $storeId = $store['id'];
      $storeName = htmlspecialchars($store['name']);
      $preview_image = htmlspecialchars($store['preview_image']);
      $tag = htmlspecialchars($store['tag']);
      $location = htmlspecialchars(getAddress($store));
      $bayesianScore = getBayesianScore($userId, $storeId, $conn);
      $targetsInfo = getTargets($storeId);
    ?>
    <div class="container-fluid store-body" onclick="redirectToDetailPage('<?php echo htmlspecialchars($store['id']); ?>')">
        <div class="row">
            <div class="store-img-group col-3">
                <!--商家照片--><img class="store-img" src="<?php echo $preview_image; ?>">
            </div>
            <div class="store-right col">
                <!--商家名稱--><h5 class="store-name"><?php echo $storeName; ?></h5>
                <div class="store-information row">
                    <div class="col-5">
                        <!--綜合評分--><h5 class="rating"><?php echo $bayesianScore; ?><small class="rating-text">/ 綜合評分</small></h5>
                        <!--餐廳分類--><h6 class="restaurant-style">類別：<?php echo $tag; ?></h6>
                        <!--餐廳地址--><h6 class="address">地址：<?php echo $location; ?></h6>
                    </div>
                    <div class="progress-group-text col">
                        <div class="progress-group">
                            <div class="progress-text " style="color: #562B08;">氛圍 40%</div>
                            <div class="progress col" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar overflow-visible" style="width: 25%; background-color: #562B08;"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            <div class="progress-text " style="color: #7B8F60;">產品 50%</div>
                            <div class="progress col" role="progressbar" aria-label="Info example" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar overflow-visible" style="width: 50%; background-color: #7B8F60;"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            <div class="progress-text " style="color: #5053AF;">服務 70%</div>
                            <div class="progress col" role="progressbar" aria-label="Warning example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar overflow-visible" style="width: 75%; background-color: #5053AF;"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            <div class="progress-text " style="color: #C19237;">售價 75%</div>
                            <div class="progress col" role="progressbar" aria-label="Danger example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar overflow-visible" style="width: 100%; background-color: #C19237;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="quick-group col-2">
                        <a class="love" href="#"><img class="love-img" src="images/love.png"><h6 class="love-text">最愛</h6></a>
                        <a class="map-link" href="<?php echo htmlspecialchars($store['link']); ?>" target="_blank" onclick="event.stopPropagation();"><img class="map-link-img" src="images/map.png"><h6 class="map-link-text">地圖</h6></a><!--href="#" #換成餐廳地圖-->
                        <a class="web" href="<?php echo htmlspecialchars($store['website']); ?>" target="_blank" onclick="event.stopPropagation();"><img class="web-img" src="images/web.png"><h6 class="web-text">官網</h6></a><!--href="#" #換成餐廳官網-->
                    </div>
                </div>
            </div>
        </div>
    </div>
  <?php endforeach; ?>
<?php else : ?>
    <p>沒有找到相關結果。</p>
<?php endif; ?>