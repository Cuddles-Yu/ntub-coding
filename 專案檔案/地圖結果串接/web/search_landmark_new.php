<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/db.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/queries.php';


// 查詢資料
function getStoreData($keyword) {
    global $conn;
    $keyword = "%" . $keyword . "%";
    $sql =
    " SELECT DISTINCT 
        s.id AS id,
        s.name AS name,
        s.preview_image AS preview_image,
        s.link AS link,         
        s.tag AS tag, 
        s.website AS website, 
        r.avg_ratings AS avg_ratings,
        r.total_reviews AS total_reviews, 
        l.city AS city, 
        l.dist AS dist,
        l.details AS details,        
        l.latitude AS latitude, 
        l.longitude AS longitude, 
        r.environment_rating AS environment_rating, 
        r.product_rating AS product_rating, 
        r.service_rating AS service_rating, 
        r.price_rating AS price_rating, 
        r.total_samples AS sample_ratings,
        r.total_withcomments AS total_withcomments, 
      FROM stores AS s
      INNER JOIN keywords AS k ON s.id = k.store_id
      INNER JOIN rates AS r ON s.id = r.store_id
      INNER JOIN locations AS l ON s.id = l.store_id
      INNER JOIN tags AS t ON s.tag = t.tag
      WHERE (s.name LIKE $keyword OR t.category LIKE $keyword OR s.tag LIKE $keyword OR k.word LIKE $keyword)
      ORDER BY r.avg_ratings DESC, r.total_reviews DESC
      LIMIT 1
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = array(
              'id' => $row['id'],
              'name' => $row['name'],
              'latitude' => $row['latitude'],
              'longitude' => $row['longitude'],
              'rating' => $row['avg_ratings'],
              'tag' => $row['tag'],
              'preview_image' => $row['preview_image'],
              'sample_ratings' => $row['sample_ratings'],
              'total_withcomments' => $row['total_withcomments']
            );
        }
    } else {
        echo json_encode(['error' => '找不到店家資訊']);
        exit;
    }
    $conn->close();
    return $data;
}

$keyword = $_POST["q"] ?? "";

$stores = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stores = getStoreData($keyword);
}

// 將數據轉換為 JSON 格式並輸出
header('Content-Type: application/json');
echo json_encode(getStoreData($keyword));
