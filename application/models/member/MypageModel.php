<?
defined('BASEPATH') or exit('직접적인 스크립트 접근은 허용되지 않습니다.');

class MypageModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('doctrine');
        $this->load->library('session');
        $this->em = $this->doctrine->em;
    }

    public function updateProfile($formData)
    {
        $errors = ['errors' => []];

        $this->validatePhone($formData, $errors);
        $this->validateNickname($formData, $errors);
        $this->validateName($formData, $errors);
        $this->validateGender($formData, $errors);
        $this->validateBirthDate($formData, $errors);

        if (!empty($errors['errors'])) {
            return ['success' => false, 'errors' => $errors['errors']];
        }

        try {
            $member = $this->em->getRepository('Models\Entities\Member')->find($formData['memberId']);
            if (!$member) {
                return ['success' => false, 'errors' => ['UserNotFound' => '사용자를 찾을 수 없습니다.']];
            }

            // 닉네임 중복 검사 (닉네임 변경 시)
            if ($formData['isNickNameChecked'] === 'true') {
                $existingUser = $this->em->getRepository('Models\Entities\Member')->findOneBy(['nickName' => $formData['nickName']]);
                if ($existingUser && $existingUser->getId() !== $formData['memberId']) {
                    return ['success' => false, 'errors' => ['NickNameDuplicate' => '이미 사용 중인 닉네임입니다.']];
                }
            }

            // 회원 정보 업데이트
            $member->setNickName($formData['nickName']);
            $member->setIntroduce($formData['introduce']);
            $formattedPhone = str_replace('-', '', $formData['phone']);
            $member->setPhone($formattedPhone);
            $member->setFirstName($formData['firstName']);
            $member->setLastName($formData['lastName']);
            $member->setGender($formData['gender'] === 'true' ? 1 : 0);
            $member->setBirth(new \DateTime($formData['birth']));
            $member->setPostalNum($formData['postalNum']);
            $member->setRoadAddress($formData['roadAddress']);
            $member->setJibunAddress($formData['jibunAddress']);
            $member->setDetailAddress($formData['detailAddress']);
            $member->setExtraAddress($formData['extraAddress']);
            $member->setModifyDate(new \DateTime(date('Y-m-d H:i')));

            $this->em->persist($member);
            $this->em->flush();

            return ['success' => true, 'errors' => []];
        } catch (\Exception $e) {
            log_message('error', '회원정보 변경 실패: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['Exception' => '회원정보 변경 중 오류가 발생했습니다.']];
        }
    }

    public function updatePassword($userData)
    {
        $regex = '/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        try {
            $user = $this->em->getRepository('Models\Entities\Member')->find($userData['userId']);
            if (!$user) {
                return ['success' => false, 'errors' => ['userNotFound' => '사용자를 찾을 수 없습니다.']];
            }

            if (!password_verify($userData['oldPassword'], $user->getPassword())) {
                return ['success' => false, 'errors' => ['oldPasswordMismatch' => '기존 비밀번호가 일치하지 않습니다.']];
            }

            $errors = $this->validateNewPasswordPolicy($userData['newPassword']);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            if (password_verify($userData['newPassword'], $user->getPassword())) {
                return ['success' => false, 'errors' => ['newPasswordSameAsOld' => '기존에 사용하던 비밀번호는 사용하실 수 없습니다.']];
            }

            if ($userData['newPassword'] !== $userData['newPasswordConfirm']) {
                return ['success' => false, 'errors' => ['passwordConfirmMismatch' => '신규 비밀번호와 비밀번호 확인이 일치하지 않습니다.']];
            }

            $user->setPassword(password_hash($userData['newPassword'], PASSWORD_DEFAULT));
            $this->em->flush();

            return ['success' => true, 'errors' => []];
        } catch (\Exception $e) {
            log_message('error', '비밀번호 변경 오류: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['exception' => '비밀번호 변경 중 오류가 발생했습니다.']];
        }
    }

    private function validateNewPasswordPolicy($password)
    {
        $errors = [];
        if (strlen($password) < 8) {
            $errors['length'] = '비밀번호는 8글자 이상이어야 합니다.';
        }
        if (!preg_match('/[a-zA-Z]/', $password)) {
            $errors['letter'] = '비밀번호에는 적어도 하나의 영문자가 포함되어야 합니다.';
        }
        if (!preg_match('/\d/', $password)) {
            $errors['digit'] = '비밀번호에는 적어도 하나의 숫자가 포함되어야 합니다.';
        }
        if (!preg_match('/[\W_]/', $password)) {
            $errors['special'] = '비밀번호에는 적어도 하나의 특수문자가 포함되어야 합니다.';
        }
        return $errors;
    }

    private function validatePhone($formData, &$errorData)
    {
        $phonePattern = '/^(?:(\+1|\+33|\+44|\+49|\+82|\+39|\+34|\+81|\+61|\+55|\+52|\+46|\+47|\+45|\+358|\+90|\+48|\+32|\+36|\+31|\+43|\+41|\+64)([1-9]\d{6,14}))|((02|0[3-9][0-9]?|070)([1-9]\d{6,7})|(01[016789])([1-9]\d{6,7}))$/';
        // 연락처 유효성검사
        if (!empty($formData['phone'])) {
            // 하이픈이 포함된 경우 바로 예외 처리
            if (strpos($formData['phone'], '-') !== false) {
                $errorData['errors']['phone'] = '전화번호 형식이 유효하지 않습니다. 하이픈(-) 없이 숫자만 입력해주세요.';
                return;
            }
            if (!preg_match($phonePattern, $formData['phone'])) {
                $errorData['errors']['phone'] = '전화번호 형식이 유효하지 않습니다. 올바른 형식으로 입력해주세요.';
            }
        } else {
            $errorData['errors']['phone'] = '연락처 입력값이 없습니다. 연락처를 입력해주세요.';
        }
    }

    private function validateNickname($formData, &$errors)
    {
        // 닉네임 유효성검사
        $currentMember = $this->em->getRepository('Models\Entities\Member')->find($formData['memberId']);
        $currentNickName = $currentMember ? $currentMember->getNickName() : null;

        $existingUser = $this->em->getRepository('Models\Entities\Member')->findOneBy(['nickName' => $formData['nickName']]); // 중복확인을 위해 DB 접근후 결과값 저장
        if (!empty($formData['nickName'])) {
            // 닉네임 패턴확인
            if (empty($formData['nickName']) || mb_strlen($formData['nickName']) < 2 || mb_strlen($formData['nickName']) > 10 || !preg_match('/^[가-힣a-zA-Z0-9]+$/', $formData['nickName'])) {
                $errors['errors']['nickName'] = '닉네임은 2~10자의 한글, 영문, 숫자만 사용할 수 있습니다.';
            }
            if ($formData['isNickNameChecked'] !== 'true') {
                $errors['errors']['nickName'] = '닉네임 중복 확인이 필요합니다. 스크립트 사용을 허용해주세요.';
            } else if ($existingUser && $existingUser->getId() !== $formData['memberId'] && $formData['nickName'] !== $currentNickName) {
                $errors['errors']['nickName'] = '이미 사용 중인 닉네임입니다.';
            }
        } else {
            $errors['errors']['nickName'] = '닉네임 입력값이 없습니다. 닉네임을 입력해주세요.';
        }
    }

    private function validateName($formData, &$errors)
    {

        // 동,서양 문화권의 이름패턴을 종합한 유니코드
        $namePattern = '/^[A-Za-z\x{00C0}-\x{00FF}\x{0100}-\x{017F}\x{0180}-\x{024F}\x{0370}-\x{03FF}\x{0400}-\x{04FF}\x{1E00}-\x{1EFF}\x{2C00}-\x{2C7F}\x{2D00}-\x{2D2F}\x{3000}-\x{303F}\x{3400}-\x{4DBF}\x{4E00}-\x{9FFF}\x{A000}-\x{A48F}\x{A490}-\x{A4CF}\x{AC00}-\x{D7AF}\x{F900}-\x{FAFF}\x{FE30}-\x{FE4F}\-\'\s]+$/u';

        // 이름 유효성검사
        if (!empty($formData['firstName'])) {
            if (!preg_match($namePattern, $formData['firstName'])) {
                $errors['errors']['firstName'] = '입력한 이름값이 유효하지 않습니다.';
            }
        } else {
            $errors['errors']['firstName'] = '이름 입력값이 없습니다. 이름을 입력해주세요.';
        }

        // 성 유효성검사
        if (!empty($formData['lastName'])) {
            if (!preg_match($namePattern, $formData['lastName'])) {
                $errors['errors']['lastName'] = '입력한 성 값이 유효하지 않습니다.';
            }
        } else {
            $errors['errors']['lastName'] = '성 입력값이 없습니다. 성 을 입력해주세요.';
        }
    }

    private function validateGender($formData, &$errors)
    {
        // 성별 유효성검사
        if (!empty($formData['gender'])) {
            if ($formData['gender'] !== 'true' && $formData['gender'] !== 'false') {
                $errors['errors']['gender'] = '입력한 성별 값이 유효하지 않습니다.';
            }
        } else {
            $formData['gender'] = NULL;
        }
    }

    private function validateBirthDate($formData, &$errors)
    {
        $datePattern = '/^\d{4}-\d{2}-\d{2}$/';
        $currentYear = date('Y');
        $minYear = $currentYear - 120; // 지금으로부터 120년 전

        if (empty($formData['birth'])) {
            $formData['birth'] = NULL;
            return true;
        }

        if (!preg_match($datePattern, $formData['birth'])) {
            $errors['errors']['birth'] = '올바른 날짜 형식을 입력해주세요.';
            return false;
        }

        list($year, $month, $day) = explode('-', $formData['birth']);
        $inputDateObj = DateTime::createFromFormat('Y-m-d', $formData['birth']);
        $isDateValid = $inputDateObj && $inputDateObj->format('Y-m-d') === $formData['birth'];

        if (!$isDateValid) {
            $errors['errors']['birth'] = '존재하지 않는 날짜입니다.';
            return false;
        }

        if ($year < $minYear || $year > $currentYear) {
            $errors['errors']['birth'] = "생년월일의 년도는 " . $minYear . "년부터 " . $currentYear . "년 사이여야 합니다.";
            return false;
        }

        $today = new DateTime();
        $today->setTime(0, 0, 0); // 오늘 날짜의 시작으로 설정
        if ($inputDateObj > $today) {
            $errors['errors']['birth'] = '생년월일은 오늘 날짜를 초과할 수 없습니다.';
            return false;
        }
        return true;
    }
}
