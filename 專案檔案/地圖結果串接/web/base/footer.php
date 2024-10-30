<?php if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) { header('Location: /home'); exit;} ?>

<link rel="stylesheet" href="/styles/footer.css?v=<?=$VERSION?>" />
<footer>
  <div class="bottom">
    <!--(比賽專用)-->
    <!-- 台北商業大學 | 資訊管理系<br> -->
    <!-- 北商資管專題 113206 小組<br> -->
    <?php if(isset($specialRestaurant)): ?>
      <en>
        餐廳名冊資料來源自
        <a href="https://data.taipei" target="_blank" draggable="false" style="color:white;">臺北市資料大平臺</a>
        ，資料集分別為
        <a href="https://data.taipei/dataset/detail?id=845818d9-c432-44b4-85dd-03d71bd867b2" target="_blank" draggable="false" style="color:white;">臺北市環保餐廳</a>、
        <a href="https://data.taipei/dataset/detail?id=d01eb1c0-2097-42d0-ad16-7a164707c28f" target="_blank" draggable="false" style="color:white;">臺北市客家美食餐飲小吃名冊</a>
        。
      </en>
    <?php endif; ?>
    <?php if(isset($mrtStations)): ?>
      <en>
        快速定位資料來源自
        <a href="https://data.taipei" target="_blank" draggable="false" style="color:white;">臺北市資料大平臺</a>
        ，資料集為
        <a href="https://data.taipei/dataset/detail?id=cfa4778c-62c1-497b-b704-756231de348b" target="_blank" draggable="false" style="color:white;">臺北捷運車站出入口座標</a>
        。
      </en>
    <?php endif; ?>
    <br><br>
    <en style="float:right;">Copyright ©2024 All rights reserved. Uicons by <a href="https://www.flaticon.com/uicons" draggable="false" target="_blank" style="color:white;">Flaticon</a></en>
  </div>
</footer>