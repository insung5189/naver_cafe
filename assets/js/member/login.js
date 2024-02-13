$(document).ready(function() {
    $('#userName').on('keyup', validateEmail);

    // 이메일 유효성 검사 keyup
    function validateEmail() {
        const emailInput = $('#userName');
        const email = emailInput.val(); // 앞뒤 공백을 제거하지 않은 원본 이메일 값
        const emailValidationMessage = $('#email-validation-message');
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

        // 입력값의 시작이나 끝에 공백이 있는지 검사하는 정규식
        const leadingOrTrailingWhitespacePattern = /^\s+|\s+$/;

        if (leadingOrTrailingWhitespacePattern.test(email)) {
            // 입력값의 시작이나 끝에 공백이 있는 경우
            emailValidationMessage.text('❌ 이메일 주소의 시작이나 끝에 공백을 포함할 수 없습니다.');
            emailValidationMessage.css('color', 'red');
            return false;
        } else if (!emailPattern.test(email)) {
            // 이메일 형식이 올바르지 않음
            emailValidationMessage.text('❌ 올바른 이메일 형식이 아닙니다.');
            emailValidationMessage.css('color', 'red');
            return false;
        } else {
            // 올바른 이메일 형식
            emailValidationMessage.text('✔️ 올바른 이메일 형식입니다.');
            emailValidationMessage.css('color', 'green');
            return true;
        }
    }
});