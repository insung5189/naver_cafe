<!-- header.php -->
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="/assets/css/reset.css">
    <!-- 폰트어썸 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
    <script src="/assets/js/member/address.js"></script>
    <title>PHP 게시판</title>
</head>
<body>
<nav class="col"> <!-- class 에 col 추가 -->
  <ul class="nav flex-row"> <!-- list 생성란 -->
    <li class="nav-item icon-link-hover">
      <a href="/article/articleController/articlelist" class="nav-link active">LIST</a>
    </li>
    <li class="nav-item icon-link-hover">
      <a href="/article/articleController/articleWrite" class="nav-link">WRITE</a>
    </li>
    <li class="nav-item icon-link-hover">
      <a href="/article/articleController" class="nav-link">HOME</a>
    </li>
    <li class="nav-item icon-link-hover">
      <a href="/article/articleController/dbconn" class="nav-link">DB연결확인</a>
    </li>
  </ul>
</nav>