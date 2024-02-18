<?
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/member/findEmailResult.css'],
];
?>
<section class="section-container">
    <div class="email-find-result-container">
        <? if ($emails = $this->session->flashdata('foundEmails')) : ?>
            <h1 class="title">
                <i class="fa-solid fa-check"></i>
                Email 찾기 결과
            </h1>
            <? $number = count($emails); ?>
            <div class="account-count">
                <span>총 </span><span class="count-num"><? echo $number; ?></span><span>개의 가입된 계정이 있습니다.</span>
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
                    <? foreach ($emails as $email) : ?>
                        <tr>
                            <td><? echo $number++; ?></td>
                            <td><? echo $email['email']; ?></td>
                            <td><? echo $email['createDate']; ?></td>
                        </tr>
                    <? endforeach; ?>
                </tbody>
            </table>
            <div class="btn-box">
                <a href="/member/logincontroller" class="btn btn-primary">로그인하기</a>
                <a href="/member/findaccountcontroller" class="btn btn-secondary">다른 계정 찾기</a>
            </div>
        <? elseif ($error = $this->session->flashdata('findEmailError')) : ?>
            <h1 class="title">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Email 찾기 결과
            </h1>
            <div class="form-box">
                <div class="error-message center"><? echo $this->session->flashdata('findEmailError'); ?></div>
            </div>
            <div class="btn-box">
                <a href="/member/findaccountcontroller" class="btn btn-primary">다시 시도하기</a>
            </div>
        <? else : ?>
            <h1 class="title">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Email 찾기 결과
            </h1>
            <div class="form-box">
                <div class="center">정보를 찾을 수 없습니다. 다시 시도해 주세요.</div>
            </div>
            <div class="btn-box">
                <a href="/member/findaccountcontroller" class="btn btn-primary">돌아가기</a>
            </div>
        <? endif; ?>
    </div>
</section>