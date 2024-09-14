<?php
$db_hostname = 'localhost';        //資料庫主機名稱
$db_username = 'root';             //登入資料庫的管理者的帳號
$db_password = '';                 //登入密碼
$db_name     = 'member';           //使用的資料庫
$db_charset  = 'utf8';             //設定字元編碼
 
//建立PDO的指定工作
$dsn = "mysql:host=$db_hostname;dbname=$db_name;charset=$db_charset";
 
try{
 //使用PDO連接到MySQL資料庫，建立PDO物件
 $conn = new PDO($dsn, $db_username, $db_password);
 
 //當錯誤發生時會將錯誤資訊放到一個類物件裡（PDOException）
 //PDO異常處理，PDO::ATTR_ERRMODE，有以下三種值的設定
 //PDO::ERRMODE_SILENT： 預設模式，不主動報錯，需要以$conn->errorInfo()的形式獲取錯誤資訊
 //PDO::ERRMODE_WARNING: 引發 E_WARNING 錯誤，主動報錯
 //PDO::ERRMODE_EXCEPTION: 主動抛出 exceptions 異常，需要以try{}cath(){}輸出錯誤資訊。
 //設定主動以警告的形式報錯
 $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
 //如果連接錯誤,將抛出一個PDOException異常對象
} 
catch ( PDOException $e ){
 //如果連結資料庫失敗則顯示錯誤訊並停止本頁的工作
 die("ERROR!!!: ". $e->getMessage());
} 
 
//$conn = null; //關閉資料庫的連線
?>

<?php
//啟動session功能
if( !isset($_SESSION) ){ session_start(); }
//設定時區
date_default_timezone_set('Asia/Taipei');
?>