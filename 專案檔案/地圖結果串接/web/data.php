<?php //抓取資料庫有在半徑內的商家資料，將資料轉為json格式

require_once 'DB.php';
require_once 'queries.php';

// 接收 POST 中的經緯度資料
$userLat = isset($_POST['userLat']) ? floatval($_POST['userLat']) : 25.0418963; // 預設經度
$userLng = isset($_POST['userLng']) ? floatval($_POST['userLng']): 121.5230431; // 預設緯度
$radius = isset($_POST['radius']) ? intval($_POST['radius']) : 1500;  // 預設半徑 1500 公尺

// 確保使用者提供了經緯度
if (!$userLat || !$userLng) {
    echo json_encode([]);
    exit();
}

// 查詢資料
function getStoreData($userLat, $userLng, $radius)
{
    global $conn;

    $sql = "
        SELECT 
            stores.id AS store_id,
            stores.name AS store_name,
            stores.preview_image AS store_preview_image,
            locations.latitude AS location_latitude,
            locations.longitude AS location_longitude, 
            stores.tag AS tag,
            rates.real_rating AS rate_real_rating,      
            rates.total_samples AS sample_ratings,      
            rates.total_withcomments AS total_withcomments,
            rates.avg_ratings AS avg_ratings,
            ( 6371000 * acos( cos( radians(?) ) 
                * cos( radians( locations.latitude ) ) 
                * cos( radians( locations.longitude ) - radians(?) ) 
                + sin( radians(?) ) 
                * sin( radians( locations.latitude ) ) ) ) AS distance    

        FROM 
            stores
        JOIN 
            locations ON stores.id = locations.store_id
        JOIN 
            rates ON stores.id = rates.store_id
            
        WHERE 
        crawler_state IN ('成功', '完成', '超時')
        HAVING distance <= ?
        ORDER BY distance ASC
    ";

     // 預備和執行 SQL 查詢
     $stmt = $conn->prepare($sql);
     if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
     $stmt->bind_param("dddd", $userLat, $userLng, $userLat, $radius);
     $stmt->execute();
     $result = $stmt->get_result();

    $data = array();

    // 遍歷結果並準備資料
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'id' => $row['store_id'],
                'name' => $row['store_name'],
                'preview_image' => $row['store_preview_image'],
                'latitude' => floatval($row['location_latitude']),
                'longitude' => floatval($row['location_longitude']), //緯度latitude再經度longitude
                'tag' => $row['tag'], //商家標籤
                'rating' => floatval($row['avg_ratings']), //這間商家的google評分
                'sample_ratings' => intval($row['sample_ratings']), //抓取的樣本總數(有留言+沒有留言)
                'total_withcomments' => intval($row['total_withcomments']), //有留言的總數
            );
        }
    }
    // 釋放資源
    $stmt->close();
    $conn->close();
    return $data;
}

// 將數據轉換為 JSON 格式並輸出
header('Content-Type: application/json');
echo json_encode(getStoreData($userLat, $userLng, $radius));
