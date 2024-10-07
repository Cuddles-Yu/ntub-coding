<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Redirecting</title>
  <script>
    localStorage.setItem('tryToLogin', 'true');
    var LOADING_DURATION = 500; // 2秒（可根據需要調整）
    setTimeout(function() {
      window.location.replace('/home');
    }, LOADING_DURATION);
  </script>
</head>
<body>
</body>
</html>
