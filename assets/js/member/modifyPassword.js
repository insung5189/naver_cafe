$(document).ready(function() {
    $("#modifyPassword").submit(function(event) {
        event.preventDefault(); // 폼 기본 제출 방지
        MemberModifyPassword__submit(this);
    });
});

function MemberModifyPassword__submit(form) {
    var oldPassword = $(form).find("[name='oldpassword']").val();
    var newPassword = $(form).find("[name='newpassword']").val();
    var newPasswordConfirm = $(form).find("[name='newpasswordcf']").val();

    // AJAX 호출을 통해 비밀번호 유효성 검사 수행
    $.ajax({
        type: 'POST',
        url: '/member/mypagecontroller/CT_validation',
        data: {
            oldpassword: oldPassword,
            newpassword: newPassword,
            newpasswordcf: newPasswordConfirm
        },
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                form.submit();
            } else {
                alert(result.message);
            }
        },
        error: function(xhr, status, error) {
            alert("요청에 실패했습니다: " + error);
        }
    });
}