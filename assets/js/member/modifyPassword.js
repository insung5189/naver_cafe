$(document).ready(function () {
    $('#modifyPassword').on('submit', function(event) {
        event.preventDefault();
        const form = $(this);
        const oldPasswordValidationMessage = $('#oldpassword-validation-message').text('');
        const newPasswordValidationMessage = $('#newpassword-validation-message').text('');
        const newPasswordCfValidationMessage = $('#newpasswordcf-validation-message').text('');

        const oldPassword = $('#oldpassword').val();
        const newPassword = $('#newpassword').val();
        const newPasswordConfirm = $('#newpasswordcf').val();

        if (!validateNewPassword(newPassword)) {
            newPasswordValidationMessage.text('❌ 신규 비밀번호는 영문, 숫자, 특수문자를 포함한 8자 이상이어야 합니다.').css('color', 'red');
            return;
        }

        if (oldPassword === newPassword) {
            newPasswordValidationMessage.text('❌ 신규 비밀번호는 기존 비밀번호와 달라야 합니다.').css('color', 'red');
            return;
        }

        if (newPassword !== newPasswordConfirm) {
            newPasswordCfValidationMessage.text('❌ 신규 비밀번호 확인이 일치하지 않습니다.').css('color', 'red');
            return;
        }

        // 기존 비밀번호 확인 AJAX 요청
        $.ajax({
            type: 'POST',
            url: '/member/findaccountcontroller/oldpasswordcf',
            data: {
                oldpassword: oldPassword
            },
            dataType: 'json',
            success: function(result) {
                if (result.success) {
                    form.off('submit').submit();
                } else {
                    oldPasswordValidationMessage.text(result.message).css('color', 'red');
                }
            },
            error: function(xhr, status, error) {
                alert("기존 비밀번호 확인에 실패했습니다. 다시 시도해주세요.: " + error);
            }
        });
    });

    function validateNewPassword(password) {
        const hasLetter = /[a-zA-Z]/.test(password);
        const hasDigit = /\d/.test(password);
        const hasSpecialChar = /[!@#$%^&*()\-_=+\[\]{}|;:'",<.>/?]/.test(password);
        const isLongEnough = password.length >= 8;
        return hasLetter && hasDigit && hasSpecialChar && isLongEnough;
    }
});