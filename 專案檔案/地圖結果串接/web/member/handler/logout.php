<?php 
  session_start();
  session_unset();
  session_destroy();
  setcookie('remember', '0', time() - 3600, "/", "", false, true);
  
  header('Content-Type: application/json');
  echo json_encode(['success' => true]);
?>