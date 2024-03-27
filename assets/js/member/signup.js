$(document).ready(function () {

    // 사용자가 작성 중이던 form 페이지를 떠나려 할 때 표시되는 경고메시지
    var formModified = false;

    $('form input, form textarea, form select').change(function () {
        formModified = true;
    });

    $(window).on('beforeunload', function () {
        if (formModified) {
            return '변경사항이 저장되지 않을 수 있습니다.';
        }
    });

    $('form').submit(function () {
        $(window).off('beforeunload');
    });

    // 중복확인 상태 변수
    var isNicknameDuplicateChecked = false;

    // 이름, 성 유니코드 정규검사식
    const nameRegex = /^[A-Za-z\u00C0-\u00FF\u0100-\u017F\u0180-\u024F\u0370-\u03FF\u0400-\u04FF\u1E00-\u1EFF\u2C00-\u2C7F\u2D00-\u2D2F\u3000-\u303F\u3400-\u4DBF\u4E00-\u9FFF\uA000-\uA48F\uA490-\uA4CF\uAC00-\uD7AF\uF900-\uFAFF\uFE30-\uFE4F-'\s]+$/;

    // 이벤트 핸들러 등록
    $('#duplicateEmail').click(duplicateEmail);
    $('#duplicateNickname').click(duplicateNickname);
    $('#upload-image').click(function () {
        $('#file').click();
    });
    $('#file').change(updateImagePreview);
    $('#remove-image').click(resetImagePreview);
    $('#userName').on('keyup', validateEmail);
    $('#userName').on('keyup', resetEmailValidation);
    $('#nickName').on('keyup', validateNickname);
    $('#nickName').on('keyup', resetNicknameValidation);
    $('#password1, #password2').on('keyup', validatePasswordAndMatch);
    $('#phone').on('keyup', validatePhone);
    // $('#firstName').on('keyup', validateFirstName);
    // $('#lastName').on('keyup', validateLastName);
    $('#gender').change(validateGenderSelect);
    $('#birth').on('keyup', validateBirthDate);
    $('#birth').on('blur', validateBirthDate);

    $('#togglePassword').click(function () {
        var passwordField = $('#password1');
        var passwordFieldType = passwordField.attr('type');

        if (passwordFieldType == 'password') {
            passwordField.attr('type', 'text');
            $(this).children('i').addClass('fa-eye-slash').removeClass('fa-eye');
        } else {
            passwordField.attr('type', 'password');
            $(this).children('i').removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    function duplicateEmail() {
        const userName = $('#userName').val();
        if (!userName) {
            alert('이메일을 입력해주세요.');
            return;
        }
        if (validateEmail()) {
            $.ajax({
                url: '/member/signupcontroller/checkEmail',
                type: 'POST',
                dataType: 'json',
                data: { userName: userName },
                success: function (response) {
                    handleEmailResponse(response);
                },
                error: function () {
                    alert('이메일 중복 확인 중 오류가 발생했습니다.');
                    return false;
                }
            });
        } else {
            alert('올바른 이메일 형식이 아닙니다.');
            return false;
        }
    }

    function handleEmailResponse(response) {
        if (response.isDuplicate) {
            alert('이미 사용 중인 이메일입니다.');
            $('#email-duplication-check-message').text('❌ 이미 사용 중인 이메일입니다.').css('color', 'red');
            $('#isUserNameChecked').val('false');
            return false;
        } else {
            if (confirm('사용 가능한 이메일입니다.\n확인 버튼을 누르시면 해당 이메일을 사용하며 \n수정이 불가능합니다.')) {
                $('#email-duplication-check-message').text('✔️ 사용 가능한 이메일입니다.').css('color', 'green');
                $('#duplicateEmail').val('중복확인 완료');
                $('#userName').attr('readonly', true);
                $('#duplicateEmail').attr('disabled', true);
                $('#isUserNameChecked').val('true');
                return true;
            }
        }
    }

    function duplicateNickname() {
        const nickName = $('#nickName').val();
        if (!nickName) {
            alert('닉네임을 입력해주세요.');
            return;
        }
        if (validateNickname()) {
            $.ajax({
                url: '/member/signupcontroller/checkNickname',
                type: 'POST',
                dataType: 'json',
                data: { nickName: nickName },
                success: function (response) {
                    handleNicknameResponse(response);
                },
                error: function () {
                    alert('닉네임 중복 확인 중 오류가 발생했습니다.');
                    return false;
                }
            });
        } else {
            alert('올바른 닉네임 형식이 아닙니다.');
            return false;
        }
    }

    function handleNicknameResponse(response) {
        if (response.isDuplicate) {
            alert('이미 사용 중인 닉네임입니다.');
            $('#nickname-duplication-check-message').text('❌ 이미 사용 중인 닉네임입니다.').css('color', 'red');
            $('#isNickNameChecked').val('false');
            return false;
        } else {
            if (confirm('사용 가능한 닉네임입니다.\n확인 버튼을 누르시면 해당 닉네임을 사용하며 \n수정이 불가능합니다.')) {
                $('#nickname-duplication-check-message').text('✔️ 사용 가능한 닉네임입니다.').css('color', 'green');
                $('#duplicateNickname').val('중복확인 완료');
                $('#nickName').attr('readonly', true);
                $('#duplicateNickname').attr('disabled', true);
                $('#isNickNameChecked').val('true');
                return true;
            }
        }
    }

    // 이미지 미리보기 및 파일 정보 표시
    function updateImagePreview(event) {
        const file = event.target.files[0];
        const fileInfo = $('#file-info');

        if (!file) {
            $('.image-preview').attr('src', 'https://i.imgur.com/0Vhk4jx.png');
            $('#file').val("");
            $('#file-info').html(`등록된 파일이 없습니다. <br>기본 이미지가 적용됩니다.`);
            return;
        }

        if (!(file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/bmp' || file.type === 'image/jpg')) {
            alert('이미지 파일만 업로드 가능합니다.');
            $('#file').val("");
            $('.image-preview').attr('src', 'https://i.imgur.com/0Vhk4jx.png');
            fileInfo.html(`이미지 파일만 등록 가능합니다. <br>다시 등록해주세요.`);
            return;
        }

        if (file.size > 3145728) {
            alert('파일 크기가 너무 큽니다. 3MB 이하의 파일을 선택해주세요.');
            $('#file').val("");
            $('.image-preview').attr('src', 'https://i.imgur.com/0Vhk4jx.png');
            fileInfo.html(`3mb이하의 파일만 등록 가능합니다. <br>다시 등록해주세요.`);
            return;
        }

        let fileSize = file.size / 1024; // KB 단위로 변환
        let fileSizeUnit = 'KB';
        if (fileSize > 1024) {
            fileSize = fileSize / 1024; // MB 단위로 변환
            fileSizeUnit = 'MB';
        }
        fileInfo.html(`파일이름 : ${file.name}<br> 파일용량 : ${fileSize.toFixed(2)} ${fileSizeUnit}`);

        const reader = new FileReader();
        reader.onload = function (e) {
            $('.image-preview').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
    }

    // 이미지 삭제 및 초기화
    function resetImagePreview() {
        $('.image-preview').attr('src', 'https://i.imgur.com/0Vhk4jx.png');
        $('#file').val("");
        $('#file-info').html(`등록된 파일이 없습니다. <br>기본 이미지가 적용됩니다.`);
    }

    // 이메일 입력란 변경 시 로직
    function resetEmailValidation() {
        if ($('#isUserNameChecked').val() === 'true') {
            $('#isUserNameChecked').val('false');
            $('#duplicateEmail').val('중복확인');
            $('#userName').attr('readonly', false);
            $('#duplicateEmail').attr('disabled', false);
            $('#email-duplication-check-message').text('');
            alert('⚠ 중복확인 후 입력값 수정을 시도하셨습니다.\n아이디 중복확인을 다시 시도해주세요.');
        }
    }

    // 이메일 유효성 검사 keyup
    function validateEmail() {
        const emailInput = $('#userName');
        const email = emailInput.val();
        const emailValidationMessage = $('#email-duplication-check-message');
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        const emailDuplicationCheck = $('#isUserNameChecked');

        const leadingOrTrailingWhitespacePattern = /^\s+|\s+$/;

        if (emailDuplicationCheck.val() !== 'true') {
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
        } else if (emailDuplicationCheck.val() === 'true') {
            emailValidationMessage.text('');
            $('#email-duplication-check-message').text('✔️ 사용 가능한 이메일입니다.').css('color', 'green');
            return true;
        }

    }

    // 비밀번호 유효성 검사 및 일치 확인
    function validatePasswordAndMatch() {
        validatePassword();
        checkPasswordMatch();
    }

    // 비밀번호 유효성 검사 keyup
    function validatePassword() {
        const password = $('#password1').val();
        const email = $('#userName').val(); // 이메일 값 가져오기
        const validationMessage = $('#password-validation-message');

        // 조건 검사
        const hasLetter = /[a-zA-Z]/.test(password);
        const hasDigit = /\d/.test(password);
        // 허용하는 특수문자 중 시스템 예약문자와 URL에 안전하지 않은 문자, 공백, ~, \, /를 제외
        const hasSpecialChar = /[!@#$%^*()\-_+=\[\]{}|;:'",.<>?]/.test(password);
        const isLongEnough = password.length >= 8;
        // 허용하지 않는 문자 포함: 시스템 예약문자, URL에 안전하지 않은 문자, 공백, ~, \, /
        const hasInvalidChar = /[\'"&?=#%\s~\\/]/.test(password);
        const isNotEmail = password !== email;

        let message = '';

        if (!isNotEmail) {
            message = '❌ 비밀번호는 이메일과 같을 수 없습니다.';
        } else if (hasInvalidChar) {
            message = '❌ 유효하지 않은 문자 포함(공백, #, %, &, =, \', ", ? 등)';
        } else {
            // 나머지 조건별 메시지 추가
            message += hasLetter ? '✔️ 영문, ' : '❌ 영문, ';
            message += hasDigit ? '✔️ 숫자, ' : '❌ 숫자, ';
            message += hasSpecialChar ? '✔️ 특수문자, ' : '❌ 특수문자, ';
            message += isLongEnough ? '✔️ 8자 이상 ' : '❌ 8자 이상 ';
        }

        // 메시지 표시
        message = message.trim().replace(/, $/, '');
        validationMessage.html(message).css('color', hasLetter && hasDigit && hasSpecialChar && isLongEnough && !hasInvalidChar && isNotEmail ? 'green' : 'red');
        return hasLetter && hasDigit && hasSpecialChar && isLongEnough && !hasInvalidChar && isNotEmail;
    }

    // 비밀번호 일치 확인 keyup
    function checkPasswordMatch() {
        const password1 = $('#password1').val();
        const password2 = $('#password2').val();
        const matchMessage = $('#password-match-message');

        if (validatePassword()) {
            if (password1 === '' && password2 === '') {
                matchMessage.text('');
                return true;
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
        } else {
            matchMessage.text('❌ 비밀번호 패턴 불만족').css('color', 'red');
            return false;
        }
    }

    // 연락처 유효성 검사
    function validatePhone() {
        const phoneInput = $('#phone');
        const message = $('#phone-validation-message');
        const phoneValue = phoneInput.val();

        // 국제 전화번호 패턴 검사 (한국 포함, 하이픈(-) 제거)
        const phonePattern = /^(?:(\+1|\+33|\+44|\+49|\+82|\+39|\+34|\+81|\+61|\+55|\+52|\+46|\+47|\+45|\+358|\+90|\+48|\+32|\+36|\+31|\+43|\+41|\+64)([1-9]\d{6,14}))$|^((02|0[3-9][0-9]?|070)([1-9]\d{6,7})|(01[016789])([1-9]\d{6,7}))$/;

        if (phoneValue.includes('-')) {
            message.text('❌ 하이픈(-)을 제거하고 숫자만 입력해주세요.').css('color', 'red');
            return false;
        } else if (!phonePattern.test(phoneValue)) {
            message.text('❌ 유효하지 않은 전화번호 형식입니다.').css('color', 'red');
            return false;
        } else {
            message.text('✔️ 유효한 전화번호 형식입니다.').css('color', 'green');
            return true;
        }
    }

    // 닉네임 입력란 변경 시 로직
    function resetNicknameValidation() {
        if ($('#isNickNameChecked').val() === 'true') {
            $('#isNickNameChecked').val('false');
            $('#duplicateNickname').val('중복확인');
            $('#nickName').attr('readonly', false);
            $('#duplicateNickname').attr('disabled', false);
            $('#nickname-duplication-check-message').text('');
            alert('⚠ 중복확인 후 입력값 수정을 시도하셨습니다.\n닉네임 중복확인을 다시 시도해주세요.');
        }
    }

    // 닉네임 유효성 검사
    function validateNickname() {
        const nicknameInput = $('#nickName').val();
        const nicknameValidationMessage = $('#nickname-duplication-check-message');
        const nickNameDuplicationCheck = $('#isNickNameChecked');

        if (nickNameDuplicationCheck.val() !== 'true') {
            // 길이 검사
            const lengthCheck = nicknameInput.length >= 2 && nicknameInput.length <= 10;
            const hasKoreanOrEnglish = /[가-힣a-zA-Z]+/.test(nicknameInput);
            const hasInvalidCharacter = /[^가-힣a-zA-Z0-9]/.test(nicknameInput);

            // 상세 메시지 생성
            let message = '';

            // 길이 조건
            message += lengthCheck ? '✔️ 2~10글자(필수)<br>' : '❌ 2~10글자(필수)<br>';

            // 문자 유형 조건
            if (hasKoreanOrEnglish && !hasInvalidCharacter) {
                message += '✔️ 한글 또는 영문 대소문자(필수)<br>';
            } else {
                if (!hasKoreanOrEnglish) {
                    message = '❌ 한글 또는 영문 대소문자(필수)';
                }
                if (hasInvalidCharacter) {
                    message = '❌ 유효하지 않은 문자 포함<br>';
                }
            }

            // 메시지 출력
            nicknameValidationMessage.html(message).css('color', lengthCheck && hasKoreanOrEnglish && !hasInvalidCharacter ? 'green' : 'red');
            return lengthCheck && hasKoreanOrEnglish && !hasInvalidCharacter;
        } else if (nickNameDuplicationCheck.val() === 'true') {
            nicknameValidationMessage.html('');
            $('#nickname-duplication-check-message').text('✔️ 사용 가능한 닉네임입니다.').css('color', 'green');
            return true;
        }

    }

    // 이름 유효성 검사
    function validateFirstName() {
        const firstName = $('#firstName').val();
        const firstNameMessage = $('#firstname-validation-message');

        if (firstName == null || firstName == '') {
            firstNameMessage.text('❌ 이름을 입력해주세요.').css('color', 'red');
            return false;
        }
        if (!nameRegex.test(firstName) || /^\s|\s$/.test(firstName)) {
            firstNameMessage.text('❌ 잘못된 입력입니다.').css('color', 'red');
            return false;
        } else {
            firstNameMessage.text('✔️');
            return true;
        }

    }

    // 성 유효성 검사
    function validateLastName() {
        const lastName = $('#lastName').val();
        const lastNameMessage = $('#lastname-validation-message');

        if (lastName == null || lastName == '') {
            lastNameMessage.text('❌ 성을 입력해주세요.').css('color', 'red');
            return false;
        }
        if (!nameRegex.test(lastName) || /^\s|\s$/.test(lastName)) {
            lastNameMessage.text('❌ 잘못된 입력입니다.').css('color', 'red');
            return false;
        } else {
            lastNameMessage.text('✔️');
            return true;
        }
    }

    // 성별 유효성검사
    function validateGenderSelect() {
        const genderSelect = $('#gender');
        const selectedValue = genderSelect.val();
        const genderValidationMessage = $('#gender-validation-message');

        if (selectedValue === null || selectedValue === '') {
            genderValidationMessage.text('✔️');
            return true;
        }

        if (selectedValue !== 'true' && selectedValue !== 'false' && selectedValue !== '' && selectedValue !== null) {
            genderValidationMessage.text('❌ 유효하지 않은 성별값입니다.').css('color', 'red');
            alert('성별 입력이 잘못되었습니다.\n성별을 다시 선택해주세요.');
            genderSelect.empty(); // 기존의 option들을 모두 제거
            genderSelect.append($('<option>', {
                value: '',
                text: '성별을 선택하세요',
                selected: true
            }));
            genderSelect.append($('<option>', { value: 'true', text: '남성' }));
            genderSelect.append($('<option>', { value: 'false', text: '여성' }));

            return false;
        } else {
            genderValidationMessage.text('✔️').css('color', 'green');
            return true;
        }
    }

    // 생년월일 유효성 검사
    function validateBirthDate() {
        const inputDate = $('#birth').val();
        const inputDateError = $('#birth-validation-message');
        const datePattern = /^\d{4}-\d{2}-\d{2}$/;
        const today = new Date();
        today.setHours(0, 0, 0, 0); // 오늘 날짜의 시작으로 설정

        // 현재 연도 및 최소/최대 유효 연도 설정
        const currentYear = today.getFullYear();
        const minYear = currentYear - 120; // 지금으로부터 120년 전
        const maxYear = currentYear; // 현재 연도

        if (inputDate === '') {
            inputDateError.text('');
            return true; // 입력하지 않은 경우에도 true를 반환
        }

        if (!datePattern.test(inputDate)) {
            inputDateError.text('❌ 올바른 날짜 형식을 입력해주세요.').css('color', 'red');
            return false;
        }

        const [year, month, day] = inputDate.split('-').map(Number);
        const inputDateObj = new Date(year, month - 1, day);

        const isDateValid = inputDateObj.getFullYear() === year &&
            inputDateObj.getMonth() + 1 === month &&
            inputDateObj.getDate() === day;

        if (!isDateValid) {
            inputDateError.text('❌ 존재하지 않는 날짜입니다.').css('color', 'red');
            return false;
        }

        // 입력된 생년월일이 유효한 연도 범위 내에 있는지 검사
        if (year < minYear || year > maxYear) {
            inputDateError.text(`❌ 년도는 ${minYear}년부터 ${maxYear}년 사이여야 합니다.`).css('color', 'red');
            return false;
        }

        // 입력 날짜가 오늘 날짜를 초과하는지 검사
        if (inputDateObj > today) {
            inputDateError.text('❌ 생년월일은 오늘 날짜를 초과할 수 없습니다.').css('color', 'red');
            return false;
        }

        // 모든 검사를 통과한 경우
        inputDateError.text('✔️').css('color', 'green');
        return true;
    }

    // 폼 제출 시 모든 유효성 검사 확인하여 문제 발생 시 폼 제출 방지
    function submitFormValidation(event) {
        var isValid = true;
        if (!validateEmail()
            || !checkPasswordMatch()
            || !validatePassword()
            || !validatePhone()
            || !validateNickname()
            || !validateFirstName()
            || !validateLastName()
            || !validateGenderSelect()
            || !validateBirthDate()
            || $('#isUserNameChecked').val() !== 'true'
            || $('#isNickNameChecked').val() !== 'true') {
            event.preventDefault();
            if (!validateEmail()) {
                scrollError('userName');
                alert('올바른 이메일 형식이 아닙니다.');
                isValid = false;
            }
            if (!validatePassword()) {
                scrollError('password1');
                alert('비밀번호는 영문, 숫자, 특수문자를 포함한 \n8자 이상이어야 합니다.');
                isValid = false;
            }
            if (!checkPasswordMatch()) {
                scrollError('password2');
                alert('비밀번호가 일치하지 않습니다.');
                isValid = false;
            }
            if (!validatePhone()) {
                scrollError('phone');
                alert('전화번호 형식에 맞추어서\n입력해주시기 바랍니다.');
                isValid = false;
            }
            if (!validateNickname()) {
                scrollError('nickName');
                alert('닉네임 형식에 맞추어서\n입력해주시기 바랍니다.');
                isValid = false;
            }
            if (!validateFirstName()) {
                scrollError('firstName');
                alert('이름(First name / Given name) 입력이 잘못되었습니다.');
                isValid = false;
            }
            if (!validateLastName()) {
                scrollError('lastName');
                alert('성(Last name / Family name) 입력이 잘못되었습니다.');
                isValid = false;
            }
            if (!validateGenderSelect()) {
                scrollError('gender');
                isValid = false;
            }
            if (!validateBirthDate()) {
                scrollError('birth');
                alert('생년월일을 다시 확인해주세요.');
                isValid = false;
            }
            if ($('#isUserNameChecked').val() !== 'true') {
                scrollError('userName');
                alert('이메일 중복 확인을 해주세요.');
                isValid = false;
            }
            if ($('#isNickNameChecked').val() !== 'true') {
                scrollError('nickName');
                alert('닉네임 중복 확인을 해주세요.');
                isValid = false;
            }
        }
        return isValid;
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

    $(document).on('submit', '.sign-up-form', function (event) {
        if (submitFormValidation(event)) {
            var formData = new FormData(this);

            $.ajax({
                url: '/member/signupcontroller/processMemberSignup',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert(response.message);
                        window.location.href = '/';
                    } else {
                        alert('오류발생: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    alert('회원 등록 중 문제가 발생했습니다. 다시 시도해주세요.' + error);
                }
            });
        }
    });
});