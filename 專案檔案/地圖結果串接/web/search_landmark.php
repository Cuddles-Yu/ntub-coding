<?php 
// 動態生成搜尋結果的地標(就是按下搜尋後，地圖上才會出現的標記)
require_once 'db.php';

function searchStores($location, $keyword)
{
    global $conn;
    $keyword = "%" . $keyword . "%";
    $sql =
        "   SELECT DISTINCT s.name,s.preview_image,s.link, s.website, r.avg_ratings, r.total_reviews, l.city, l.details, s.tag, r.environment_rating, r.product_rating, r.service_rating, r.price_rating, r.total_samples AS sample_ratings, r.total_withcomments, l.latitude, l.longitude 
        FROM stores AS s
        INNER JOIN keywords AS k ON s.id = k.store_id
        INNER JOIN rates AS r ON s.id = r.store_id
        INNER JOIN locations AS l ON s.id = l.store_id
        INNER JOIN tags AS t ON s.tag = t.tag
        WHERE l.city = ? AND (s.name LIKE ? OR s.description LIKE ? OR t.category LIKE ? OR s.tag LIKE ? OR k.word LIKE ?)
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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stores = searchStores($location, $keyword);
}

$data = [];
foreach ($stores as $store) {
    $data[] = [
        'name' => $store['name'],
        'latitude' => $store['latitude'],
        'longitude' => $store['longitude'],
        'rating' => $store['avg_ratings'],
        'tag' => $store['tag'],
        'preview_image' => $store['preview_image'],
        'sample_ratings' => $store['sample_ratings'],
        'total_withcomments' => $store['total_withcomments']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);

?>