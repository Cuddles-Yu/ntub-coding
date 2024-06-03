<?php
require 'db.php';

//商家:有category,description, link, name,phone_number,preview_image,tag,time,website
function getStoreInfo($storeName) {
    global $conn;
    $sql = "SELECT * FROM stores WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $storeName);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
// 搜尋店家關鍵字
function searchStores($location, $adjective, $keyword) {
    global $conn;
    $keyword = '%' . $keyword . '%';
    $sql = "SELECT DISTINCT s.name, r.real_rating, r.total_comments FROM stores AS s
            INNER JOIN keywords AS k ON s.id = k.store_name
            INNER JOIN rates AS r ON s.id = r.store_name
            INNER JOIN locations AS l ON s.id = l.store_name
            INNER JOIN tags AS t ON s.tag = t.tag
            WHERE l.city = ? and (s.name LIKE ? or s.description LIKE ? or t.category LIKE ? or s.tag LIKE ? or k.word LIKE ?)
            ORDER BY r.real_rating DESC, total_comments DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $location, $keyword, $keyword, $keyword, $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();

    $stores = [];
    while ($row = $result->fetch_assoc()) {
        $stores[] = $row;
    }
    return $stores;
}

//留言:有contents,rating,sort,store_name,time,userid
function getComments($storeName) {
    global $conn;
    $sql = "SELECT * FROM comments WHERE store_name = ? ORDER BY sort";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $storeName);
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    return $comments;
}

//地點:有city,details,dist,latitude,longitude,postal_code,store_name,vil
function getLocation($storeName) {
    global $conn;
    $sql = "SELECT * FROM locations WHERE store_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $storeName);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

//評價:有avg_ratings,real_rating,total_ratings,total_comments,store_responses,store_name
function getRating($storeName) {
    global $conn;
    $sql = "SELECT * FROM rates WHERE store_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $storeName);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
//服務:有store_name,dine_in,take_away,delivery
function getService($storeName) {
    global $conn;
    $sql = "SELECT * FROM services WHERE store_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $storeName);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

//關鍵字:有word,count,store_name
function getKeyword($storeName) {
    global $conn;
    $sql = "SELECT * FROM keywords WHERE store_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $storeName);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
// 函式來獲取所有店家的關鍵字
function getAllKeywords($storeName) {
    global $conn;
    // 使用預備語句來執行 SQL 查詢
    $sql = "SELECT store_name, word, count FROM keywords WHERE store_name = ? ORDER BY count DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $storeName);
    $stmt->execute();
    $result = $stmt->get_result();

    $keywords = [];
    while ($row = $result->fetch_assoc()) {
        if (count($keywords) < 5) {
            // 只加入前五個關鍵字
            $keywords[] = $row;
        }
    }
    $stmt->close();
    return $keywords;
}

//評論者:有id,level
function getUser($storeName) {
    global $conn;
    $sql = "SELECT * FROM users WHERE store_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $storeName);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>

