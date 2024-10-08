<?php  
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/db.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/session.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/base/function.php';
  global $conn, $MEMBER_ID;

  header('Content-Type: application/json');
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (is_null($MEMBER_ID)) {
      echo json_encode(['success' => false, 'message' => '尚未登入帳號']);
      exit();
    }
    $searchRadius = intval($_POST['searchRadius']);
    $openNow = $_POST['openNow'];
    $parking = $_POST['parking'];
    $wheelchairAccessible = $_POST['wheelchairAccessible'];
    $vegetarian = $_POST['vegetarian'];
    $healthy = $_POST['healthy'];
    $kidsFriendly = $_POST['kidsFriendly'];
    $petsFriendly = $_POST['petsFriendly'];
    $genderFriendly = $_POST['genderFriendly'];
    $dilivery = $_POST['dilivery'];
    $takeaway = $_POST['takeaway'];
    $dineIn = $_POST['dineIn'];
    $breakfast = $_POST['breakfast'];
    $brunch = $_POST['brunch'];
    $lunch = $_POST['lunch'];
    $dinner = $_POST['dinner'];
    $reservation = $_POST['reservation'];
    $groupFriendly = $_POST['groupFriendly'];
    $familyFriendly = $_POST['familyFriendly'];
    $toilet = $_POST['toilet'];
    $wifi = $_POST['wifi'];
    $cash = $_POST['cash'];
    $creditCard = $_POST['creditCard'];
    $debitCard = $_POST['debitCard'];
    $mobilePayment = $_POST['mobilePayment'];

    ### 更新偏好設定 ###
    $stmt = bindPrepare($conn, "
      UPDATE preferences
      SET search_radius=?, open_now=?, parking=?, wheelchair_accessible=?, vegetarian=?, healthy=?, 
          kids_friendly=?, pets_friendly=?, gender_friendly=?, delivery=?, takeaway=?, dine_in=?, 
          breakfast=?, brunch=?, lunch=?, dinner=?, reservation=?, group_friendly=?, family_friendly=?, 
          toilet=?, wifi=?, cash=?, credit_card=?, debit_card=?, mobile_payment=?
      WHERE member_id = ?
    ", "issssssssssssssssssssssssi",
      $searchRadius, $openNow, $parking, $wheelchairAccessible, $vegetarian, $healthy, 
      $kidsFriendly, $petsFriendly, $genderFriendly, $dilivery, $takeaway, $dineIn, 
      $breakfast, $brunch, $lunch, $dinner, $reservation, $groupFriendly, $familyFriendly, 
      $toilet, $wifi, $cash, $creditCard, $debitCard, $mobilePayment, $MEMBER_ID
    );
    if ($stmt->execute()) {
      echo json_encode(['success' => true, 'message' => '會員偏好修改成功']);
    } else {
      echo json_encode(['success' => false, 'message' => $conn->error]);
    }    
    $stmt->close();
  }