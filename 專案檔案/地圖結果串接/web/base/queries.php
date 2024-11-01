<?php if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) { header('Location: /home'); exit;} ?>

<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/analysis.php';

  function getStoreInfo($storeName) {
      global $conn;
      $stmt = bindPrepare($conn, "
        SELECT * FROM stores
        LEFT JOIN rates ON stores.id = rates.store_id
        WHERE name = ? AND crawler_state IN ('成功', '完成', '超時')
      ", "s", $storeName);
      $stmt->execute();
      $result = $stmt->get_result();
      return $result->fetch_assoc();
  }

  function getLandmarkCategories() {
    global $conn;
    $stmt = $conn->prepare("
      SELECT DISTINCT category FROM landmarks
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $categories = [];
    while ($row = $result->fetch_assoc()) {
      $categories[] = $row['category'];
    }
    $stmt->close();
    return $categories;
  }

  function getCities() {
    global $conn;
    $stmt = $conn->prepare("
      SELECT DISTINCT city FROM locations
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $cities = [];
    while ($row = $result->fetch_assoc()) {
      $cities[] = $row['city'];
    }
    $stmt->close();
    return $cities;
  }
  function getDists($city) {
    if (!$city) return [];
    global $conn;
    $stmt = bindPrepare($conn, "
      SELECT DISTINCT dist FROM locations
      WHERE city = ?
    ", 's', $city);
    $stmt->execute();
    $result = $stmt->get_result();
    $dists = [];
    while ($row = $result->fetch_assoc()) {
      $dists[] = $row['dist'];
    }
    $stmt->close();
    return $dists;
  }


  function getStoreInfoById($storeId) {
      global $conn;
      $stmt = bindPrepare($conn, "
        SELECT * FROM stores
        LEFT JOIN rates ON stores.id = rates.store_id
        WHERE id = ? AND crawler_state IN ('成功', '完成', '超時')
      ", "i", $storeId);
      $stmt->execute();
      $result = $stmt->get_result();
      return $result->fetch_assoc();
  }

  function getComments($storeId) {
      global $conn;
      $stmt = bindPrepare($conn,
      " SELECT * FROM comments WHERE store_id = ? AND contents IS NOT NULL
      ", "i", $storeId);
      $stmt->execute();
      $result = $stmt->get_result();
      $comments = [];
      while ($row = $result->fetch_assoc()) {
          $comments[] = $row;
      }
      return $comments;
  }

  function getRelevantComments($storeId) {
      global $conn;
      $stmt = bindPrepare($conn,
      " SELECT * FROM comments 
        WHERE store_id = ? AND contents IS NOT NULL AND sample_of_most_relevant = '1' 
      ", "i", $storeId);
      $stmt->execute();
      $result = $stmt->get_result();
      $comments = [];
      while ($row = $result->fetch_assoc()) {
          $comments[] = $row;
      }
      return $comments;
  }

  function getHighestComments($storeId) {
      global $conn;
      $stmt = bindPrepare($conn,
      " SELECT * FROM comments 
        WHERE store_id = ? AND contents IS NOT NULL AND sample_of_highest_rating = '1'
        ORDER BY rating DESC, id ASC
      ", "i", $storeId);
      $stmt->execute();
      $result = $stmt->get_result();
      $comments = [];
      while ($row = $result->fetch_assoc()) {
          $comments[] = $row;
      }
      return $comments;
  }

  function getLowestComments($storeId) {
      global $conn;
      $stmt = bindPrepare($conn, 
      " SELECT * FROM comments
        WHERE store_id = ? AND contents IS NOT NULL AND sample_of_lowest_rating = '1'
        ORDER BY rating DESC, id ASC
      ", "i", $storeId);
      $stmt->execute();
      $result = $stmt->get_result();
      $comments = [];
      while ($row = $result->fetch_assoc()) {
          $comments[] = $row;
      }
      return $comments;
  }

  function getLocation($storeId) {
      global $conn;
      $stmt = bindPrepare($conn, 
      " SELECT * FROM locations WHERE store_id = ?
      ", "i", $storeId);
      $stmt->execute();
      $result = $stmt->get_result();
      return $result->fetch_assoc();
  }

  function getRating($storeId) {
      global $conn;
      $stmt = bindPrepare($conn,
      " SELECT * FROM rates WHERE store_id = ?
      ", "i", $storeId);
      $stmt->execute();
      $result = $stmt->get_result();
      return $result->fetch_assoc();
  }

  function getService($storeId) {
      global $conn;
      $stmt = bindPrepare($conn,
      " SELECT * FROM services WHERE store_id = ?
      ", "i", $storeId);
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

  function getFoodKeyword($storeId) {
    global $conn, $MIN_KEYWORD_COUNT;
    $stmt = bindPrepare($conn,
    " SELECT * FROM keywords
      WHERE store_id = ? AND source = 'recommend' AND count >= $MIN_KEYWORD_COUNT
      ORDER BY count DESC
    ", "i", $storeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $keywords = [];
    while ($row = $result->fetch_assoc()) {
      $keywords[] = $row;
    }
    $stmt->close();
    return $keywords;
  }

  function getKeywords($storeId, $limit=0) {
    global $conn;
    $sql = "
      SELECT word, count FROM keywords
      WHERE store_id = ? and source = 'comment'
      ORDER BY count DESC".
      ($limit?" LIMIT $limit" : "")
    ;
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

  function getOtherBranches($branchTitle, $storeId) {
    if (!isset($branchTitle)) return;
    global $conn;
    $stmt = bindPrepare($conn,
    " SELECT s.*, r.avg_ratings, l.city, l.dist, l.vil, l.details
      FROM stores AS s
      LEFT JOIN rates AS r ON s.id = r.store_id
      LEFT JOIN locations AS l ON s.id = l.store_id
      WHERE s.crawler_state IN ('成功', '完成', '超時') AND s.branch_title = ? AND s.id != ?
    ", "sd", $branchTitle, $storeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $branches = [];
    while ($row = $result->fetch_assoc()) {
        $branches[] = $row;
    }
    $stmt->close();
    return $branches;
  };

  function getMarks($storeId, $states, $target, $limit) {
    global $conn;
    if (!is_array($states)) $states = [$states];
    $allStates = implode(',', array_map(function($state) use ($conn) {
        return "'" . $conn->real_escape_string($state) . "'";
    }, $states));
    if ($target) {
      $target = targetTransform($target);
      $stmt = bindPrepare($conn, "
        SELECT object, COUNT(*) AS count FROM marks AS m
        INNER JOIN comments AS c ON m.store_id = c.store_id AND m.comment_id = c.id
        WHERE object !='' AND m.store_id = ? AND target = ? AND state IN ($allStates)
        GROUP BY object".
        (!$limit ? " HAVING count > 1" : "")."
        ORDER BY count DESC, object
      ", "is", $storeId, $target);
    } else {
      $stmt = bindPrepare($conn, "
        SELECT object, COUNT(*) AS count FROM marks AS m
        INNER JOIN comments AS c ON m.store_id = c.store_id AND m.comment_id = c.id
        WHERE object !='' AND m.store_id = ? AND state IN ($allStates)
        GROUP BY object".
        (!$limit ? " HAVING count > 1" : "")."
        ORDER BY count DESC, object
      ", "i", $storeId);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $marks = [];
    $count = 0;
    $endResult = false;
    $showMore = false;
    while ($row = $result->fetch_assoc()) {
      if ($row['count'] <= 1) {
        $endResult = true;
        if (!$limit) break;
      }
      if ($limit && $count >= 20) {
        $showMore = $row['count'] > 1;
        break;
      }
      $marks[] = $row;
      $count++;
    }
    if ($count < 20) $endResult = true;
    $stmt->close();
    return ['marks' => $marks, 'endResult' => $endResult, 'showMore' => $showMore];
}