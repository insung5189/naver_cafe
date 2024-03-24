<?
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/member/modifyPasswordDone.css']
];
?>

<section class="section-container">
    <div class="container">
        <?php if ($this->session->userdata('passwordChanged')) : ?>
            <h1 class="title">
                <i class="fa-solid fa-check"></i>
                비밀번호 변경 완료
            </h1>
            <p class="page-guide">비밀번호 변경이 성공적으로 완료되었습니다! 새로운 비밀번호로 로그인해주세요.</p>
            <div class="btn-box flex-end">
                <a href="/member/logincontroller/sessionDestroyAndLogin" class="btn btn-primary">로그인 페이지로 이동</a>
                <a href="/" class="btn btn-secondary">메인 페이지로 이동</a>
            </div>
        <?php else : ?>
            <div class="error-message">
                <h1 class="title">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    잘못된 접근입니다.
                </h1>
                <p class="page-guide">비밀번호 변경을 위해서는 올바른 절차를 따라야 합니다.</p>
                <p class="page-guide">비밀번호 변경 과정을 정상적으로 마치셨다면 바뀐 비밀번호로 로그인이 가능합니다.</p>
                <div class="btn-box flex-end">
                    <a href="/member/logincontroller" class="btn btn-primary">로그인 페이지로 이동</a>
                    <a href="/" class="btn btn-secondary">메인 페이지로 이동</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>