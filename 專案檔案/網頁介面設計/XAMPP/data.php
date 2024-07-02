<!-- <?php
header('Content-Type: application/json');
require_once 'DB.php'; 

// $key = $value
$name = $_POST['name'];
$location = $_POST['location'];
$adjective = $_POST['adjective'];
$keyword = $_POST['keyword'];

//根據搜索條件從資料庫中提取數據
$query = "SELECT * FROM stores WHERE location LIKE '%$location%' AND (adjective LIKE '%$adjective%' OR keyword LIKE '%$keyword%')";
$result = mysqli_query($conn, $query);

$stores = array();
while($row = mysqli_fetch_assoc($result)) {
    $stores[] = $row;
}

echo json_encode($stores);



?> -->

<?php
require_once 'db.php'; 

// 查詢用戶信息
$sql_users = "SELECT * FROM users"; // 這裡的 SQL 查詢應根據你的需求定義
$result_users = $conn->query($sql_users);

// 查詢訂單信息
$sql_orders = "SELECT * FROM orders"; // 這裡的 SQL 查詢應根據你的需求定義
$result_orders = $conn->query($sql_orders);

// 檢查查詢結果並處理
if ($result_users->num_rows > 0 && $result_orders->num_rows > 0) {
    $users = [];
    while ($row = $result_users->fetch_assoc()) {
        $users[] = $row;
    }

    $orders = [];
    while ($row = $result_orders->fetch_assoc()) {
        $orders[] = $row;
    }

    // 將數據組合成一個關聯數組
    $data = [
        'users' => $users,
        'orders' => $orders
    ];

    // 將關聯數組轉換為 JSON 格式
    $json = json_encode($data);

    // 輸出 JSON 數據
    echo $json;
} else {
    echo "查詢結果為空";
}

// 關閉數據庫連接
$conn->close();
?>