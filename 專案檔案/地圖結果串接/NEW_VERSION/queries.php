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


// 留言(依商家查詢) 所有有文字留言的商家
function getComments($storeId) {
    global $conn;
    $sql = "SELECT * FROM comments WHERE store_id = ? AND contents IS NOT NULL ORDER BY  rating DESC, id ASC";
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

// 留言(依商家查詢) 樣本為「最相關」有文字留言的商家
function getRelevantComments($storeId) {
    global $conn;
    $sql = "SELECT * FROM comments WHERE store_id = ? AND contents IS NOT NULL AND sample_of_most_relevant = '1' ORDER BY  rating DESC, id ASC";
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

// 留言(依商家查詢) 樣本為「評分最高」有文字留言的商家
function getHighestComments($storeId) {
    global $conn;
    $sql = "SELECT * FROM comments WHERE store_id = ? AND contents IS NOT NULL AND sample_of_highest_rating = '1' ORDER BY  rating DESC, id ASC";
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

// 留言(依商家查詢) 樣本為「評分最低」有文字留言的商家
function getLowestComments($storeId) {
    global $conn;
    $sql = "SELECT * FROM comments WHERE store_id = ? AND contents IS NOT NULL AND sample_of_lowest_rating = '1' ORDER BY rating DESC, id ASC";

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

// 推薦餐點關鍵字(依商家查詢)
function getFoodKeyword($storeId) {
    global $conn;
    $sql = "SELECT * FROM keywords WHERE store_id = ? AND source = 'recommend' ORDER BY count DESC ";
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
    // 構建營業時間
    $hours = [];
    while ($row = $result->fetch_assoc()) {
        $openingHours[$row['day_of_week']][] = [
            'open_time' => $row['open_time'],
            'close_time' => $row['close_time']
        ];
    }
    $stmt->close();
    return $openingHours;
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
