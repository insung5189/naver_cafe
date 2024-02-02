<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>회원가입</title>
    <link rel="stylesheet" type="text/css" href="/assets/css/member/signup.css">
    <script src="/assets/js/member/signup.js"></script>
    <script src="/assets/js/member/address.js"></script>
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
                    <div class="text-xs">카페 가입을 위한 정보를 입력해주세요.</div>
                    <span class="required-field">*</span><span class="text-xs">는 필수 입력사항 입니다.</span>
                </div>
                <form method="POST" action="/member/SignupController/processMemberSignup" enctype="multipart/form-data">
                    <!-- 사용자 아이디 (이메일) -->
                    <div class="form-control">
                        <hr>
                        <div class="flex-wrap">

                            <div class="label-gap">

                                <label class="label" for="userName">
                                    <span class="label-text">사용자 아이디</span>
                                    <span class="description">(Email)</span>
                                    <span class="required-field">*</span>
                                </label>

                            </div>

                            <input autofocus class="custom-input" id="userName" maxlength="50" placeholder="아이디" required name="userName" type="email">
                            
                            <div class="text-xs" id="email-validation-message"></div>
                        </div>
                        <hr>
                        <div class="flex-wrap">
                        <div class="label-gap">
                                        <label class="label" for="">
                                            <span class="label-text">프로필 이미지</span>
                                            <span class="description">(최대50MB)</span>
                                        </label>
                                    </div>
                            <!-- 프로필 이미지 업로드 -->
                            <div class="user-profile-register">

                                    <label for="file" class="img-file-lable"></label>
                                    <div class="img-wrap">
                                        <img class="image-preview" src="https://i.imgur.com/0Vhk4jx.png" alt="Preview" />

                                        <div class="file-btn-wrap">
                                            <button type="button" id="upload-image" class="custom-btn">등록</button>
                                            <input style="display: none;" type="file" accept="image/jpeg, image/png, image/bmp, image/jpg" id="file" name="file">
                                            <button type="button" id="remove-image" class="custom-btn">삭제</button>
                                        </div>

                                        <div id="file-info" class="text-small"></div>

                                    </div>

                            </div>
                        </div>
                        <hr>
                    </div>


                    <div class="form-control">
                        <label class="label" for="password1">
                            <span class="label-text">비밀번호</span>
                        </label>
                        <input class="custom-input" id="password1" maxlength="50" placeholder="비밀번호" required name="password1" type="password">
                        <div class="validate-message text-xs" id="password-validation-message">영문, 숫자, 특수문자를 포함한 8자 이상</div>
                    </div>

                    <div class="form-control">
                        <label class="label" for="password2">
                            <span class="label-text">비밀번호 확인</span>
                        </label>
                        <input class="custom-input" id="password2" maxlength="50" placeholder="비밀번호 확인" required name="password2" type="password">
                        <div class="validate-message text-xs" id="password-match-message"></div>
                    </div>

                    <div class="form-control">
                        <label class="label" for="phone">
                            <span class="label-text">연락처</span>
                        </label>
                        <input class="custom-input" id="phone" placeholder="연락처" required name="phone" type="number">
                    </div>

                    <div class="form-control">
                        <label class="label" for="nickName">
                            <span class="label-text">닉네임</span>
                        </label>
                        <input class="custom-input" id="nickName" maxlength="50" placeholder="닉네임" required name="nickName" type="text">
                    </div>


                    <!-- 이름 -->
                    <div class="form-control">
                        <label class="label" for="firstName">
                            <span class="label-text">이름(First name / Given name)</span>
                        </label>
                        <input class="custom-input" id="firstName" placeholder="ex) 길동" required name="firstName" type="text">
                    </div>

                    <!-- 성 -->
                    <div class="form-control">
                        <label class="label" for="lastName">
                            <span class="label-text">성(Last name / Family name)</span>
                        </label>
                        <input class="custom-input" id="lastName" placeholder="ex) 홍" required name="lastName" type="text">
                    </div>

                    <!-- 성별 -->
                    <div class="form-control">
                        <label class="label" for="gender">
                            <span class="label-text">성별</span>
                        </label>
                        <select id="gender" name="gender">
                            <option value="false" disabled selected>성별</option>
                            <option value="true">남성</option>
                            <option value="true">여성</option>
                        </select>
                    </div>

                    <!-- 생년월일 -->
                    <div class="form-control">
                        <label class="label" for="birthDate">
                            <span class="label-text">생년월일</span>
                        </label>
                        <input class="custom-input" id="birth" pattern="\d{4}-\d{2}-\d{2}" name="birth" type="date">
                        <span class="hid-ast" id="birthDateError" style='color: #E50122; visibility: hidden; font-size:12px'>올바른 날짜 형식을 입력해주세요. (예: 1993-11-03)</span>
                    </div>

                    <div class="form-control">
                        <label class="label" for="sample4_postcode">
                            <span class="label-text">우편번호</span>
                        </label>
                        <div class="input-wrap">
                            <input class="custom-input" id="sample4_postcode" placeholder="우편번호" name="postalNum" type="text">
                            <input onclick="sample4_execDaumPostcode()" type="button" value="우편번호 찾기" class="postal-num-btn btn btn-secondary btn-outline">
                        </div>
                    </div>
                    <div class="form-control">
                        <label class="label" for="sample4_roadAddress">
                            <span class="label-text">도로명</span>
                        </label>
                        <input class="custom-input" id="sample4_roadAddress" placeholder="도로명" name="roadAddress" type="text">
                    </div>
                    <div class="form-control">
                        <label class="label" for="sample4_jibunAddress">
                            <span class="label-text">지번주소</span>
                        </label>
                        <input class="custom-input" id="sample4_jibunAddress" placeholder="지번주소" name="jibunAddress" type="text">
                    </div>
                    <span id="guide" style="color:#999;display:none"></span>
                    <div class="form-control">
                        <label class="label" for="sample4_extraAddress">
                            <span class="label-text">참고항목</span>
                        </label>
                        <input class="custom-input" id="sample4_extraAddress" placeholder="참고항목" name="extraAddress" type="text">
                    </div>
                    <div class="form-control">
                        <label class="label" for="sample4_detailAddress">
                            <span class="label-text">상세주소</span>
                        </label>
                        <input class="custom-input" id="sample4_detailAddress" placeholder="상세주소" name="detailAddress" type="text">
                    </div>

                    <div class="form-group">
                        <a class="" href="/">취소</a>
                        <input type="submit" value="회원가입">
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>

</html>