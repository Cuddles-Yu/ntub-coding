<?php
require_once('./shared/conn_pdo.php');
include_once('./shared/assist.php');
 
try {
  $sql_str = "SELECT * FROM member WHERE BINARY mem_mail=:mem_mail AND mem_pwd=:mem_pwd";

  $RS = $conn -> prepare($sql_str);
 
  $mem_mail = $_POST['mem_mail'];  //接收登入的帳號
  $mem_pwd  = $_POST['mem_pwd'];   //接收登入的密碼  
 
  $RS -> bindParam(':mem_mail', $mem_mail);
  $RS -> bindParam(':mem_pwd', $mem_pwd);
 
  $RS -> execute();
  $total = $RS -> rowCount();
 
  //$total是資料集的筆數, 如果>=1表示有查詢到資料，是符合登入的會員
  if( $total >= 1 ){
    $row_RS = $RS -> fetch(PDO::FETCH_ASSOC);
    $_SESSION['mem_id']    = $row_RS['mem_id'];     //將會員ID記錄到SESSION系統變數
    $_SESSION['mem_name']  = $row_RS['mem_name'];   //將會員名稱記錄到SESSION系統變數
 
    $url = './home.php';  //登入成功要前往的位址
 
  }else{
    //登入失敗..............登入失敗要前往的位址，並加上msg參數
    $url = './login.php?msg=1';
  }
 
  header('Location:'.$url);  
} 
catch ( PDOException $e ){
  die("ERROR!!!: ". $e->getMessage());
}
?>