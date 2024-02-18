$(document).ready(function() {
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