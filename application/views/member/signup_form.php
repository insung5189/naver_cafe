<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>회원가입</title>
    <link rel="stylesheet" type="text/css" href="/assets/css/member/signup.css">
    <script src="/assets/js/member/signup.js"></script>
</head>

<body>
    <main>
        <section>
            <div class="container">
                <h1 class="page-title">
                    <i class="fa-solid fa-user-plus"></i>
                    카페 가입하기
                </h1>
                <div>
                    <div class="page-guide">카페 가입을 위한 정보를 입력해주세요.</div>
                    <span class="required-field">*</span><span class="page-guide"> 는 필수 입력사항 입니다.</span>
                </div>
                <form method="POST" action="/member/SignupController/processMemberSignup" enctype="multipart/form-data">
                    <!-- 사용자 아이디 (이메일) -->
                    <div class="form-box">
                        <div class="feild-box">

                            <div class="label-box">
                                <label class="label-section" for="userName">
                                    <span class="label-text">카페 설명</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <span>비드코칭연구소 제품완성팀 미션 카페 입니다.</span>
                            </div>

                        </div>
                        <hr>
                        <div class="feild-box">

                            <div class="label-box">
                                <label class="label-section" for="userName">
                                    <span class="label-text">사용자 아이디</span>
                                    <span class="description">(Email)</span>
                                    <span class="required-field">*</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input autofocus class="custom-input" id="userName" maxlength="50" placeholder="ex) example@email.com" required name="userName" type="email">
                                <input id="duplicateEmail" type="button" value="중복확인" class="btn">
                                <span class="description pl-5" id="email-duplication-check-message"></span>
                                <span class="description pl-5" id="email-validation-message"></span>
                            </div>

                        </div>
                        <hr>
                        <div class="feild-box">

                            <div class="label-box">
                                <label class="label-section" for="">
                                    <span class="label-text">프로필 이미지</span>
                                    <span class="description">(최대50MB)</span>
                                </label>
                            </div>

                            <!-- 프로필 이미지 업로드 -->
                            <div class="input-box">
                                <label for="file" class="label-section"></label>
                                <div class="img-wrap">
                                    <img class="image-preview" src="https://i.imgur.com/0Vhk4jx.png" alt="Preview" />
                                    <div class="file-btn-wrap">
                                        <button type="button" id="upload-image" class="img-btn">등록</button>
                                        <input style="display: none;" type="file" accept="image/jpeg, image/png, image/bmp, image/jpg" id="file" name="file">
                                        <button type="button" id="remove-image" class="img-btn">삭제</button>
                                    </div>
                                    <div id="file-info">등록된 파일이 없습니다. <br>기본 이미지가 적용됩니다.</div>
                                </div>
                            </div>

                        </div>
                        <hr>
                        <div class="feild-box">

                            <div class="label-box">
                                <label class="label-section" for="password1">
                                    <span class="label-text">비밀번호</span>
                                    <span class="required-field">*</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input class="custom-input" id="password1" maxlength="50" placeholder="비밀번호를 입력해주세요." required name="password1" type="password">
                                <span class="description pl-5" id="password-validation-message"> * 영문, 숫자, 특수문자를 포함한 8자 이상</span>
                            </div>

                        </div>
                        <hr>
                        <div class="feild-box">

                            <div class="label-box">
                                <label class="label-section" for="password2">
                                    <span class="label-text">비밀번호 확인</span>
                                    <span class="required-field">*</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input class="custom-input" id="password2" maxlength="50" placeholder="비밀번호를 한 번 더 입력해주세요." required name="password2" type="password">
                                <span class="description pl-5" id="password-match-message"></span>
                            </div>

                        </div>
                        <hr>
                        <div class="feild-box">

                            <div class="label-box">
                                <label class="label-section" for="phone">
                                    <span class="label-text">연락처</span>
                                    <span class="description">(-제외)</span>
                                    <span class="required-field">*</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input class="custom-input" id="phone" placeholder="ex) 01012345678" required name="phone" type="number">
                                <span class="description pl-5" id="phone-validation-message"></span>
                            </div>

                        </div>
                        <hr>
                        <div class="feild-box">

                            <div class="label-box">
                                <label class="label-section" for="nickName">
                                    <span class="label-text">닉네임</span>
                                    <span class="required-field">*</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input class="custom-input" id="nickName" maxlength="50" placeholder="닉네임" required name="nickName" type="text">
                                <input id="duplicateNickname" type="button" value="중복확인" class="btn">
                                <span class="description pl-5" id="nickname-duplication-check-message"></span>
                            </div>

                        </div>
                        <hr>
                        <!-- 이름 -->
                        <div class="feild-box">

                            <div class="label-box">
                                <label class="label-section" for="firstName">
                                    <span class="label-text">이름</span>
                                    <span class="description">(First name / Given name)</span>
                                    <span class="required-field">*</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input class="custom-input" id="firstName" placeholder="ex) 길동" required name="firstName" type="text">
                            </div>

                        </div>
                        <hr>
                        <!-- 성 -->
                        <div class="feild-box">

                            <div class="label-box">
                                <label class="label-section" for="lastName">
                                    <span class="label-text">성</span>
                                    <span class="description">(Last name / Family name)</span>
                                    <span class="required-field">*</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input class="custom-input" id="lastName" placeholder="ex) 홍" required name="lastName" type="text">
                            </div>

                        </div>
                        <hr>
                        <!-- 성별 -->
                        <div class="feild-box">

                            <div class="label-box">
                                <label class="label-section" for="gender">
                                    <span class="label-text">성별</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <select id="gender" name="gender">
                                    <option value="" disabled selected>성별</option>
                                    <option value="true">남성</option>
                                    <option value="false">여성</option>
                                </select>
                            </div>

                        </div>
                        <hr>
                        <!-- 생년월일 -->
                        <div class="feild-box">

                            <div class="label-box">
                                <label class="label-section" for="birthDate">
                                    <span class="label-text">생년월일</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input class="custom-input" id="birth" pattern="\d{4}-\d{2}-\d{2}" name="birth" type="date">
                                <span class="" id="birthDateError" style='color: #E50122; visibility: hidden; font-size:12px'>올바른 날짜 형식을 입력해주세요. (예: 1993-11-03)</span>
                            </div>

                        </div>
                        <hr>

                        <div class="feild-box adrs-box">

                            <div class="label-box">
                                <label class="label-section" for="sample4_postcode">
                                    <span class="label-text">우편번호</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input class="custom-input" id="sample4_postcode" placeholder="우편번호" name="postalNum" type="text">
                                <input onclick="sample4_execDaumPostcode()" type="button" value="우편번호 찾기" class="btn">
                            </div>

                        </div>

                        <div class="feild-box adrs-box">

                            <div class="label-box">
                                <label class="label-section" for="sample4_roadAddress">
                                    <span class="label-text">도로명</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input class="custom-input" id="sample4_roadAddress" placeholder="도로명" name="roadAddress" type="text">
                            </div>

                        </div>

                        <div class="feild-box adrs-box">

                            <div class="label-box">
                                <label class="label-section" for="sample4_jibunAddress">
                                    <span class="label-text">지번주소</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input class="custom-input" id="sample4_jibunAddress" placeholder="지번주소" name="jibunAddress" type="text">
                                <span id="guide" style="color:#999;display:none"></span>
                            </div>

                        </div>


                        <div class="feild-box adrs-box">

                            <div class="label-box">
                                <label class="label-section" for="sample4_extraAddress">
                                    <span class="label-text">참고항목</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input class="custom-input" id="sample4_extraAddress" placeholder="참고항목" name="extraAddress" type="text">
                            </div>

                        </div>

                        <div class="feild-box">

                            <div class="label-box">
                                <label class="label-section" for="sample4_detailAddress">
                                    <span class="label-text">상세주소</span>
                                </label>
                            </div>

                            <div class="input-box">
                                <input class="custom-input" id="sample4_detailAddress" placeholder="상세주소" name="detailAddress" type="text">
                            </div>

                        </div>

                        <hr>
                        <div>
                            <ul>
                                <li>
                                    <span class="bold h3">이 카페에서 활동하는 동안 원활한 카페 운영을 위하여&nbsp;</span>
                                    <span class="remov-space">Email, 닉네임, 활동내역, 이름/성, 연락처가&nbsp;</span>
                                    <span class="bold h3">이 카페의 운영진</span>
                                    <span class="remov-space">에게 공개되며,</span>
                                    <span class="bold h3">계정정보는 카페 탈퇴 후에도 3개월 동안 보관</span>
                                    <span class="remov-space">됩니다. 본 동의를 거부하실 수 있으나, 카페 가입이 불가능합니다.</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="">
                        <a class="" href="/">취소</a>
                        <input type="submit" value="회원가입">
                    </div>

                </form>
            </div>
        </section>
    </main>
</body>

</html>