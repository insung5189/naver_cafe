<?


$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/member/login.css'],
    'js' => ['/assets/js/member/login.js']
];
?>
<section class="section-container">
    <div class="container">
        <h1 class="title">
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
            사용자 로그인
        </h1>
        <div class="title">
            <p class="page-guide">반갑습니다! 비드코칭연구소 제품완성팀 미션 카페입니다.</p>
            <p class="page-guide">이메일로 로그인 해주세요.</p>
        </div>
        <?php if (!empty($errors)) : ?>
            <div class="error-messages">
                <p class="error-alert"><strong>⚠️ 문제가 발생했습니다!</strong></p>
                <?php foreach ($errors as $field => $error) : ?>
                    <p class="error-alert"><strong><?= htmlspecialchars($field) ?>:</strong> <?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/member/LoginController/processLogin" class="login-form">
            <div class="form-box">
                <div class="field-box">
                    <div class="label-box">
                        <label class="label-section" for="userName">
                            <span class="label-text">아이디</span>
                            <span class="description">(Email)</span>
                        </label>
                    </div>

                    <div class="input-box">
                        <input autofocus class="custom-input" maxlength="50" name="userName" id="userName" placeholder="사용자 아이디 (이메일)" type="text" autocomplete="off">
                        <span class="description pl-5" id="email-validation-message"></span>
                    </div>
                </div>

                <hr>

                <div class="field-box">
                    <div class="label-box">
                        <label class="label-section" for="password">
                            <span class="label-text">비밀번호</span>
                        </label>
                    </div>

                    <div class="input-box">
                        <input autofocus class="custom-input" maxlength="50" name="password" id="password" placeholder="비밀번호를 입력해주세요" type="password" autocomplete="off">
                    </div>
                </div>
                <hr>
            </div>

            <div class="btn-center">
                <div class="action-btn-box">
                    <div class="center btn-box">
                        <input class="form-btn-box submit-btn" type="submit" value="로그인">
                        <a href="/member/signupcontroller">
                            <input class="form-btn-box btn signup-btn" type="button" value="회원가입">
                        </a>
                    </div>
                    <div class="center btn-box">
                        <a href="/member/findAccountController">
                            <input class="form-btn-box btn find-account-btn" type="button" value="Email / 비밀번호 찾기">
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- <div class="login-logo flex mt-[20px] justify-between">
    <div class="w-1/3">
        <div class="logo" id="login-kakao">
            <a href="/oauth2/authorization/kakao">
                <img class="w-[80px] h-[80px]" src="@{/image/login_kakao.png}" alt="">
            </a>
        </div>
    </div>
    <div class="w-1/3 mx-[60px]">
        <div class="logo" id="login-naver">
            <a href="/oauth2/authorization/naver">
                <img class="w-[80px] h-[80px]" src="@{/image/login_naver.png}" alt="">
            </a>
        </div>
    </div>
    <div class="w-1/3" id="login_google">
        <a class="flex justify-center" href="/oauth2/authorization/google">
            <img class="w-[80px] h-[80px]" src="@{/image/login_google.png}" alt="">
        </a>
    </div>
</div> -->

</section>