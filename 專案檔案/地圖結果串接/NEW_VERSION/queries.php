<?php
require_once 'DB.php';

// 商家資料表得到(商家名稱)
function getStoreInfo($storeName) {
    global $conn;
    $sql = "SELECT * FROM stores WHERE name = ? AND crawler_state IN ('成功', '完成', '超時')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $storeName);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}


// 留言(依商家查詢)
function getComments($storeId) {
    global $conn;
    $sql = "SELECT * FROM comments WHERE store_id = ? ORDER BY  `id` DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    return $comments;
}

// 地址(依商家查詢)
function getLocation($storeId) {
    global $conn;
    $sql = "SELECT * FROM locations WHERE store_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// 評價(依商家查詢)
function getRating($storeId) {
    global $conn;
    $sql = "SELECT * FROM rates WHERE store_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// 服務(依商家查詢)
function getService($storeId) {
    global $conn;
    $sql = "SELECT * FROM services WHERE store_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $services = [];
    while ($row = $result->fetch_assoc()) {
        // 檢查 category 並進行分類
        if (in_array($row['category'], ['服務項目', '付款方式', '規劃', '無障礙程度','停車場','設施'])) {
            $services[$row['category']][] = $row;
        } else {
            $services['其他'][] = $row;
        }
    }
    $stmt->close();
    return $services;
}

// 關鍵字(依商家查詢)
function getKeyword($storeId) {
    global $conn;
    $sql = "SELECT * FROM keywords WHERE store_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// 所有的關鍵字(依商家查詢從多到少)
function getAllKeywords($storeId) {
    global $conn;
    $sql = "SELECT word, count FROM keywords WHERE store_id = ? and source = 'google' ORDER BY count DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $result = $stmt->get_result();

    $keywords = [];
    while ($row = $result->fetch_assoc()) {
            $keywords[] = $row;
    }
    $stmt->close();
    return $keywords;
}

//營業時間(依商家查詢)
function getOpeningHours($storeId) {
    global $conn;
    $sql = "SELECT * FROM openhours WHERE store_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// 其他分店
function getOtherBranches($branchTitle, $storeId) {
    global $conn;
    $sql = "SELECT s.*, t.tag, r.avg_ratings, l.city, l.dist, l.vil, l.details 
            FROM stores AS s 
            LEFT JOIN tags AS t ON s.tag = t.tag 
            LEFT JOIN rates AS r ON s.id = r.store_id
            LEFT JOIN locations AS l ON s.id = l.store_id
            WHERE s.crawler_state IN ('成功', '完成', '超時') 
            AND s.branch_title = ? 
            AND s.id != ?";  // 排除當前分店

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("si", $branchTitle, $storeId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $branches = [];
    while ($row = $result->fetch_assoc()) {
        $branches[] = $row;
    }
    $stmt->close();
    return $branches;
}
?>
