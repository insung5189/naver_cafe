<?php
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/errorPage.css']
];
?>

<section class="section-container">
    <div class="container">
        <h1 class="title">
            <i class="fa-solid fa-triangle-exclamation"></i> 잘못된 접근입니다.
        </h1>
        <p class="page-guide">죄송합니다. 이 페이지에 접근할 권한이 없습니다. 로그인이 필요할 수 있습니다.</p>
        <div class="btn-box flex-end">
            <a href="/member/logincontroller" class="btn btn-primary">로그인 페이지로 이동</a>
            <a href="/" class="btn btn-secondary">메인 페이지로 이동</a>
            <button onclick="window.history.back();" class="btn find-account-btn">뒤로 가기</button>
        </div>
    </div>
</section>