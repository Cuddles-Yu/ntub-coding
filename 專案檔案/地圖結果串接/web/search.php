<?php
require_once 'db.php';
require_once 'queries.php';

function searchStores($location, $keyword)
{
    global $conn;
    $keyword = "%" . $keyword . "%";
    $sql =
        "   SELECT DISTINCT s.id,s.name,s.preview_image,s.link, s.website, r.avg_ratings, r.total_reviews, l.city, l.details, s.tag, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, l.latitude, l.longitude 
        FROM stores AS s
        INNER JOIN keywords AS k ON s.id = k.store_id
        INNER JOIN rates AS r ON s.id = r.store_id
        INNER JOIN locations AS l ON s.id = l.store_id
        INNER JOIN tags AS t ON s.tag = t.tag
        WHERE l.city = ? 
        AND (s.name LIKE ? OR s.description LIKE ? OR t.category LIKE ? OR s.tag LIKE ? OR k.word LIKE ?)
        AND s.crawler_state IN ('成功', '完成', '超時')
        ORDER BY r.avg_ratings DESC, r.total_reviews DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssss",
        $location,
        $keyword,
        $keyword,
        $keyword,
        $keyword,
        $keyword
    );
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

$location = "台北市";
$keyword = $_POST["keyword"] ?? "";

$stores = [];
if ($_SERVER["REQUEST_METHOD"] ==  "POST"&& !empty($keyword)) {
    $stores = searchStores($location, $keyword);
}
?>

<!-- 動態生成搜尋結果 -->
<div id="searchResults" class="store">
    <?php if (!empty($stores)) : ?>
        <?php foreach ($stores as $store) : ?>
            <div class="container-fluid store-body" onclick="redirectToDetailPage('<?php echo htmlspecialchars($store['name']); ?>', '<?php echo htmlspecialchars($store['id']); ?>')"> <!--整框商家 可點下進入詳細頁面-->
                <div class="row">
                    <div class="store-img-group col-3">
                        <img class="store-img" src="<?php echo htmlspecialchars($store['preview_image']); ?>"><!--填入商家照片-->
                    </div>
                    <div class="store-right col">
                        <h5 class="store-name"><?php echo htmlspecialchars($store['name']); ?></h5><!--填入商家名稱-->
                        <div class="store-information row">
                            <div class="col-5">
                                <h5 class="rating"><?php echo htmlspecialchars($store['avg_ratings']) * 20; ?><!--填入綜合評分--><small class="rating-text">/(綜合)評分</small></h5>
                                <h6 class="restaurant-style">分類: <?php echo htmlspecialchars($store['tag']); ?></h6><!--填入餐廳分類-->
                                <h6 class="address">地址: <?php echo htmlspecialchars($store['city'] . $store['details']); ?></h6><!--填入餐廳地址-->
                            </div>
                            <div class="progress-group-text col">                                
                                <div class="progress-group">
                                    <div class="progress-text" style="color: #562B08;">氛圍 40%</div>
                                    <div class="progress col" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar overflow-visible" style="width: 25%; background-color: #562B08;"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <div class="progress-text" style="color: #7B8F60;">產品 50%</div>
                                    <div class="progress col" role="progressbar" aria-label="Info example" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar overflow-visible" style="width: 50%; background-color: #7B8F60;"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <div class="progress-text" style="color: #5053AF;">服務 70%</div>
                                    <div class="progress col" role="progressbar" aria-label="Warning example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar overflow-visible" style="width: 75%; background-color: #5053AF;"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <div class="progress-text" style="color: #C19237;">售價 75%</div>
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
</div>