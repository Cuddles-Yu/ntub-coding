<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/db.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/queries.php';

$storeId = $_GET['storeId'] ?? null;

if (is_null($storeId)) {
    echo json_encode(['error' => '無效的店家 ID']);
    exit;
}

// 查詢資料
function getStoreData($storeId) {
    global $conn;
    $sql = 
    " SELECT 
        s.id AS store_id,
        s.name AS store_name,
        s.link AS store_link,
        s.preview_image AS store_preview_image,
        l.latitude AS location_latitude,
        l.longitude AS location_longitude, 
        s.tag AS tag,
        r.real_rating AS rate_real_rating,      
        r.total_samples AS sample_ratings,      
        r.total_withcomments AS total_withcomments,
        r.avg_ratings AS avg_ratings
      FROM stores AS s
      JOIN locations AS l ON s.id = l.store_id
      JOIN rates AS r ON s.id = r.store_id
      WHERE s.id = $storeId AND crawler_state IN ('成功', '完成', '超時');
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = array(
                'id' => $row['store_id'],
                'name' => $row['store_name'],
                'link' => $row['store_link'],
                'preview_image' => $row['store_preview_image'],
                'latitude' => floatval($row['location_latitude']), 
                'longitude' => floatval($row['location_longitude']), //緯度latitude再經度longitude
                'tag' => $row['tag'], //商家標籤
                'rating' => floatval($row['avg_ratings']), //這間商家的google評分
                'sample_ratings' =>intval($row['sample_ratings'] ), //抓取的樣本總數(有留言+沒有留言)
                'total_withcomments' =>intval($row['total_withcomments'] ), //有留言的總數
            );
        }
    } else {
        echo json_encode(['error' => '找不到店家資訊']);
        exit;
    }
    $conn->close();
    return $data;
}

// 將數據轉換為 JSON 格式並輸出
header('Content-Type: application/json');
echo json_encode(getStoreData($storeId));
