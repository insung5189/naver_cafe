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
    'css' => ['/assets/css/member/modifyPassword.css'],
    'js' => ['/assets/js/member/modifyPassword.js']
];
?>
<section class="section-container">
    <div class="container">
        <? if ($email = $this->session->userdata('resetMemberEmail')) : ?>
            <? $createDate = $this->session->userdata('resetMemberCreateDate'); ?>
            <h1 class="title">
                <i class="fa-solid fa-arrows-rotate"></i>
                비밀번호 변경
            </h1>
            <div class="title">
                <p class="page-guide">비밀번호를 변경합니다. 영문, 숫자, 특수문자 포함 8자리 이상으로 구성해주세요.</p>
            </div>

            <? if (!empty($errors)) : ?>
                <div class="error-messages">
                    <p class="error-alert"><strong>⚠️ 문제가 발생했습니다!</strong></p>
                    <? foreach ($errors as $field => $error) : ?>
                        <p class="error-alert"><strong><?= htmlspecialchars($field) ?>:</strong> <?= htmlspecialchars($error) ?></p>
                    <? endforeach; ?>
                </div>
            <? endif; ?>


            <? if (!empty($this->session->flashdata('error'))) : ?>
                <div class="error-message center"><? echo $this->session->flashdata('error'); ?></div>
            <? endif; ?>
            <div class="account-count">
                <span>귀하의 가입된 계정 내역입니다.</span>
            </div>
            <table class="email-result-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>이메일</th>
                        <th>가입일</th>
                    </tr>
                </thead>
                <tbody>
                    <? $number = 1; ?>
                    <tr>
                        <td><? echo htmlspecialchars($number++); ?></td>
                        <td><? echo htmlspecialchars($email); ?></td>
                        <td><? echo htmlspecialchars($createDate); ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="inline">
                <!-- FindEmailForm -->
                <form method="POST" action="/member/MypageController/processModifyPassword" id="modifyPassword" class="modify-password-form">
                    <div class="form-box">
                        <div class="field-box">
                            <div class="label-box">
                                <label class="label-section" for="oldpassword">
                                    <span class="label-text">기존 비밀번호</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input autofocus class="custom-input" maxlength="50" name="oldpassword" id="oldpassword" placeholder="기존 비밀번호를 입력해주세요." type="password" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="caption-box">
                            <div class="label-box">
                                <div class="label-section">
                                </div>
                            </div>

                            <div class="input-box position">
                                <span class="description" id="oldpassword-validation-message"></span>
                            </div>

                        </div>

                        <hr>

                        <div class="field-box">
                            <div class="label-box">
                                <label class="label-section" for="newpassword">
                                    <span class="label-text">신규 비밀번호</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input autofocus class="custom-input" maxlength="50" name="newpassword" id="newpassword" placeholder="신규 비밀번호를 입력해주세요." type="password" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="caption-box">
                            <div class="label-box">
                                <div class="label-section">
                                </div>
                            </div>

                            <div class="input-box position">
                                <span class="description" id="newpassword-validation-message"></span>
                            </div>

                        </div>

                        <hr>

                        <div class="field-box">
                            <div class="label-box">
                                <label class="label-section" for="newpasswordcf">
                                    <span class="label-text">신규 비밀번호 확인</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input autofocus class="custom-input" maxlength="50" name="newpasswordcf" id="newpasswordcf" placeholder="신규 비밀번호를 한 번 더 입력해주세요." type="password" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="caption-box">
                            <div class="label-box">
                                <div class="label-section">
                                </div>
                            </div>

                            <div class="input-box position">
                                <span class="description" id="newpasswordcf-validation-message"></span>
                            </div>

                        </div>
                        <hr>
                    </div>

                    <div class="action-btn-box">
                        <div class="center btn-box">
                            <input class="form-btn-box btn find-account-btn" type="submit" value="비밀번호 변경">
                        </div>
                    </div>
                </form>
            </div>
        <?php else : ?>
            <div class="error-message">
                <h1 class="title">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    잘못된 접근입니다.
                </h1>
                <p class="page-guide">비밀번호 변경을 위해서는 올바른 절차를 따라야 합니다.</p>
                <div class="btn-box flex-end">
                    <a href="/member/logincontroller" class="btn btn-primary">로그인 페이지로 이동</a>
                    <a href="/" class="btn btn-secondary">메인 페이지로 이동</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>