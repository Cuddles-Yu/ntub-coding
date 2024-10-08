<?php 
  header("Access-Control-Allow-Origin: https://commentspace.ascdc.tw");
  header("Access-Control-Allow-Credentials: true");
  header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type");
  session_start();
  session_unset();
  session_destroy();
  setcookie('remember', '0', time() - 3600, "/", "commentspace.ascdc.tw", false, true);
  // header('Set-Cookie: remember=0; Path=/; SameSite=None; Secure; HttpOnly');
  
  header('Content-Type: application/json');
  echo json_encode(['success' => true]);
?>