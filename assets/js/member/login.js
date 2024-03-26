$(document).ready(function () {
    // AJAX 요청을 통해 현재 로그인 상태 확인
    function getCookie(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length == 2) return parts.pop().split(";").shift();
        else return null;
    }
    // AJAX 요청을 통해 현재 로그인 상태 확인
    $.get("/member/logincontroller/checkSession", function (response) {
        if (response.isLoggedIn) {
            var loginAnotherAccount = confirm("이미 로그인되어 있습니다. 다른 계정으로 로그인하시겠습니까?");
            if (loginAnotherAccount) {
                window.location.href = '/member/logincontroller/processLogoutAndRedirectLoginPage';
            } else {
                // 쿠키에서 리다이렉션 URL 읽기
                var redirectUrl = getCookie('redirect_url');
                if (redirectUrl && redirectUrl !== '/member/logincontroller') {
                    // 리다이렉션 URL이 로그인 페이지가 아닌 경우 이전 페이지로 돌아가기
                    history.back();
                } else {
                    // 리다이렉션 URL이 로그인 페이지이거나 설정되지 않은 경우 루트 경로로 리다이렉션
                    window.location.href = '/';
                }
            }
        }
    });

    $('#togglePassword').click(function () {
        var passwordField = $('#password');
        var passwordFieldType = passwordField.attr('type');

        if (passwordFieldType == 'password') {
            passwordField.attr('type', 'text');
            $(this).children('i').addClass('fa-eye-slash').removeClass('fa-eye');
        } else {
            passwordField.attr('type', 'password');
            $(this).children('i').removeClass('fa-eye-slash').addClass('fa-eye');
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