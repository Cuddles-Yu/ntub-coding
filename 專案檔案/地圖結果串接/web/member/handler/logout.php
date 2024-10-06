<?php 
  session_start();
  session_unset();
  session_destroy();
  setcookie('remember', '', time() - 3600, "/");
  
  header('Content-Type: application/json');
  echo json_encode(['success' => true]);