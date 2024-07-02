<?php
require_once 'db.php';

// 商家資料表得到(商家名稱)
function getStoreInfo($storeName) {
    global $conn;
    $sql = "SELECT * FROM stores WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $storeName);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// 留言(依商家查詢)
function getComments($storeId) {
    global $conn;
    $sql = "SELECT * FROM comments WHERE store_id = ? ORDER BY sort";
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
    return $result->fetch_assoc();
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
    $sql = "SELECT word, count FROM keywords WHERE store_id = ? ORDER BY count DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $result = $stmt->get_result();

    $keywords = [];
    while ($row = $result->fetch_assoc()) {
        if (count($keywords) < 5) {
            $keywords[] = $row;
        }
    }
    $stmt->close();
    return $keywords;
}

// 評論者(依商家查詢)?
function getContributors($storeId) {
    global $conn;
    $sql = "SELECT * FROM contributors WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}


?>
