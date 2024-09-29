<?php //抓取資料庫商家的資料定義函式
require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';

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

// 根據商家ID獲取商家資訊
function getStoreInfoById($storeId) {
    global $conn;
    $sql = "SELECT * FROM stores WHERE id = ? AND crawler_state IN ('成功', '完成', '超時')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $storeId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// 留言(依商家查詢) 所有有文字留言的商家
function getComments($storeId) {
    global $conn;
    $sql = "SELECT * FROM comments WHERE store_id = ? AND contents IS NOT NULL ";
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
    $sql = "SELECT * FROM comments WHERE store_id = ? AND contents IS NOT NULL AND sample_of_most_relevant = '1' ";
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
    $sql = "SELECT * FROM openhours WHERE store_id = $storeId";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    // 構建營業時間
    $openingHours = [];
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
  if (!isset($branchTitle)) return;
  global $conn;
  $sql = 
  "   SELECT s.*, r.avg_ratings, l.city, l.dist, l.vil, l.details 
      FROM stores AS s 
      LEFT JOIN rates AS r ON s.id = r.store_id
      LEFT JOIN locations AS l ON s.id = l.store_id
      WHERE s.crawler_state IN ('成功', '完成', '超時') 
      AND s.branch_title = '$branchTitle' 
      AND s.id != $storeId
  ";
  $stmt = $conn->prepare($sql);
  $stmt->execute();    
  $result = $stmt->get_result();
  $branches = [];
  while ($row = $result->fetch_assoc()) {
      $branches[] = $row;
  }
  $stmt->close();
  return $branches;
};

//標籤
function getMarks($storeId, $states) {
    global $conn;
    if (!is_array($states)) $states = [$states];
    $allStates = implode(',', array_map(function($state) use ($conn) {
        return "'" . $conn->real_escape_string($state) . "'";
    }, $states));
    $sql = 
    "   SELECT object, COUNT(*) AS count FROM marks
        WHERE object !='' AND store_id = $storeId AND state IN ($allStates)
        GROUP BY object
        ORDER BY count DESC
        LIMIT 20
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $marks = [];
    while ($row = $result->fetch_assoc()) {
        $marks[] = $row;
    }
    $stmt->close();
    return $marks;
}
?>

