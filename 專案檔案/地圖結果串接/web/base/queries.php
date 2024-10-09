<?php if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) { header('Location: /home'); exit;} ?>

<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';

  function getStoreInfo($storeName) {
      global $conn;
      $stmt = bindPrepare($conn,
      " SELECT * FROM stores 
        WHERE name = ? AND crawler_state IN ('成功', '完成', '超時')
      ", "s", $storeName);
      $stmt->execute();
      $result = $stmt->get_result();
      return $result->fetch_assoc();
  }

  function getStoreInfoById($storeId) {
      global $conn;
      $stmt = bindPrepare($conn,
      " SELECT * FROM stores 
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
      global $conn;
      $stmt = bindPrepare($conn,
      " SELECT * FROM keywords
        WHERE store_id = ? AND source = 'recommend'
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

  function getAllKeywords($storeId) {
      global $conn;
      $stmt = bindPrepare($conn,
      " SELECT word, count FROM keywords
        WHERE store_id = ? and source = 'google'
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

  function getMarks($storeId, $states) {
      global $conn;
      if (!is_array($states)) $states = [$states];
      $allStates = implode(',', array_map(function($state) use ($conn) {
          return "'" . $conn->real_escape_string($state) . "'";
      }, $states));
      $stmt = bindPrepare($conn,
      " SELECT object, COUNT(*) AS count FROM marks
        WHERE object !='' AND store_id = ? AND state IN ($allStates)
        GROUP BY object
        ORDER BY count DESC
        LIMIT 20
      ", "i", $storeId);
      $stmt->execute();
      $result = $stmt->get_result();
      $marks = [];
      while ($row = $result->fetch_assoc()) {
          $marks[] = $row;
      }
      $stmt->close();
      return $marks;
  }