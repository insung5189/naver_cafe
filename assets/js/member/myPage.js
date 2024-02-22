$(document).ready(function () {

    $('#modifyPasswordTitle, #modifyPasswordGuide, #modifyPasswordSection').hide();
    $('#myPageTitle, #myPageGuide, .my-profile-info').show();

    $('.sort_area a').click(function () {
        $('.sort_area a').removeClass('on underline');

        $(this).addClass('on underline');

        if ($(this).text().trim() === "내 정보 조회 / 수정") {
            $('#modifyPasswordTitle, #modifyPasswordGuide, #modifyPasswordSection').hide();
            $('#myPageTitle, #myPageGuide, .my-profile-info').show();
        } else if ($(this).text().trim() === "비밀번호 변경") {
            $('#myPageTitle, #myPageGuide, .my-profile-info').hide();
            $('#modifyPasswordTitle, #modifyPasswordGuide, #modifyPasswordSection').show();
        }
    });

    // 이름, 성 유니코드 정규검사식
    const nameRegex = /^[A-Za-z\u00C0-\u00FF\u0100-\u017F\u0180-\u024F\u0370-\u03FF\u0400-\u04FF\u1E00-\u1EFF\u2C00-\u2C7F\u2D00-\u2D2F\u3000-\u303F\u3400-\u4DBF\u4E00-\u9FFF\uA000-\uA48F\uA490-\uA4CF\uAC00-\uD7AF\uF900-\uFAFF\uFE30-\uFE4F-'\s]+$/;

    // 이벤트 핸들러 등록
    $('#duplicateNickname').click(duplicateNickname);
    $('#upload-image').click(function () {
        $('#file').click();
    });
    $('#nickName').on('keyup', validateNickname);
    $('#nickName').on('keyup, focus', resetNicknameValidation);
    $('#phone').on('keyup, focus', validatePhone);
    $('#firstName').on('keyup, focus', validateFirstName);
    $('#lastName').on('keyup, focus', validateLastName);
    $('#gender').change(validateGenderSelect);
    $('#birth').on('keyup, focus', validateBirthDate);
    $('#birth').on('blur, focus', validateBirthDate);
    $('form').on('submit', submitFormValidation);

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

    // 연락처 유효성 검사
    function validatePhone() {
        const phoneInput = $('#phone');
        const message = $('#phone-validation-message');
        const phoneValue = phoneInput.val().replace(/-/g, ''); // 하이픈 제거

        // 국제 전화번호 패턴 검사 (한국 포함)
        const phonePattern = /^(\+\d{1,3}-?)?(01[016-9]|02|0[3-6][1-5]?|070)-?([1-9]\d{2,3}-?\d{4})$/;

        if (!phonePattern.test(phoneValue)) {
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
            alert('⚠ 입력값 수정을 시도하셨습니다.\n닉네임 입력 후 꼭 중복확인 시도해주세요.');
        }
    }

    // 닉네임 유효성 검사
    function validateNickname() {
        const nicknameInput = $('#nickName').val();
        const nicknameValidationMessage = $('#nickname-duplication-check-message');

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
            message += '✔️ 한글 또는 영문 대소문자(필수)<br>✔️ 숫자(선택)<br>';
        } else {
            if (!hasKoreanOrEnglish) {
                message += '❌ 한글 또는 영문 대소문자(필수)<br>✔️ 숫자(선택)<br>';
            }
            if (hasInvalidCharacter) {
                message = '❌ 유효하지 않은 문자 포함<br>';
            }
        }

        // 메시지 출력
        nicknameValidationMessage.html(message).css('color', lengthCheck && hasKoreanOrEnglish && !hasInvalidCharacter ? 'green' : 'red');
        return lengthCheck && hasKoreanOrEnglish && !hasInvalidCharacter;
    }

    // 이름 유효성 검사
    function validateFirstName() {
        const firstName = $('#firstName').val().trim();
        const firstNameMessage = $('#firstname-validation-message');

        if (firstName == null || firstName == '') {
            firstNameMessage.text('❌ 이름을 입력해주세요.').css('color', 'red');
            return false;
        }
        if (!nameRegex.test(firstName)) {
            firstNameMessage.text('❌ 잘못된 입력입니다.').css('color', 'red');
            return false;
        } else {
            firstNameMessage.text('✔️');
            return true;
        }

    }

    // 성 유효성 검사
    function validateLastName() {
        const lastName = $('#lastName').val().trim();
        const lastNameMessage = $('#lastname-validation-message');

        if (lastName == null || lastName == '') {
            lastNameMessage.text('❌ 성을 입력해주세요.').css('color', 'red');
            return false;
        }
        if (!nameRegex.test(lastName)) {
            lastNameMessage.text('❌ 잘못된 입력입니다.').css('color', 'red');
            return false;
        } else {
            lastNameMessage.text('✔️');
            return true;
        }
    }

    // 성별 유효성검사
    function validateGenderSelect() {
        const genderSelect = $('#gender'); // jQuery 객체로 select 요소 자체를 참조
        const selectedValue = genderSelect.val(); // 선택된 값
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
        if (!validatePhone() || !validateNickname() || !validateFirstName() || !validateLastName() || !validateGenderSelect() || !validateBirthDate() || $('#isNickNameChecked').val() !== 'true') {
            event.preventDefault();
            if (!validatePhone()) {
                scrollError('phone');
                alert('전화번호 형식에 맞추어서\n입력해주시기 바랍니다.');
            }
            if (!validateNickname()) {
                scrollError('nickName');
                alert('닉네임 형식에 맞추어서\n입력해주시기 바랍니다.');
            }
            if (!validateFirstName()) {
                scrollError('firstName');
                alert('이름(First name / Given name) 입력이 잘못되었습니다.');
            }
            if (!validateLastName()) {
                scrollError('lastName');
                alert('성(Last name / Family name) 입력이 잘못되었습니다.');
            }
            if (!validateGenderSelect()) {
                scrollError('gender');
            }
            if (!validateBirthDate()) {
                scrollError('birth');
                alert('생년월일을 다시 확인해주세요.');
            }
            if ($('#isNickNameChecked').val() !== 'true') {
                scrollError('nickName');
                alert('닉네임 중복 확인을 해주세요.');
            }
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
});