<?
$GLOBALS['pageResources'] = [
    'css' => ['/assets/css/member/myPage.css'],
    'js' => ['/assets/js/member/myPage.js']
];
?>

<section class="section-container">
    <div class="container">
        <h1 class="title" id="myPageTitle">
            <i class="fa-solid fa-user"></i>
            마이페이지
        </h1>
        <div class="title" id="myPageGuide">
            <p class="page-guide">회원정보를 조회 및 수정할 수 있습니다.</p>
        </div>

        <h1 class="title" id="modifyPasswordTitle" style="display:none;">
            <i class="fa-solid fa-arrows-rotate"></i>
            비밀번호 변경
        </h1>
        <div class="title" id="modifyPasswordGuide" style="display:none;">
            <p class="page-guide">비밀번호를 변경합니다. 영문, 숫자, 특수문자 포함 8자리 이상으로 구성해주세요.</p>
        </div>

        <? if (isset($_SESSION['user_data'])) : ?>
            <? $user = $_SESSION['user_data']; ?>
            <div class="prfl-box">
                <span class="profile-img">

                    <?
                    $fileUrl = '/assets/file/images/memberImgs/';
                    $profileImagePath = ($member->getMemberFileName() === 'default.png') ? 'defaultImg/default.png' : $member->getMemberFileName();
                    ?>
                    <img class="my-page-image-preview" src="<?= $fileUrl . $profileImagePath; ?>" alt="프로필사진">

                    <div data-member-img-src="<?= $fileUrl . $profileImagePath; ?>"></div>
                    <form id="prfl-img-form" action="">
                        <!-- <label class="prfl-img-edit" for="member-prfl-img-edit">
                            <input type="file" name="member-prfl-img" id="member-prfl-img-edit" accept="image/jpg, image/jpeg, image/png, image/bmp, image/webp, image/gif" hidden>
                        </label> -->
                    </form>
                </span>
                <span class="nick-and-info">
                    <div class="nick-area">
                        <?= $member->getNickName(); ?>
                        (
                        <?
                        $email = $member->getUserName();
                        list($localPart, $domainPart) = explode('@', $email);
                        $maskedLocalPart = substr($localPart, 0, 3) . str_repeat('*', strlen($localPart) - 3);
                        $domainParts = explode('.', $domainPart);
                        $domainFirstPart = $domainParts[0];
                        $maskedDomainFirstPart = $domainFirstPart[0] . str_repeat('*', strlen($domainFirstPart) - 1);
                        $maskedDomainPart = $maskedDomainFirstPart . '.' . implode('.', array_slice($domainParts, 1));
                        $maskedEmail = $maskedLocalPart . '@' . $maskedDomainPart;
                        ?>
                        <?= $maskedEmail ?>
                        )
                    </div>
                    <div class="info-area">
                        <span>방문</span> <em class="count-num"><?= $member->getVisit(); ?></em> <span class="ml-17">작성글</span> <em class="count-num"><?= $articleCount; ?></em>
                    </div>
                    <div id="member-prfl-file-info"></div>
                    <!-- <a href="javascript:void(0);" id="member-prfl-file-remove">이미지 삭제</a> -->
                </span>

            </div>

            <? if (!empty($errors)) : ?>
                <div class="error-messages mb-0">
                    <p class="error-alert"><strong>⚠️ 문제가 발생했습니다!</strong></p>
                    <? foreach ($errors as $field => $error) : ?>
                        <p class="error-alert"><strong><?= htmlspecialchars($field) ?>:</strong> <?= htmlspecialchars($error) ?></p>
                    <? endforeach; ?>
                </div>
            <? endif; ?>

            <div class="content-box">
                <div class="list-style">
                    <div class="sort_area">
                        <a href="javascript:void(0);" class="link_sort on underline" id="linkToProfileInfo"><span>내 정보 조회 / 수정</span></a>
                        <a href="javascript:void(0);" class="link_sort" id="linkToChangePassword"><span>비밀번호 변경</span></a>
                    </div>
                </div>
                <!-- 내 정보 조회 / 수정 탭 내용 -->
                <div class="my-profile-info">
                    <form id="prfl-info-form" method="POST" action="/member/mypagecontroller/processUpdateProfile" enctype="multipart/form-data" class="my-page-form">
                        <input type="hidden" name="memberId" id="updateProfileMemberId" value="<?= $member->getId(); ?>">
                        <div class="form-box">
                            <!-- 카페 설명 -->
                            <div class="field-box">
                                <div class="label-box">
                                    <label class="label-section" for="userName">
                                        <span class="label-text">아이디</span>
                                        <span class="description">(Email)</span>
                                    </label>
                                </div>

                                <div class="input-box">
                                    <span class="introduce label-text"><?= $member->getUsername(); ?></span>
                                </div>
                            </div>

                            <hr>
                            <!-- 닉네임 -->
                            <div class="field-box">

                                <div class="label-box">
                                    <label class="label-section" for="nickName">
                                        <span class="label-text">닉네임</span>
                                        <span class="description">(NickName)</span>
                                        <span class="required-field">*</span>
                                    </label>
                                </div>

                                <div class="input-box">
                                    <input class="custom-input" id="nickName" maxlength="50" placeholder="닉네임" required name="nickName" type="text" value="<?= $member->getNickName(); ?>">
                                    <div class="message-box">
                                        <input id="duplicateNickname" type="button" value="중복확인" class="btn" style="display:none;">
                                        <input type="hidden" name="isNickNameChecked" id="isNickNameChecked" value="true">
                                        <span class="description pl-5" id="nickname-duplication-check-message"></span>
                                    </div>
                                </div>

                            </div>
                            <div class="caption-box">

                                <div class="label-box">
                                    <div class="label-section">
                                    </div>
                                </div>

                                <div class="input-box">
                                    <span class="description pl-5 caption"> * 영문(대ㆍ소문자 구분없음)또는 한글, 숫자(선택)를 포함한 2~10글자</span>
                                </div>

                            </div>
                            <hr>

                            <!-- 자기소개 -->
                            <div class="field-box introduce">

                                <div class="label-box intro-label">
                                    <label class="label-section" for="introduce">
                                        <span class="label-text">자기소개</span>
                                    </label>
                                    <div class="text-caculate-intro" style="display:none;">
                                        0 / 200
                                    </div>
                                </div>

                                <div class="input-box">
                                    <textarea class="intro-text-box" name="introduce" id="introduce" cols="30" rows="10" placeholder="자기소개를 입력해주세요." maxlength="500"><?= $member->getIntroduce(); ?></textarea>
                                </div>

                            </div>
                            <hr>

                            <!-- 연락처 -->
                            <div class="field-box">

                                <div class="label-box">
                                    <label class="label-section" for="phone">
                                        <span class="label-text">연락처</span>
                                        <span class="description">(Phone)</span>
                                        <span class="required-field">*</span>
                                    </label>
                                </div>

                                <div class="input-box">
                                    <input class="custom-input" id="phone" placeholder="ex) 01012345678" required name="phone" type="text" value="<?= $member->getPhone(); ?>">
                                    <span class="description pl-5" id="phone-validation-message"></span>
                                </div>

                            </div>

                            <div class="caption-box">

                                <div class="label-box">
                                    <div class="label-section">
                                    </div>
                                </div>

                                <div class="input-box enter">
                                    <span class="description pl-5 caption">* 하이픈(-)을 제거하고 입력해주세요(해외연락처 예시 : +821012345678)</span>
                                    <span class="description pl-5 caption">* For international numbers, include the country code (e.g., +821012345678)</span>
                                </div>

                            </div>
                            <hr>

                            <!-- 이름 -->
                            <div class="field-box">

                                <div class="label-box">
                                    <label class="label-section" for="firstName">
                                        <span class="label-text">이름</span>
                                        <span class="description">(First name / Given name)</span>
                                        <span class="required-field">*</span>
                                    </label>
                                </div>

                                <div class="input-box">
                                    <input class="custom-input" id="firstName" placeholder="ex) 길동" required name="firstName" type="text" value="<?= $member->getFirstName(); ?>">
                                    <div class="message-box">
                                        <span class="description pl-5" id="firstname-validation-message"></span>
                                    </div>
                                </div>

                            </div>
                            <hr>
                            <!-- 성 -->
                            <div class="field-box">

                                <div class="label-box">
                                    <label class="label-section" for="lastName">
                                        <span class="label-text">성</span>
                                        <span class="description">(Last name / Family name)</span>
                                        <span class="required-field">*</span>
                                    </label>
                                </div>

                                <div class="input-box">
                                    <input class="custom-input" id="lastName" placeholder="ex) 홍" required name="lastName" type="text" value="<?= $member->getLastName(); ?>">
                                    <div class="message-box">
                                        <span class="description pl-5" id="lastname-validation-message"></span>
                                    </div>
                                </div>

                            </div>
                            <hr>
                            <!-- 성별 -->
                            <div class="field-box">

                                <div class="label-box">
                                    <label class="label-section" for="gender">
                                        <span class="label-text">성별</span>
                                        <span class="description">(Gender)</span>
                                    </label>
                                </div>

                                <div class="input-box">
                                    <select id="gender" name="gender" class="custom-input">
                                        <option value="">성별 선택</option>
                                        <option value="true" <?= $member->getGender() == '0' ? 'selected' : '' ?>>남성</option>
                                        <option value="false" <?= $member->getGender() == '1' ? 'selected' : '' ?>>여성</option>
                                    </select>
                                    <div class="message-box">
                                        <span class="description pl-5" id="gender-validation-message"></span>
                                    </div>
                                </div>

                            </div>
                            <hr>
                            <!-- 생년월일 -->
                            <div class="field-box">

                                <div class="label-box">
                                    <label class="label-section" for="birthDate">
                                        <span class="label-text">생년월일</span>
                                        <span class="description">(Birth)</span>
                                    </label>
                                </div>

                                <div class="input-box">
                                    <input class="custom-input" id="birth" pattern="\d{4}-\d{2}-\d{2}" name="birth" type="date" value="<?= $member->getBirth() ? $member->getBirth()->format('Y-m-d') : '' ?>">
                                    <div class="message-box">
                                        <span class="description pl-5" id="birth-validation-message"></span>
                                    </div>
                                </div>

                            </div>
                            <div class="caption-box">

                                <div class="label-box">
                                    <div class="label-section">
                                    </div>
                                </div>

                                <div class="input-box">
                                    <span class="description pl-5 caption"> * 올바른 날짜 형식을 입력해주세요. (예: 1993-11-03)</span>
                                </div>

                            </div>
                            <hr>
                            <!-- 다음카카오 주소API(우편번호) -->
                            <div class="field-box">

                                <div class="label-box">
                                    <label class="label-section" for="sample4_postcode">
                                        <span class="label-text">우편번호</span>
                                    </label>
                                </div>

                                <div class="input-box">
                                    <input class="custom-input" id="sample4_postcode" placeholder="우편번호" name="postalNum" type="text" readonly value="<?= $member->getPostalNum() ?>">
                                    <input onclick="sample4_execDaumPostcode()" type="button" value="우편번호 찾기" class="btn"> <!--address.js-->
                                </div>

                            </div>

                            <!-- 다음카카오 주소API(도로명주소) -->
                            <div class="field-box">

                                <div class="label-box">
                                    <label class="label-section" for="sample4_roadAddress">
                                        <span class="label-text">도로명</span>
                                    </label>
                                </div>

                                <div class="input-box">
                                    <input class="custom-input" id="sample4_roadAddress" placeholder="도로명" name="roadAddress" type="text" readonly value="<?= $member->getRoadAddress() ?>">
                                </div>

                            </div>

                            <!-- 다음카카오 주소API(지번주소) -->
                            <div class="field-box">

                                <div class="label-box">
                                    <label class="label-section" for="sample4_jibunAddress">
                                        <span class="label-text">지번주소</span>
                                    </label>
                                </div>

                                <div class="input-box">
                                    <input class="custom-input" id="sample4_jibunAddress" placeholder="지번주소" name="jibunAddress" type="text" readonly value="<?= $member->getJibunAddress() ?>">
                                    <span id="guide" style="color:#999;display:none"></span>
                                </div>

                            </div>

                            <!-- 다음카카오 주소API(참고항목-법정동명) -->
                            <div class="field-box">

                                <div class="label-box">
                                    <label class="label-section" for="sample4_extraAddress">
                                        <span class="label-text">참고항목</span>
                                    </label>
                                </div>

                                <div class="input-box">
                                    <input class="custom-input" id="sample4_extraAddress" placeholder="참고항목" name="extraAddress" type="text" readonly value="<?= $member->getExtraAddress() ?>">
                                </div>

                            </div>

                            <!-- 다음카카오 주소API(상세주소) -->
                            <div class="field-box">

                                <div class="label-box">
                                    <label class="label-section" for="sample4_detailAddress">
                                        <span class="label-text">상세주소</span>
                                    </label>
                                </div>

                                <div class="input-box">
                                    <input class="custom-input" id="sample4_detailAddress" placeholder="상세주소" name="detailAddress" type="text" value="<?= $member->getDetailAddress() ?>">
                                    <input type="button" onclick="resetAllAddressFields()" class="btn" value="주소 초기화">
                                </div>

                            </div>
                            <hr>
                        </div>

                        <!-- 저장버튼 -->
                        <div class="center btn-box">
                            <input class="form-btn-box submit-btn" type="submit" value="저장">
                            <a href="/">
                                <input class="form-btn-box btn" type="button" value="취소">
                            </a>
                        </div>
                    </form>
                </div>

            </div>
        <? endif; ?>

        <!-- 비밀번호 변경 탭 내용 -->
        <div id="modifyPasswordSection" style="display:none;">
            <? if (isset($_SESSION['user_data'])) : ?>
                <? $user = $_SESSION['user_data']; ?>
                <? $createDate = $this->session->userdata('create_date'); ?>
                <? if (!empty($this->session->flashdata('error'))) : ?>
                    <div class="error-message center"><? echo $this->session->flashdata('error'); ?></div>
                <? endif; ?>

                <div class="inline">
                    <!-- FindEmailForm -->
                    <form id="prfl-password-form" method="POST" action="/member/MypageController/processModifyPassword" id="modifyPassword" class="my-page-form">
                        <input type="hidden" name="memberId" id="modifiedPasswordMemberId" value="<?= $user['user_id']; ?>">
                        <div class="form-box">
                            <div class="pw-field-box">
                                <div class="label-box">
                                    <label class="label-section" for="oldpassword">
                                        <span class="label-text">기존 비밀번호</span>
                                    </label>
                                </div>

                                <div class="input-box">
                                    <input autofocus class="custom-input" maxlength="50" name="oldpassword" id="oldpassword" placeholder="기존 비밀번호를 입력해주세요." type="password" autocomplete="off" required>
                                </div>
                            </div>

                            <hr>

                            <div class="pw-field-box">
                                <div class="label-box">
                                    <label class="label-section" for="newpassword">
                                        <span class="label-text">신규 비밀번호</span>
                                    </label>
                                </div>

                                <div class="input-box">
                                    <input autofocus class="custom-input" maxlength="50" name="newpassword" id="newpassword" placeholder="신규 비밀번호를 입력해주세요." type="password" autocomplete="off" required>
                                </div>
                                <div class="absolute">
                                    <div class="description" id="newpassword-validation-message"></div>
                                    <div class="description" id="newpassword-space-validation-message"></div>
                                </div>
                            </div>
                            <hr>

                            <div class="pw-field-box">
                                <div class="label-box">
                                    <label class="label-section" for="newpasswordcf">
                                        <span class="label-text">신규 비밀번호 확인</span>
                                    </label>
                                </div>

                                <div class="input-box">
                                    <input autofocus class="custom-input" maxlength="50" name="newpasswordcf" id="newpasswordcf" placeholder="신규 비밀번호를 한 번 더 입력해주세요." type="password" autocomplete="off" required>
                                </div>
                                <div class="absolute-match">
                                    <span class="description" id="newpasswordcf-validation-message"></span>
                                </div>
                            </div>

                            <hr>
                        </div>

                        <div class="center btn-box">
                            <input class="btn find-account-btn" type="submit" value="비밀번호 변경">
                        </div>
                </div>
                </form>
        </div>
    <?php else : ?>
        <div class="error-message">
            <h1 class="title">
                <i class="fa-solid fa-triangle-exclamation"></i>
                잘못된 접근입니다.
            </h1>
            <p class="page-guide">비밀번호 변경을 위해서는 올바른 절차를 따라야 합니다.</p>
            <div class="btn-box flex-end">
                <a href="/member/logincontroller" class="btn btn-primary">로그인 페이지로 이동</a>
                <a href="/" class="btn btn-secondary">메인 페이지로 이동</a>
            </div>
        </div>
    <?php endif; ?>
    </div>
    </div>
</section>