<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <title><?=$pageTitle?> - 評星宇宙</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
  <link rel="stylesheet" href="/styles/404.css?v=<?=$VERSION?>">
</head>

<body>
  <div class="container">
    <div class="icon">
      <img src="<?=$icon??'/images/website-error.png';?>" alt="圖示">
    </div>
    <div class="text">
      <h1><?=$errorMessage?></h1>
      <p><?=$errorDescription?></p>
      <p><?=$suggestion?></p>
      <a draggable="false" href="<?=$link?>"><?=$linkLabel?></a>
    </div>
  </div>

</body>
</html>