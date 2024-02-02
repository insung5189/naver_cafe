$(document).ready(function() {
    var isEmailDuplicateChecked = false;
    var isNicknameDuplicateChecked = false;

    $('#duplicateEmail').click(function() {
        duplicateEmail();
    });

    $('#duplicateNickname').click(function() {
        duplicateNickname();
    });



    function duplicateEmail() {
        const userName = $('#userName').val();
        $.ajax({
            url: '/member/signupcontroller/checkEmail',
            type: 'POST',
            dataType: 'json',
            data: { userName: userName },
            success: function(response) {
                if (response.isDuplicate) {
                    $('#email-duplication-check-message').text('❌ 이미 사용 중인 이메일입니다.').css('color', 'red');
                    isEmailDuplicateChecked = false;
                } else {
                    $('#email-duplication-check-message').text('✔️ 사용 가능한 이메일입니다.').css('color', 'green');
                    isEmailDuplicateChecked = true;
                }
            },
            error: function() {
                alert('이메일 중복 확인 중 오류가 발생했습니다.');
            }
        });
    }
    
    function duplicateNickname() {
        const nickName = $('#nickName').val();
        $.ajax({
            url: '/member/signupcontroller/checkNickname',
            type: 'POST',
            dataType: 'json',
            data: { nickName: nickName },
            success: function(response) {
                if (response.isDuplicate) {
                    $('#nickname-duplication-check-message').text('❌ 이미 사용 중인 닉네임입니다.').css('color', 'red');
                    isNicknameDuplicateChecked = false; // 중복된 닉네임, 중복 확인 필요
                } else {
                    $('#nickname-duplication-check-message').text('✔️ 사용 가능한 닉네임입니다.').css('color', 'green');
                    isNicknameDuplicateChecked = true; // 중복 확인 완료
                }
            },
            error: function() {
                alert('닉네임 중복 확인 중 오류가 발생했습니다.');
            }
        });
    }
    
    $('#upload-image').click(function() {
        $('#file').click();
    });

    // 이미지 미리보기 및 파일 정보 표시
    $('#file').change(function(event) {
        const file = event.target.files[0];

        // 파일이 이미지인지 확인
        if (!file.type.match('image.*')) {
            alert('이미지 파일만 업로드 가능합니다.');
            $('#file').val("");
            return;
        }

        // 파일 사이즈 체크 (50MB 제한)
        if (file.size > 52428800) {
            alert('파일 크기가 너무 큽니다. 50MB 이하의 파일을 선택해주세요.');
            $('#file').val("");
            return;
        }

        // 이미지 미리보기 및 파일 정보 표시
        const preview = $('.image-preview');
        const fileInfo = $('#file-info');
        let fileSize = file.size / 1024; // KB 단위로 변환
        let fileSizeUnit = 'KB';
        if (fileSize > 1024) {
            fileSize = fileSize / 1024; // MB 단위로 변환
            fileSizeUnit = 'MB';
        }
        fileInfo.html(`파일이름 : ${file.name}<br> 파일용량 : ${fileSize.toFixed(2)} ${fileSizeUnit}`);

        const reader = new FileReader();
        reader.onload = function() {
            preview.attr('src', reader.result);
        };
        reader.readAsDataURL(file);
    });

    // 이미지 삭제 및 초기화
    $('#remove-image').click(function() {
        const preview = $('.image-preview');
        const fileInfo = $('#file-info');
        preview.attr('src', 'https://i.imgur.com/0Vhk4jx.png'); // 기본 이미지로 재설정
        fileInfo.html(''); // 파일 정보 초기화
        $('#file').val(""); // 파일 입력 필드 초기화
    });

    // 이메일 입력란
    $('#userName').on('keyup', function() {
        validateEmail();
    });

    $('#password1, #password2').on('keyup', function() {
        validatePassword();
        checkPasswordMatch();
    });

    $('#phone').on('keyup', function() {
        validatePhone();
    });
    

    // 폼 제출 시 유효성 검사
    $('form').on('submit', function(event) {
        if (!validateEmail() || !checkPasswordMatch() || !validatePassword() || !validatePhone() || !isEmailDuplicateChecked || !isNicknameDuplicateChecked) {
            event.preventDefault();
            // 이메일, 비밀번호 불일치나 유효성 검사 실패 시 스크롤 이동
            if (!validateEmail()) {
                scrollError('userName'); // 이메일 유효성 검사 실패 시 스크롤 이동
                alert('올바른 이메일 형식이 아닙니다.');
            }
            if (!checkPasswordMatch()) {
                scrollError('password2'); // 비밀번호 불일치 시 스크롤 이동
                alert('비밀번호가 일치하지 않습니다.');
            }
            if (!validatePassword()) {
                scrollError('password1'); // 비밀번호 유효성 검사 실패 시 스크롤 이동
                alert('비밀번호는 영문, 숫자, 특수문자를 포함한 \n8자 이상이어야 합니다.');
            }
            if (!validatePhone()) {
                scrollError('phone'); // 연락처 유효성 검사 실패 시 스크롤 이동
                alert('전화번호 형식에 맞추어서\n입력해주시기 바랍니다.');
            }
            if (!isEmailDuplicateChecked) {
                scrollError('userName'); // 이메일 입력란으로 스크롤 이동
                alert('이메일 중복 확인을 해주세요.');
            }
            if (!isNicknameDuplicateChecked) {
                scrollError('nickName'); // 닉네임 입력란으로 스크롤 이동
                alert('닉네임 중복 확인을 해주세요.');
            }
        }
    });



    // 이메일 유효성 검사 input
    function validateEmail() {
        const emailInput = $('#userName');
        const emailValidationMessage = $('#email-validation-message');
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

        if (emailPattern.test(emailInput.val())) {
            emailValidationMessage.text('✔️ 올바른 이메일 형식입니다.');
            emailValidationMessage.css('color', 'green');
            return true;
        } else {
            emailValidationMessage.text('❌ 올바른 이메일 형식이 아닙니다.');
            emailValidationMessage.css('color', 'red');
            return false;
        }
    }

    // 비밀번호 유효성 검사 input
    function validatePassword() {
        const password1 = $('#password1').val();
        const validationMessage = $('#password-validation-message');
    
        // 조건 검사
        const hasLetter = /[a-zA-Z]/.test(password1);
        const hasDigit = /\d/.test(password1);
        const hasSpecialChar = /[!@#$%^&*()\-_=+\[\]{}|;:'",<.>/?]/.test(password1);
        const isLongEnough = password1.length >= 8;
    
        // 상세한 조건 불충족 메시지
        let message = '';
        if (!hasLetter) message += '영문자, ';
        if (!hasDigit) message += '숫자, ';
        if (!hasSpecialChar) message += '특수문자, ';
        if (!isLongEnough) message += '8자 이상, ';
    
        if (message !== '') {
            // 마지막 쉼표 제거
            message = message.slice(0, -2);
            validationMessage.html(`❌ 다음을 포함해야 합니다: ${message}`).css('color', 'red');
            return false;
        } else {
            validationMessage.text('✔️ 사용 가능한 비밀번호 입니다.').css('color', 'green');
            return true;
        }
    }

    // 비밀번호 일치 확인 input
    function checkPasswordMatch() {
        const password1 = $('#password1').val();
        const password2 = $('#password2').val();
        const matchMessage = $('#password-match-message');
    
        if (password1 === '' && password2 === '') {
            matchMessage.text('');
            return true; // 두 필드가 모두 비어있는 경우는 초기 상태로 간주함
        } else if (password1 === '') {
            matchMessage.text('❌ 사용할 비밀번호가 입력되지 않았습니다.').css('color', 'red');
            return false;
        } else if (password2 === '') {
            matchMessage.text('❌ 비밀번호가 입력되지 않았습니다.').css('color', 'red');
            return false;
        } else if (password1 !== password2) {
            matchMessage.text('❌ 비밀번호 불일치').css('color', 'red');
            return false;
        } else {
            matchMessage.text('✔️ 비밀번호 일치').css('color', 'green');
            return true;
        }
    }

    function validatePhone() {
        const phoneInput = $('#phone');
        const message = $('#phone-validation-message');
        const phoneValue = phoneInput.val().replace(/-/g, ''); // 하이픈 제거
    
        // 한국 전화번호 패턴 (휴대전화, 유선전화, 인터넷전화)
        const phonePattern = /^(01(?:0|1|[6-9])\d{7,8})|(0(?:2|3[1-3]|4[1-4]|5[1-5]|6[1-4])\d{7,8})|(070\d{7,8})$/;
    
        if (!phonePattern.test(phoneValue)) {
            message.text('❌ 유효하지 않은 전화번호 형식입니다. (하이픈 제외)').css('color', 'red');
            return false;
        } else {
            message.text('✔️ 유효한 전화번호 형식입니다.').css('color', 'green');
            return true;
        }
    }
    


    // 앵커
    function scrollError(elementId) {
        const element = $('#' + elementId);
        if (element.length > 0) {
            $('html, body').animate({
                scrollTop: element.offset().top - 100
            }, 500);
            element.focus();
        }
    }

    $('#birth').on('input', function() {
        const inputDate = $('#birth').val();
        const inputDateError = $('#birthDateError');
        const datePattern = /^\d{4}-\d{2}-\d{2}$/;
        const currentYear = new Date().getFullYear();
        const minYear = currentYear - 120; // 120년 전까지 유효한 날짜로 설정
        const maxYear = currentYear; // 현재 연도까지 유효
    
        
        if (!datePattern.test(inputDate)) {
            inputDateError.text('❌ 올바른 날짜 형식을 입력해주세요.');
            inputDateError.css('visibility', 'visible');
            return;
        }
            
        const [year, month, day] = inputDate.split('-').map(Number);
        const dateObj = new Date(year, month - 1, day); // JavaScript의 월은 0부터 시작
        if (dateObj.getFullYear() !== year || dateObj.getMonth() + 1 !== month || dateObj.getDate() !== day) {
            inputDateError.text('❌ 존재하지 않는 날짜입니다.');
            inputDateError.css('visibility', 'visible');
            return;
        }
            
        if (year < minYear || year > maxYear) {
            inputDateError.text(`❌ 생년월일은 ${minYear}년부터 ${maxYear}년 사이여야 합니다.`);
            inputDateError.css('visibility', 'visible');
            return;
        }
        inputDateError.css('visibility', 'hidden');
    });

});

function updateFileName() {
    var input = document.getElementById('file');
    var fileName = document.getElementById('file-name');
    if (input.files && input.files.length > 0) {
        fileName.textContent = input.files[0].name;
    } else {
        fileName.textContent = '';
    }
}