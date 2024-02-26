$(document).ready(function () {
    // AJAX 요청을 통해 현재 로그인 상태 확인
    $.get("/member/logincontroller/checkSession", function (response) {
        if (response.isLoggedIn) {
            var loginAnotherAccount = confirm("이미 로그인되어 있습니다. 다른 계정으로 로그인하시겠습니까?");
            if (loginAnotherAccount) {
                window.location.href = history.back();
            } else {

            }
        }
    });

    $('#userName').on('keyup', validateEmail);

    // 이메일 유효성 검사 keyup
    function validateEmail() {
        const emailInput = $('#userName');
        const email = emailInput.val();
        const emailValidationMessage = $('#email-validation-message');
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

        const leadingOrTrailingWhitespacePattern = /^\s+|\s+$/;

        if (leadingOrTrailingWhitespacePattern.test(email)) {
            emailValidationMessage.text('❌ 이메일 주소의 시작이나 끝에 공백을 포함할 수 없습니다.');
            emailValidationMessage.css('color', 'red');
            return false;
        } else if (!emailPattern.test(email)) {
            emailValidationMessage.text('❌ 올바른 이메일 형식이 아닙니다.');
            emailValidationMessage.css('color', 'red');
            return false;
        } else {
            emailValidationMessage.text('✔️ 올바른 이메일 형식입니다.');
            emailValidationMessage.css('color', 'green');
            return true;
        }
    }
});