$(document).ready(function () {

    // 마이페이지 공통 이벤트 핸들러 및 함수
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

    // 앵커(프로필 부분에 별도 적용 예정)
    // function scrollError(elementId) {
    //     const element = $('#' + elementId);
    //     if (element.length > 0) {
    //         $('html, body').animate({
    //             scrollTop: element.offset().top - 100
    //         }, 500);
    //         element.focus();
    //     }
    // }

    let validationResults = {
        isValid: true,
        fieldId: '',
        tabToShow: '',
        validErrorMsg: ''
    };

    // 유효성 검사 실패 시 앵커 함수
    function showErrorAndScrollToField(validationResult) {
        if (!validationResult.isValid && validationResult.validErrorMsg) {
            alert(validationResult.validErrorMsg);

            $('.sort_area a').removeClass('on underline');
            $(validationResult.tabToShow).addClass('on underline');

            // 실패한 검사와 관련된 탭의 내용만 표시
            if (validationResult.tabToShow === '#linkToProfileInfo') {
                $('#modifyPasswordTitle, #modifyPasswordGuide, #modifyPasswordSection').hide();
                $('#myPageTitle, #myPageGuide, .my-profile-info').show();

                // 해당 필드로 스크롤 및 포커스
                $('html, body').animate({
                    scrollTop: $(validationResult.fieldId).offset().top - 100
                }, 500, function () {
                    $(validationResult.fieldId).focus();
                });
            } else if (validationResult.tabToShow === '#linkToChangePassword') {
                $('#myPageTitle, #myPageGuide, .my-profile-info').hide();
                $('#modifyPasswordTitle, #modifyPasswordGuide, #modifyPasswordSection').show();

                $(validationResult.fieldId).focus();
            }
        }
    }







    /* 프로필 사진 변경 이벤트 핸들러 및 함수 */

    // 초기 사용자의 프로필 사진 경로 저장
    var initialImgSrc = $('.my-page-image-preview').attr('src');

    $('#member-prfl-img-edit').change(updateImagePreview);
    $('#member-prfl-file-remove').click(function () {
        resetImagePreview(initialImgSrc);
    });
    $('#prfl-img-form').on('submit', submitPrflImgFormValidation);

    // 이미지 미리보기 및 파일 정보 표시
    function updateImagePreview(event) {
        const file = event.target.files[0];
        const fileInfo = $('#member-prfl-file-info');

        if (!file) {
            $('.my-page-image-preview').attr('src', initialImgSrc);
            $('#member-prfl-img-edit').val("");
            fileInfo.html(`등록된 파일이 없습니다. 기존 이미지가 적용됩니다.`);
            return;
        }

        if (!file.type.match('image.*') || file.size > 5242880) {
            alert('이미지 파일만 업로드 가능합니다.');
            $('#member-prfl-img-edit').val("");
            fileInfo.html(`5mb이하의 이미지 파일만 등록 가능합니다. 다시 등록해주세요.`);
            return;
        }

        let fileSize = file.size / 1024; // KB 단위로 변환
        let fileSizeUnit = 'KB';
        if (fileSize > 1024) {
            fileSize = fileSize / 1024; // MB 단위로 변환
            fileSizeUnit = 'MB';
        }
        fileInfo.html(`파일이름 : ${file.name},파일용량 : ${fileSize.toFixed(2)} ${fileSizeUnit}`);

        const reader = new FileReader();
        reader.onload = function (e) {
            $('.my-page-image-preview').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
    }

    // 이미지 삭제 및 초기화
    function resetImagePreview(initialImgSrc) {
        $('.my-page-image-preview').attr('src', initialImgSrc);
        $('#member-prfl-img-edit').val("");
        $('#member-prfl-file-info').html(`등록된 파일이 없습니다. 기존 이미지가 적용됩니다.`);
    }

    // 폼 제출 시 모든 유효성 검사 확인하여 문제 발생 시 폼 제출 방지
    function submitPrflImgFormValidation(event) {
        if (true/*이미지파일항목에 관련된 조건*/) {
            event.preventDefault();
            if (true/*이미지파일항목에 관련된 조건*/) {
                scrollError('???');
                alert('전화번호 형식에 맞추어서\n입력해주시기 바랍니다.');
            }
        }
    }














    /* 개인정보 수정 이벤트 핸들러 및 함수 */

    // 이름, 성 유니코드 정규검사식
    const nameRegex = /^[A-Za-z\u00C0-\u00FF\u0100-\u017F\u0180-\u024F\u0370-\u03FF\u0400-\u04FF\u1E00-\u1EFF\u2C00-\u2C7F\u2D00-\u2D2F\u3000-\u303F\u3400-\u4DBF\u4E00-\u9FFF\uA000-\uA48F\uA490-\uA4CF\uAC00-\uD7AF\uF900-\uFAFF\uFE30-\uFE4F-'\s]+$/;

    var originalNickName = $('#nickName').val();

    $('#duplicateNickname').click(duplicateNickname);
    // $('#nickName').on('keyup, focus, input', validateNickname);

    $('#nickName').on('keyup, focus, input', function () {
        validateNickname();
        var currentNickName = $(this).val();

        if (originalNickName === currentNickName) {
            $('#isNickNameChecked').val('true');
            $('#duplicateNickname').hide();
            $('#nickname-duplication-check-message').text('✔️ 기존에 사용하던 닉네임입니다.').css('color', 'green');
        } else {
            $('#isNickNameChecked').val('false');
            $('#duplicateNickname').show();
        }
    });
    $('#nickName').on('keyup click', resetNicknameValidation);
    $('#phone').on('keyup input click', validatePhone);
    $('#firstName').on('keyup focus input click', validateFirstName);
    $('#lastName').on('keyup focus input click', validateLastName);
    $('#gender').on('keyup focus input click', validateGenderSelect);
    $('#gender').change(validateGenderSelect);
    $('#birth').on('keyup focus input click', validateBirthDate);
    $('#prfl-info-form').on('submit', submitPrflInfoFormValidation);

    // textarea 최대 허용길이 설정
    var textMaxLength = 500;

    // '.intro-text-box'의 현재 길이를 계산하고 초기 상태를 설정
    var currentLength = $('.intro-text-box').val().length;
    if (currentLength > 0) {
        $('.text-caculate-intro').show().text(currentLength + ' / ' + textMaxLength);
    } else {
        $('.text-caculate-intro').hide();
    }

    validateNickname();
    validatePhone();
    validateFirstName();
    validateLastName();
    validateGenderSelect();
    validateBirthDate();

    // 자기소개 텍스트 카운팅
    $('body').on('input', '.intro-text-box', function () {
        var currentLength = $(this).val().length;
        const textMaxLength = 500;
        if (currentLength > 0) {
            $('.text-caculate-intro').show().text(currentLength + ' / ' + textMaxLength);
        } else {
            $('.text-caculate-intro').hide();
        }

        if (currentLength > textMaxLength - 1) {
            alert("텍스트는 최대 " + textMaxLength + "자까지 입력 가능합니다.");
            $(this).val($(this).val().substr(0, textMaxLength));
        }
    });

    // 닉네임 중복확인 ajax
    function duplicateNickname() {
        const nickName = $('#nickName').val();
        if (!nickName) {
            alert('닉네임을 입력해주세요.');
            return;
        }
        if (originalNickName === nickName) {
            handleNicknameResponse(false);
            return;
        }
        if (validateNickname().isValid) {
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

    // 닉네임 중복확인 후 처리.
    function handleNicknameResponse(response) {
        if (response.isDuplicate) {
            alert('이미 사용 중인 닉네임입니다.');
            $('#nickname-duplication-check-message').text('❌ 이미 사용 중인 닉네임입니다.').css('color', 'red');
            return false;
        } else if (!response.isDuplicate) {
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
        validationResults.fieldId = '#phone';
        validationResults.tabToShow = '#linkToProfileInfo';
        const phoneInput = $('#phone');
        const message = $('#phone-validation-message');
        const phoneValue = phoneInput.val();

        // 국제 전화번호 패턴 검사 (한국 포함, 하이픈(-) 제거)
        const phonePattern = /^(?:(\+1|\+33|\+44|\+49|\+82|\+39|\+34|\+81|\+61|\+55|\+52|\+46|\+47|\+45|\+358|\+90|\+48|\+32|\+36|\+31|\+43|\+41|\+64)([1-9]\d{6,14}))$|^((02|0[3-9][0-9]?|070)([1-9]\d{6,7})|(01[016789])([1-9]\d{6,7}))$/;

        if (phoneValue.includes('-')) {
            validationResults.isValid = false;
            validationResults.validErrorMsg = '하이픈(-)을 제거하고 숫자만 입력해주세요.';
            message.text('❌ 하이픈(-)을 제거하고 숫자만 입력해주세요.').css('color', 'red');
            return validationResults;
        } else if (!phonePattern.test(phoneValue)) {
            validationResults.isValid = false;
            validationResults.validErrorMsg = '유효하지 않은 전화번호 형식입니다.';
            message.text('❌ 유효하지 않은 전화번호 형식입니다.').css('color', 'red');
            return validationResults;
        } else {
            validationResults.isValid = true;
            validationResults.validErrorMsg = '';
            message.text('✔️ 유효한 전화번호 형식입니다.').css('color', 'green');
            return validationResults;
        }
    }

    // 닉네임 입력란 변경 시 로직
    function resetNicknameValidation() {
        if ($('#isNickNameChecked').val() === 'true') {
            $('#isNickNameChecked').val('false');
            $('#duplicateNickname').val('중복확인');
            $('#nickName').attr('readonly', false);
            $('#duplicateNickname').attr('disabled', false);
            alert('⚠ 입력값 수정을 시도하셨습니다.\n기존에 사용하던 닉네임이 아닐 경우\n입력 후 꼭 중복확인 시도해주세요.');
        } else if ($('#nickName').attr('readonly', false)) {

        }
    }

    // 닉네임 유효성 검사 메서드
    function validateNickname() {
        validationResults.fieldId = '#nickName';
        validationResults.tabToShow = '#linkToProfileInfo';
        const nicknameInput = $('#nickName').val().trim();
        const nicknameValidationMessage = $('#nickname-duplication-check-message');

        // 길이 검사
        if (!(nicknameInput.length >= 2 && nicknameInput.length <= 10)) {
            validationResults.isValid = false;
            validationResults.validErrorMsg = '닉네임은 2~10글자 사이여야 합니다.';
            nicknameValidationMessage.html('❌ 2~10글자 사이여야 합니다.').css('color', 'red');
            return validationResults;
        }

        // 한글 또는 영문 대소문자 검사
        if (!/[가-힣a-zA-Z]+/.test(nicknameInput)) {
            validationResults.isValid = false;
            validationResults.validErrorMsg = '닉네임에는 한글 또는 영문 대소문자가 포함되어야 합니다.';
            nicknameValidationMessage.html('❌ 한글 또는 영문 대소문자가 포함되어야 합니다.').css('color', 'red');
            return validationResults;
        }

        // 유효하지 않은 문자 포함 검사
        if (/[^가-힣a-zA-Z0-9]/.test(nicknameInput)) {
            validationResults.isValid = false;
            validationResults.validErrorMsg = '닉네임에 유효하지 않은 문자가 포함되어 있습니다.';
            nicknameValidationMessage.html('❌ 유효하지 않은 문자가 있습니다.').css('color', 'red');
            return validationResults;
        } else {
            validationResults.isValid = true;
            validationResults.validErrorMsg = '';
            nicknameValidationMessage.html('✔️ 사용 가능한 닉네임입니다.').css('color', 'green');
            return validationResults;
        }
    }


    // 이름 유효성 검사
    function validateFirstName() {
        validationResults.fieldId = '#firstName';
        validationResults.tabToShow = '#linkToProfileInfo';
        const firstName = $('#firstName').val().trim();
        const firstNameMessage = $('#firstname-validation-message');

        if (firstName == null || firstName == '') {
            validationResults.isValid = false;
            validationResults.validErrorMsg = '이름을 입력해주세요.';
            firstNameMessage.text('❌ 이름을 입력해주세요.').css('color', 'red');
            return validationResults;
        }
        if (!nameRegex.test(firstName)) {
            validationResults.isValid = false;
            validationResults.validErrorMsg = '이름 : 잘못된 입력입니다.';
            firstNameMessage.text('❌ 잘못된 입력입니다.').css('color', 'red');
            return validationResults;
        } else {
            validationResults.isValid = true;
            validationResults.validErrorMsg = '';
            firstNameMessage.text('✔️');
            return validationResults;
        }

    }

    // 성 유효성 검사
    function validateLastName() {
        validationResults.fieldId = '#lastName';
        validationResults.tabToShow = '#linkToProfileInfo';
        const lastName = $('#lastName').val().trim();
        const lastNameMessage = $('#lastname-validation-message');

        if (lastName == null || lastName == '') {
            validationResults.isValid = false;
            validationResults.validErrorMsg = '성을 입력해주세요.';
            lastNameMessage.text('❌ 성을 입력해주세요.').css('color', 'red');
            return validationResults;
        }
        if (!nameRegex.test(lastName)) {
            validationResults.isValid = false;
            validationResults.validErrorMsg = '성 : 잘못된 입력입니다.';
            lastNameMessage.text('❌ 잘못된 입력입니다.').css('color', 'red');
            return validationResults;
        } else {
            validationResults.isValid = true;
            validationResults.validErrorMsg = '';
            lastNameMessage.text('✔️');
            return validationResults;
        }
    }

    // 성별 유효성검사
    function validateGenderSelect() {
        validationResults.fieldId = '#gender';
        validationResults.tabToShow = '#linkToProfileInfo';
        const genderSelect = $('#gender');
        const selectedValue = genderSelect.val();
        const genderValidationMessage = $('#gender-validation-message');

        if (selectedValue === null || selectedValue === '') {
            validationResults.isValid = true;
            validationResults.validErrorMsg = '';
            genderValidationMessage.text('✔️');
            return validationResults;
        }

        if (selectedValue !== 'true' && selectedValue !== 'false' && selectedValue !== '' && selectedValue !== null) {
            validationResults.isValid = false;
            validationResults.validErrorMsg = '성별 입력이 잘못되었습니다.\n성별을 다시 선택해주세요.';
            genderValidationMessage.text('❌ 유효하지 않은 성별값입니다.').css('color', 'red');
            genderSelect.empty(); // 기존의 option들을 모두 제거
            genderSelect.append($('<option>', {
                value: '',
                text: '성별을 선택하세요',
                selected: true
            }));
            genderSelect.append($('<option>', { value: 'true', text: '남성' }));
            genderSelect.append($('<option>', { value: 'false', text: '여성' }));
            return validationResults;
        } else {
            validationResults.isValid = true;
            validationResults.validErrorMsg = '';
            genderValidationMessage.text('✔️').css('color', 'green');
            return validationResults;
        }
    }

    // 생년월일 유효성 검사
    function validateBirthDate() {
        validationResults.fieldId = '#birth';
        validationResults.tabToShow = '#linkToProfileInfo';
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
            validationResults.isValid = true;
            validationResults.validErrorMsg = '';
            inputDateError.text('');
            return validationResults; // 입력하지 않은 경우에도 true를 반환
        }

        if (!datePattern.test(inputDate)) {
            validationResults.isValid = false;
            validationResults.validErrorMsg = '올바른 날짜 형식을 입력해주세요.';
            inputDateError.text('❌ 올바른 날짜 형식을 입력해주세요.').css('color', 'red');
            return validationResults;
        }

        const [year, month, day] = inputDate.split('-').map(Number);
        const inputDateObj = new Date(year, month - 1, day);

        const isDateValid = inputDateObj.getFullYear() === year &&
            inputDateObj.getMonth() + 1 === month &&
            inputDateObj.getDate() === day;

        if (!isDateValid) {
            validationResults.isValid = false;
            validationResults.validErrorMsg = '존재하지 않는 날짜입니다.';
            inputDateError.text('❌ 존재하지 않는 날짜입니다.').css('color', 'red');
            return validationResults;
        }

        // 입력된 생년월일이 유효한 연도 범위 내에 있는지 검사
        if (year < minYear || year > maxYear) {
            validationResults.isValid = false;
            validationResults.validErrorMsg = '년도는 ' + minYear + '년부터 ' + maxYear + '년 사이여야 합니다.';
            inputDateError.text(`❌ 년도는 ${minYear}년부터 ${maxYear}년 사이여야 합니다.`).css('color', 'red');
            return validationResults;
        }

        // 입력 날짜가 오늘 날짜를 초과하는지 검사
        if (inputDateObj > today) {
            validationResults.isValid = false;
            validationResults.validErrorMsg = '생년월일은 오늘 날짜를 초과할 수 없습니다.';
            inputDateError.text('❌ 생년월일은 오늘 날짜를 초과할 수 없습니다.').css('color', 'red');
            return validationResults;
        }

        // 모든 검사를 통과한 경우
        validationResults.isValid = true;
        validationResults.validErrorMsg = '';
        inputDateError.text('✔️').css('color', 'green');
        return validationResults;
    }

    // 폼 제출 시 모든 유효성 검사 확인하여 문제 발생 시 폼 제출 방지
    function submitPrflInfoFormValidation(event) {
        event.preventDefault();
        if (!validatePhone().isValid
            || !validateNickname().isValid
            || !validateFirstName().isValid
            || !validateLastName().isValid
            || !validateGenderSelect().isValid
            || !validateBirthDate().isValid
            || $('#isNickNameChecked').val() !== 'true') {
            event.preventDefault();
            if ($('#isNickNameChecked').val() !== 'true') {
                validationResults.fieldId = '#nickName';
                validationResults.tabToShow = '#linkToProfileInfo';
                validationResults.isValid = false;
                validationResults.validErrorMsg = '닉네임 중복 확인을 해주세요.';
                showErrorAndScrollToField(validationResults);
            } else {
                showErrorAndScrollToField(validationResults);
            }
        }

        // 폼 데이터 수집
        var formData = new FormData(this);

        // AJAX 요청
        $.ajax({
            url: '/member/MypageController/processUpdateProfile',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    window.location.reload();
                } else {
                    var errorMessages = Object.values(response.errors).join('\n');
                    alert(errorMessages);
                }
            },
            error: function (xhr, status, error) {
                alert('회원정보 수정에 실패했습니다. 다시 시도해주세요.');
            }
        });
    }







    /* 비밀번호 변경 이벤트 핸들러 및 함수 */

    $('#newpassword, #newpasswordcf').on('keyup', validatePasswordAndMatch);
    $('#prfl-password-form').on('submit', processModifyPassword);

    // 인증된 사용자 비밀번호 변경 ajax
    function processModifyPassword(event) {
        event.preventDefault(); // 폼 기본 제출 방지

        var memberId = $('#modifiedPasswordMemberId').val();
        const oldpassword = $('#oldpassword').val();
        const newpassword = $('#newpassword').val();
        const newpasswordcf = $('#newpasswordcf').val();

        // 유효성 검사
        if (!validatePassword().isValid || !checkPasswordMatch().isValid) {
            showErrorAndScrollToField(validationResults);
            return; // 유효성 검사 실패 시 함수 종료
        }

        if (!oldpassword || !newpassword || !newpasswordcf) {
            alert('입력되지 않은 값이 있습니다.');
            return; // 입력 누락 시 함수 종료
        }

        // Ajax 요청
        $.ajax({
            url: '/member/MypageController/processModifyPassword',
            type: 'POST',
            dataType: 'json',
            data: {
                memberId: memberId,
                oldpassword: oldpassword,
                newpassword: newpassword,
                newpasswordcf: newpasswordcf
            },
            success: function (response) {
                if (response.success) {
                    alert('비밀번호가 성공적으로 변경되었습니다.');
                    window.location.href = '/member/MypageController/modifyPasswordDone'; // 성공 페이지로 리디렉션
                } else {
                    var errorMessages = Object.values(response.errors).join('\n');
                    alert('비밀번호 변경에 실패했습니다: \n' + errorMessages);
                }
            },
            error: function (xhr, status, error) {
                alert('비밀번호 변경 처리 중 오류가 발생했습니다: ' + error);
            }
        });
    }

    // 비밀번호 유효성 검사 및 일치 확인
    function validatePasswordAndMatch() {
        validatePassword();
        checkPasswordMatch();
    }

    // 비밀번호 유효성 검사 keyup
    function validatePassword() {
        validationResults.fieldId = '#newpassword';
        validationResults.tabToShow = '#linkToChangePassword';
        const newpassword = $('#newpassword').val();
        const newpasswordValidationMessage = $('#newpassword-validation-message');
        const newpasswordSpaceValidationMessage = $('#newpassword-space-validation-message');

        // 조건 검사
        const hasLetter = /[a-zA-Z]/.test(newpassword);
        const hasDigit = /\d/.test(newpassword);
        const hasSpecialChar = /[!@#$%^&*()\-_=+\[\]{}|;:'",<.>/?]/.test(newpassword);
        const isLongEnough = newpassword.length >= 8;
        const hasInvalidChar = /[^a-zA-Z0-9!@#$%^&*()\-_=+\[\]{}|;:'",<.>/?]/.test(newpassword);

        // 상세한 조건 불충족 메시지 초기화
        let message = '';
        let spaceMessage = '';

        // 조건별 메시지 추가
        message += hasLetter ? '✔️ 영문, ' : '❌ 영문, ';
        message += hasDigit ? '✔️ 숫자, ' : '❌ 숫자, ';
        message += hasSpecialChar ? '✔️ 특수문자, ' : '❌ 특수문자, ';
        message += isLongEnough ? '✔️ 8자 이상 ' : '❌ 8자 이상 ';
        spaceMessage += !hasInvalidChar ? '' : '❌ 유효하지 않은 문자 포함<br>(공백, 허용되지 않는 특수문자 등)';

        message = message.trim().replace(/, $/, '');
        newpasswordValidationMessage.html(message).css('color', hasLetter && hasDigit && hasSpecialChar && isLongEnough ? 'green' : 'red');
        newpasswordSpaceValidationMessage.html(spaceMessage).css('color',!hasInvalidChar ? 'green' : 'red');

        if (hasLetter && hasDigit && hasSpecialChar && isLongEnough && !hasInvalidChar) {
            validationResults.isValid = true;
            validationResults.validErrorMsg = '';
            return validationResults;
        } else {
            validationResults.isValid = false;
            if (!hasLetter) {
                validationResults.validErrorMsg = '비밀번호에 영문이 포함되지 않았습니다.';
                return validationResults;
            } else if (!hasDigit) {
                validationResults.validErrorMsg = '비밀번호에 숫자가 포함되지 않았습니다.';
                return validationResults;
            } else if (!hasSpecialChar) {
                validationResults.validErrorMsg = '비밀번호에 특수문자가 포함되지 않았습니다.';
                return validationResults;
            } else if (!isLongEnough) {
                validationResults.validErrorMsg = '비밀번호 길이가 8글자 이상이어야 합니다.';
                return validationResults;
            } else if (hasInvalidChar) {
                validationResults.validErrorMsg = '비밀번호에 유효하지 않은 문자가 포함되어있습니다.\n(공백, 허용되지 않는 특수문자 등)';
                return validationResults;
            }
        }
    }

    // 비밀번호 일치 확인 keyup
    function checkPasswordMatch() {
        validationResults.fieldId = '#newpasswordcf';
        validationResults.tabToShow = '#linkToChangePassword';
        const newpassword = $('#newpassword').val();
        const newpasswordcf = $('#newpasswordcf').val();
        const newpasswordcfValidationMessage = $('#newpasswordcf-validation-message');

        if (validatePassword()) {
            validationResults.fieldId = '#newpasswordcf';
            if (newpassword === '' && newpasswordcf === '') {
                validationResults.isValid = true;
                validationResults.validErrorMsg = '';
                newpasswordcfValidationMessage.text('');
                return validationResults;
            } else if (newpassword === '') {
                validationResults.isValid = false;
                validationResults.validErrorMsg = '사용할 비밀번호가 입력되지 않았습니다.';
                newpasswordcfValidationMessage.text('❌ 사용할 비밀번호가 입력되지 않았습니다.').css('color', 'red');
                return validationResults;
            } else if (newpasswordcf === '') {
                validationResults.isValid = false;
                validationResults.validErrorMsg = '비밀번호가 입력되지 않았습니다.';
                newpasswordcfValidationMessage.text('❌ 비밀번호가 입력되지 않았습니다.').css('color', 'red');
                return validationResults;
            } else if (newpassword !== newpasswordcf) {
                validationResults.isValid = false;
                validationResults.validErrorMsg = '비밀번호가 일치하지 않습니다.';
                newpasswordcfValidationMessage.text('❌ 비밀번호 불일치').css('color', 'red');
                return validationResults;
            } else {
                validationResults.isValid = true;
                validationResults.validErrorMsg = '';
                newpasswordcfValidationMessage.text('✔️ 비밀번호 일치').css('color', 'green');
                return validationResults;
            }
        } else {
            validationResults.isValid = false;
            validationResults.validErrorMsg = '비밀번호 패턴을 만족하지 못합니다..';
            newpasswordcfValidationMessage.text('❌ 비밀번호 패턴 불만족').css('color', 'red');
            return validationResults;
        }
    }



    // 폼 제출 시 모든 유효성 검사 확인하여 문제 발생 시 폼 제출 방지
    function submitPrflPasswordFormValidation(event) {
        if (!validatePassword().isValid || !checkPasswordMatch().isValid) {
            event.preventDefault();
            showErrorAndScrollToField(validationResults);
        }
    }

});