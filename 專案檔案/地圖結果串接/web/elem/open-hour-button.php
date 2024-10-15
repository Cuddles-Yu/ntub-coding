<?php
  function getOpeningHours($storeId) {
    global $conn;
    $stmt = bindPrepare($conn,
    " SELECT * FROM openhours
      WHERE store_id = ?
    ", "i", $storeId);
    $stmt->execute();
    $result = $stmt->get_result();
    //構建營業時間
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

  function getOpeningStatus($storeId) {
    global $conn;
    date_default_timezone_set('Asia/Taipei');
    $daysOfWeek = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
    $today = $daysOfWeek[date('w')];
    $currentTime = date('H:i:s');
    $oneHourLater = date('H:i:s', strtotime('+1 hour'));
    $oneHourBeforeClose = date('H:i:s', strtotime('+1 hour', strtotime($currentTime)));
    $stmt = bindPrepare($conn,"
      SELECT open_time, close_time FROM openhours 
      WHERE store_id = ? AND day_of_week = ?"
    , "is", $storeId, $today
    );
    $stmt->execute();
    $result = $stmt->get_result();
    $status = [
      'text' => '已打烊',
      'class' => 'orange',
    ];
    while ($row = $result->fetch_assoc()) {
      if (!empty($row['open_time']) && !empty($row['close_time'])) {
        if ($currentTime >= $row['open_time'] && $currentTime <= $row['close_time']) {
          if ($oneHourBeforeClose >= $row['close_time'] && $currentTime < $row['close_time']) {
            $status['text'] = '即將打烊';
            $status['class'] = 'dark-orange';
          } else {
            $status['text'] = '營業中';
            $status['class'] = 'green';
          }
          break;
        }
        if ($currentTime < $row['open_time'] && $oneHourLater >= $row['open_time']) {
          $status['text'] = '即將營業';
          $status['class'] = 'dark-green';
          break;
        }
      }
    }
    $stmt->close();
    return $status;
  }

  $openingStatus = getOpeningStatus($STORE_ID);
  $openingHours = getOpeningHours($STORE_ID);
  $daysOfWeek = ['星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日'];
  $todayIndex = date('N') - 1;
  $reorderedDays = array_merge(array_slice($daysOfWeek, $todayIndex), array_slice($daysOfWeek, 0, $todayIndex));
  $lastDay = end($reorderedDays);
  $statusText = $openingStatus['text'];
  $statusClass = $openingStatus['class'];
?>

<button type="button" class="btn btn-solid-<?=$statusClass?> status" data-bs-container="body" data-bs-toggle="popover"
  data-bs-title="營業時間" data-bs-placement="top" data-bs-html="true"
  data-bs-content="
  <?php
    foreach ($reorderedDays as $dayIndex => $day) {
      if ($dayIndex === 0) {
        echo "<strong style='color: #FF4500;'>".htmlspecialchars($day).' (今天)</strong><br>';
      } else {
        echo htmlspecialchars($day) . '<br>';
      }
      if (empty($openingHours[$day])) {
        echo '休息<br>';
      } else {
        foreach ($openingHours[$day] as $hour) {
          // 檢查是否為 24 小時營業
          if ($hour['open_time'] === '00:00:00' && $hour['close_time'] === '24:00:00') {
            echo '24小時營業<br>';
          } elseif ($hour['open_time'] === null || $hour['close_time'] === null) {
            echo '休息<br>';
          } else {
            $openTime = date('H:i', strtotime($hour['open_time']));
            $closeTime = ($hour['close_time'] === '24:00:00') ? '24:00' : date('H:i', strtotime($hour['close_time']));
            echo htmlspecialchars($openTime) . ' - ' . htmlspecialchars($closeTime) . '<br>';
          }
        }
      }
      if ($day !== $lastDay) echo "<div class='open-hour-separator'></div>";
  }?>">
  <i class="fi fi-sr-clock status-img"></i><?=$statusText?><i class="fi fi-sr-angle-small-right text-icon button-down-arrow"></i>
</button>