$(document).ready(function () {
    $('#newpassword, #newpasswordcf').on('keyup', validatePasswordAndMatch);

    // 비밀번호 유효성 검사 및 일치 확인
    function validatePasswordAndMatch() {
        validatePassword();
        checkPasswordMatch();
    }

    // 비밀번호 유효성 검사 keyup
    function validatePassword() {
        const newpassword = $('#newpassword').val();
        const validationMessage = $('#newpassword-validation-message');

        // 조건 검사
        const hasLetter = /[a-zA-Z]/.test(newpassword);
        const hasDigit = /\d/.test(newpassword);
        const hasSpecialChar = /[!@#$%^&*()\-_=+\[\]{}|;:'",<.>/?]/.test(newpassword);
        const isLongEnough = newpassword.length >= 8;
        const hasInvalidChar = /[^a-zA-Z0-9!@#$%^&*()\-_=+\[\]{}|;:'",<.>/?]/.test(newpassword);

        // 상세한 조건 불충족 메시지 초기화
        let message = '';

        // 조건별 메시지 추가
        message += hasLetter ? '✔️ 영문, ' : '❌ 영문, ';
        message += hasDigit ? '✔️ 숫자, ' : '❌ 숫자, ';
        message += hasSpecialChar ? '✔️ 특수문자, ' : '❌ 특수문자, ';
        message += isLongEnough ? '✔️ 8자 이상 ' : '❌ 8자 이상 ';
        message += !hasInvalidChar ? '' : '<br>❌ 유효하지 않은 문자 포함<br>(공백, 허용되지 않는 특수문자 등)';

        message = message.trim().replace(/, $/, '');
        validationMessage.html(message).css('color', hasLetter && hasDigit && hasSpecialChar && isLongEnough && !hasInvalidChar ? 'green' : 'red');
        if (hasLetter && hasDigit && hasSpecialChar && hasSpecialChar && isLongEnough && !hasInvalidChar) {
            return true;
        } else {
            return false;
        }
    }

    // 비밀번호 일치 확인 keyup
    function checkPasswordMatch() {
        const newpassword = $('#newpassword').val();
        const newpasswordcf = $('#newpasswordcf').val();
        const newPasswordCfValidationMessage = $('#newpasswordcf-validation-message');

        if (validatePassword()) {
            if (newpassword === '' && newpasswordcf === '') {
                newPasswordCfValidationMessage.text('');
                return true;
            } else if (newpassword === '') {
                newPasswordCfValidationMessage.text('❌ 사용할 비밀번호가 입력되지 않았습니다.').css('color', 'red');
                return false;
            } else if (newpasswordcf === '') {
                newPasswordCfValidationMessage.text('❌ 비밀번호가 입력되지 않았습니다.').css('color', 'red');
                return false;
            } else if (newpassword !== newpasswordcf) {
                newPasswordCfValidationMessage.text('❌ 비밀번호 불일치').css('color', 'red');
                return false;
            } else {
                newPasswordCfValidationMessage.text('✔️ 비밀번호 일치').css('color', 'green');
                return true;
            }
        } else {
            newPasswordCfValidationMessage.text('❌ 비밀번호 패턴 불만족').css('color', 'red');
            return false;
        }
    }

    $('#modifyPassword').on('submit', function (event) {
        event.preventDefault();
        const form = $(this);
        const newPasswordValidationMessage = $('#newpassword-validation-message').text('');
        const newPasswordCfValidationMessage = $('#newpasswordcf-validation-message').text('');

        const newPassword = $('#newpassword').val();
        const newPasswordConfirm = $('#newpasswordcf').val();

        if (!validateNewPassword(newPassword)) {
            newPasswordValidationMessage.text('❌ 신규 비밀번호는 영문, 숫자, 특수문자를 포함한 8자 이상이어야 합니다.').css('color', 'red');
            return;
        }

        // if (oldPassword === newPassword) {
        //     newPasswordValidationMessage.text('❌ 신규 비밀번호는 기존 비밀번호와 달라야 합니다.').css('color', 'red');
        //     return;
        // }

        if (newPassword !== newPasswordConfirm) {
            newPasswordCfValidationMessage.text('❌ 신규 비밀번호 확인이 일치하지 않습니다.').css('color', 'red');
            return;
        }

        // // 기존 비밀번호 확인 AJAX 요청
        // $.ajax({
        //     url: '/member/findaccountcontroller/oldpasswordcf',
        //     type: 'POST',
        //     dataType: 'json',
        //     data: {
        //         oldpassword: oldPassword
        //     },
        //     success: function(result) {
        //         if (result.success) {
        //             form.off('submit').submit();
        //         } else {
        //             oldPasswordValidationMessage.text(result.message).css('color', 'red');
        //         }
        //     },
        //     error: function(xhr, status, error) {
        //         alert("기존 비밀번호 확인에 실패했습니다. 다시 시도해주세요.: " + error);
        //     }
        // });
        form.off('submit').submit();
    });

    function validateNewPassword(password) {
        const hasLetter = /[a-zA-Z]/.test(password);
        const hasDigit = /\d/.test(password);
        const hasSpecialChar = /[!@#$%^&*()\-_=+\[\]{}|;:'",<.>/?]/.test(password);
        const isLongEnough = password.length >= 8;
        return hasLetter && hasDigit && hasSpecialChar && isLongEnough;
    }
});