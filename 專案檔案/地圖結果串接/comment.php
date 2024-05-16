<?php
  include 'DB.php';
  $sql = "SELECT * FROM `comments` WHERE `store_name` = '85度C 中和圓通店' AND `sort` = 1"; //特定店家的第幾筆留言

  //送出查詢的SQL指令
  if ($result = mysqli_query($link, $sql)) {
     while ($row = mysqli_fetch_assoc($result)) {
         $content = $row["contents"];
         $limited_content =(mb_strlen($content)>140)? mb_substr($content,0,140).'...':$content;
         echo $limited_content . "<br/>";
     }
     mysqli_free_result($result); //釋放占用記憶體
  } else {
      echo "查詢失敗: " . mysqli_error($link);
  }
  mysqli_close($link); //關閉資料庫連接
?>