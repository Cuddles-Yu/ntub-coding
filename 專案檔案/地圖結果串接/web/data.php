<?php //抓取資料庫所有商家的資料，將資料轉為json格式

require_once 'DB.php';
require_once 'queries.php';

// 查詢資料
function getStoreData() {
    global $conn;

    $sql = "
        SELECT 
            stores.name AS store_name,
            stores.preview_image AS store_preview_image,
            locations.latitude AS location_latitude,
            locations.longitude AS location_longitude, 
            stores.tag AS tag,
            rates.real_rating AS rate_real_rating,      
            rates.total_samples AS sample_ratings,      
            rates.total_withcomments AS total_withcomments,
            rates.avg_ratings AS avg_ratings    

        FROM 
            stores
        JOIN 
            locations ON stores.id = locations.store_id
        JOIN 
            rates ON stores.id = rates.store_id
            
        WHERE crawler_state IN ('成功', '完成', '超時');
    ";

    $result = $conn->query($sql);

    if ($result === false) {
        echo"SQL Error: " . $conn->error;
        exit();
    }

    $data = array();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = array(
                'name' => $row['store_name'],
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
        echo json_encode([]);
        exit();
    }

    $conn->close();
    return $data;
}

// 將數據轉換為 JSON 格式並輸出
header('Content-Type: application/json');
echo json_encode(getStoreData());
?>

