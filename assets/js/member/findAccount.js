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

    // 이름, 성 유니코드 정규검사식
    const nameRegex = /^[A-Za-z\u00C0-\u00FF\u0100-\u017F\u0180-\u024F\u0370-\u03FF\u0400-\u04FF\u1E00-\u1EFF\u2C00-\u2C7F\u2D00-\u2D2F\u3000-\u303F\u3400-\u4DBF\u4E00-\u9FFF\uA000-\uA48F\uA490-\uA4CF\uAC00-\uD7AF\uF900-\uFAFF\uFE30-\uFE4F-'\s]+$/;

    $('#firstNameM').on('keyup', validateFirstNameM);
    $('#lastNameM').on('keyup', validateLastNameM);
    $('#phoneM').on('keyup', validatePhoneM);

    $('#userNameP').on('keyup', validateEmailP);
    $('#firstNameP').on('keyup', validateFirstNameP);
    $('#lastNameP').on('keyup', validateLastNameP);
    $('#phoneP').on('keyup', validatePhoneP);

    $('#findPW').on('submit', submitFormValidationP);
    $('#findEmail').on('submit', submitFormValidationM);

    // 이름 유효성 검사(Email찾기)
    function validateFirstNameM() {
        const firstName = $('#firstNameM').val().trim();
        const firstNameMessage = $('#m-firstname-validation-message');

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

    // 성 유효성 검사(Email찾기)
    function validateLastNameM() {
        const lastName = $('#lastNameM').val().trim();
        const lastNameMessage = $('#m-lastname-validation-message');

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

    // 연락처 유효성 검사(Email찾기)
    function validatePhoneM() {
        const phoneInput = $('#phoneM');
        const message = $('#m-phone-validation-message');
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


    // 이메일 유효성 검사(PW찾기)
    function validateEmailP() {
        const emailInput = $('#userNameP');
        const email = emailInput.val();
        const emailValidationMessage = $('#p-email-validation-message');
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

    // 이름 유효성 검사(PW찾기)
    function validateFirstNameP() {
        const firstName = $('#firstNameP').val().trim();
        const firstNameMessage = $('#p-firstname-validation-message');

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

    // 성 유효성 검사(PW찾기)
    function validateLastNameP() {
        const lastName = $('#lastNameP').val().trim();
        const lastNameMessage = $('#p-lastname-validation-message');

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

    // 연락처 유효성 검사(PW)
    function validatePhoneP() {
        const phoneInput = $('#phoneP');
        const message = $('#p-phone-validation-message');
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

    // P 폼 유효성 검사 및 제출
    function submitFormValidationP(event) {
        if (!validateEmailP() || !validatePhoneP() || !validateFirstNameP() || !validateLastNameP()) {
            event.preventDefault();
            if (!validateEmailP()) {
                scrollError('userNameP');
                alert('올바른 이메일 형식이 아닙니다.');
            }
            if (!validatePhoneP()) {
                scrollError('phoneP');
                alert('전화번호 형식에 맞추어서\n입력해주시기 바랍니다.');
            }
            if (!validateFirstNameP()) {
                scrollError('firstNameP');
                alert('이름(First name / Given name) 입력이 잘못되었습니다.');
            }
            if (!validateLastNameP()) {
                scrollError('lastNameP');
                alert('성(Last name / Family name) 입력이 잘못되었습니다.');
            }
        }
    }

    // M 폼 유효성 검사 및 제출
    function submitFormValidationM(event) {
        if (!validatePhoneM() || !validateFirstNameM() || !validateLastNameM()) {
            event.preventDefault();
            if (!validatePhoneM()) {
                scrollError('phoneM');
                alert('전화번호 형식에 맞추어서\n입력해주시기 바랍니다.');
            }
            if (!validateFirstNameM()) {
                scrollError('firstNameM');
                alert('이름(First name / Given name) 입력이 잘못되었습니다.');
            }
            if (!validateLastNameM()) {
                scrollError('lastNameM');
                alert('성(Last name / Family name) 입력이 잘못되었습니다.');
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
