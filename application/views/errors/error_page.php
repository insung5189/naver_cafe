<?php
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/errorPage.css']
];
?>

<section class="section-container">
    <div class="container">
        <h1 class="title">
            <i class="fa-solid fa-triangle-exclamation"></i> <?= $title ?>
        </h1>
        <p class="page-guide"><?= $message ?></p>
        <div class="btn-box flex-end">
            <a href="/member/logincontroller" class="btn btn-primary">로그인 페이지로 이동</a>
            <a href="/" class="btn btn-secondary">메인 페이지로 이동</a>
            <button onclick="window.history.back();" class="btn find-account-btn">뒤로 가기</button>
        </div>
    </div>
</section>