<?php //網站首頁
require_once 'db.php';

function searchStores($keyword)
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
        WHERE (s.name LIKE ? OR s.description LIKE ? OR t.category LIKE ? OR s.tag LIKE ? OR k.word LIKE ?)
        AND s.crawler_state IN ('成功', '完成', '超時')
        ORDER BY r.avg_ratings DESC, r.total_reviews DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssss",
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

$keyword = $_POST["keyword"] ?? "";

$stores = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($keyword)) {
    $stores = searchStores($keyword);
}

?>