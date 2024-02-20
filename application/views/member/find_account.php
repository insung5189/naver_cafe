<?
if (isset($_SESSION['user_data'])) {
    echo "<script>
    if (confirm('이미 로그인되어 있습니다. 확인을 누르시면 로그아웃 됩니다.')) {
        window.location.href = '/member/findaccountcontroller/processLogoutAndRedirectFindAccount';
    } else {
        window.location.href = history.back();
    }
    </script>";
    exit;
}

$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/member/findAccount.css'],
    'js' => ['/assets/js/member/findAccount.js']
];
?>
<section class="section-container">
    <div class="container">
        <h1 class="title">
            <i class="fa-solid fa-magnifying-glass"></i>
            Email / 비밀번호 찾기
        </h1>
        <div class="title">
            <p class="page-guide">가입하신 회원정보를 입력하면 Email / 비밀번호 조회 및 변경이 가능합니다.</p>
        </div>

        <? if (!empty($errors)) : ?>
            <div class="error-messages">
                <p class="error-alert"><strong>⚠️ 문제가 발생했습니다!</strong></p>
                <? foreach ($errors as $field => $error) : ?>
                    <p class="error-alert"><strong><?= htmlspecialchars($field) ?>:</strong> <?= htmlspecialchars($error) ?></p>
                <? endforeach; ?>
            </div>
        <? endif; ?>

        <div class="inline">
            <!-- FindEmailForm -->
            <form method="POST" action="/member/FindAccountController/processFindEmail" id="findEmail" class="find-account-form">
                <div class="form-box">
                    <div class="field-box">
                        <div class="label-box">
                            <label class="label-section" for="firstNameM">
                                <span class="label-text">이름</span>
                                <span class="description">(First name)</span>
                            </label>
                        </div>

                        <div class="input-box">
                            <input autofocus class="custom-input" maxlength="50" name="firstName" id="firstNameM" placeholder="ex) 길동" type="text" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="caption-box">
                        <div class="label-box">
                            <div class="label-section">
                            </div>
                        </div>

                        <div class="input-box position-absolute">
                            <span class="description" id="m-firstname-validation-message"></span>
                        </div>

                    </div>

                    <hr>

                    <div class="field-box">
                        <div class="label-box">
                            <label class="label-section" for="lastNameM">
                                <span class="label-text">성</span>
                                <span class="description">(Last name)</span>
                            </label>
                        </div>

                        <div class="input-box">
                            <input autofocus class="custom-input" maxlength="50" name="lastName" id="lastNameM" placeholder="ex) 홍" type="text" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="caption-box">
                        <div class="label-box">
                            <div class="label-section">
                            </div>
                        </div>

                        <div class="input-box position-absolute">
                            <span class="description" id="m-lastname-validation-message"></span>
                        </div>

                    </div>

                    <hr>

                    <div class="field-box">
                        <div class="label-box">
                            <label class="label-section" for="phoneM">
                                <span class="label-text">연락처</span>
                                <span class="description">(Phone)</span>
                            </label>
                        </div>

                        <div class="input-box">
                            <input autofocus class="custom-input" maxlength="50" name="phone" id="phoneM" placeholder="ex) 01012345678" type="text" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="caption-box">
                        <div class="label-box">
                            <div class="label-section">
                            </div>
                        </div>

                        <div class="input-box position-absolute">
                            <span class="description" id="m-phone-validation-message"></span>
                        </div>

                    </div>

                    <hr>
                </div>

                <div class="action-btn-box">
                    <div class="center btn-box">
                        <input class="form-btn-box btn find-account-btn" type="submit" value="Email 찾기">
                    </div>
                </div>
            </form>

            <!-- FindPasswordForm -->
            <form method="POST" action="/member/FindAccountController/processFindPassword" id="findPW" class="find-account-form">
                <div class="form-box">
                    <div class="field-box">
                        <div class="label-box">
                            <label class="label-section" for="userNameP">
                                <span class="label-text">아이디</span>
                                <span class="description">(Email)</span>
                            </label>
                        </div>

                        <div class="input-box">
                            <input autofocus class="custom-input" maxlength="50" name="userName" id="userNameP" placeholder="ex) example@email.com" type="email" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="caption-box">
                        <div class="label-box">
                            <div class="label-section">
                            </div>
                        </div>

                        <div class="input-box">
                            <span class="description pl-5 caption" id="p-email-validation-message"></span>
                        </div>

                    </div>

                    <hr>
                    <div class="field-box">
                        <div class="label-box">
                            <label class="label-section" for="firstNameP">
                                <span class="label-text">이름</span>
                                <span class="description">(First name)</span>
                            </label>
                        </div>

                        <div class="input-box">
                            <input autofocus class="custom-input" maxlength="50" name="firstName" id="firstNameP" placeholder="ex) 길동" type="text" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="caption-box">
                        <div class="label-box">
                            <div class="label-section">
                            </div>
                        </div>

                        <div class="input-box">
                            <span class="description pl-5 caption" id="p-firstname-validation-message"></span>
                        </div>

                    </div>

                    <hr>

                    <div class="field-box">
                        <div class="label-box">
                            <label class="label-section" for="lastNameP">
                                <span class="label-text">성</span>
                                <span class="description">(Last name)</span>
                            </label>
                        </div>

                        <div class="input-box">
                            <input autofocus class="custom-input" maxlength="50" name="lastName" id="lastNameP" placeholder="ex) 홍" type="text" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="caption-box">
                        <div class="label-box">
                            <div class="label-section">
                            </div>
                        </div>

                        <div class="input-box">
                            <span class="description pl-5 caption" id="p-lastname-validation-message"></span>
                        </div>

                    </div>

                    <hr>

                    <div class="field-box">
                        <div class="label-box">
                            <label class="label-section" for="phoneP">
                                <span class="label-text">연락처</span>
                                <span class="description">(Phone)</span>
                            </label>
                        </div>

                        <div class="input-box">
                            <input autofocus class="custom-input" maxlength="50" name="phone" id="phoneP" placeholder="ex) 01012345678" type="text" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="caption-box">
                        <div class="label-box">
                            <div class="label-section">
                            </div>
                        </div>

                        <div class="input-box">
                            <span class="description pl-5 caption" id="p-phone-validation-message"></span>
                        </div>

                    </div>

                    <hr>
                </div>

                <div class="action-btn-box">
                    <div class="center btn-box">
                        <input class="form-btn-box btn find-account-btn" type="submit" value="비밀번호 찾기">
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>