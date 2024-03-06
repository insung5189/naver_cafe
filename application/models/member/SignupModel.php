<?
defined('BASEPATH') or exit('No direct script access allowed');

class SignupModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function processSignup($formData)
    {

        $errorData = ['errors' => []];

        $this->validateEmail($formData, $errorData);
        $this->validatePassword($formData, $errorData);
        $this->validatePhone($formData, $errorData);
        $this->validateNickname($formData, $errorData);
        $this->validateName($formData, $errorData);
        $this->validateGender($formData, $errorData);
        $this->validateBirthDate($formData, $errorData);
        $this->processProfileImage($formData, $errorData);

        if (!empty($errorData['errors'])) {
            return ['success' => false, 'errors' => $errorData['errors']];
        }

        try {
            $member = new Models\Entities\Member();
            $member->setUserName($formData['userName']);
            $member->setPassword(password_hash($formData['password'], PASSWORD_DEFAULT));
            $member->setNickName($formData['nickName']);
            $formattedPhone = str_replace('-', '', $formData['phone']);
            $member->setPhone($formattedPhone);
            $member->setFirstName($formData['firstName']);
            $member->setLastName($formData['lastName']);
            $member->setGender($formData['gender'] === 'true' ? 1 : 0);
            $member->setCreateDate(new \DateTime(date('Y-m-d H:i')));
            $member->setPostalNum($formData['postalNum']);
            $member->setRoadAddress($formData['roadAddress']);
            $member->setJibunAddress($formData['jibunAddress']);
            $member->setDetailAddress($formData['detailAddress']);
            $member->setExtraAddress($formData['extraAddress']);
            $member->setIsActive(true);
            $member->setBlacklist(false);
            $member->setRole('ROLE_MEMBER');
            $member->setVisit(1);
            $member->setIntroduce(NULL);
            if (!empty($formData['birth'])) {
                $member->setBirth(new \DateTime($formData['birth']));
            }
            if (!empty($formData['memberFilePath']) && !empty($formData['memberFileName'])) {
                $member->setMemberFilePath($formData['memberFilePath']);
                $member->setMemberFileName($formData['memberFileName']);
            }

            // EntityManager로 데이터베이스에 저장
            $this->em->persist($member);
            $this->em->flush();
            $this->session->set_flashdata('welcome_message', $formData['nickName'] . '님 환영합니다.\n가입하신 계정으로 로그인해주세요.');
            return ['success' => true, 'errors' => []];
        } catch (\Exception $e) {
            log_message('error', '회원 가입 실패: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['message' => '회원 가입 중 오류가 발생했습니다.']];
        }
    }

    private function validateEmail($formData, &$errorData)
    {
        // Email 유효성검사
        $userEmailName = $this->doctrine->em->getRepository('Models\Entities\Member')->findOneBy(['userName' => $formData['userName']]); // 중복확인을 위해 DB 접근후 결과값 저장
        if (!empty($formData['userName'])) {
            if (!filter_var($formData['userName'], FILTER_VALIDATE_EMAIL)) {
                $errorData['errors']['email'] = '올바른 이메일 형식이 아닙니다.';
            }
            if ($formData['isUserNameChecked'] !== 'true') {
                $errorData['errors']['email'] = '이메일 중복 확인이 필요합니다. 스크립트 사용을 허용해주세요.';
            } else if ($userEmailName) {
                $errorData['errors']['email'] = '이미 사용 중인 이메일입니다.';
            }
        } else {
            $errorData['errors']['email'] = '이메일 입력값이 없습니다. 이메일을 입력해주세요.';
        }
    }

    private function validatePassword($formData, &$errorData)
    {
        // 비밀번호 유효성검사
        if (!empty($formData['password']) && !empty($formData['password2'])) {
            if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $formData['password'])) {
                $errorData['errors']['password1'] = '비밀번호는 영문, 숫자, 특수문자를 포함한 8자 이상이어야 합니다.';
            } else if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $formData['password2'])) {
                $errorData['errors']['password2'] = '비밀번호 확인란의 패턴이 부합하지 않습니다.';
            } else if ($formData['password'] != $formData['password2']) { // 비밀번호 확인필드 일치여부
                $errorData['errors']['password2'] = '비밀번호 확인이 기존 비밀번호와 일치하지 않습니다.';
            }
        } else {
            $errorData['errors']['password1'] = '비밀번호 입력값이 없습니다. 비밀번호를 입력해주세요.';
        }
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

    private function validateNickname($formData, &$errorData)
    {
        // 닉네임 유효성검사
        $userNickName = $this->doctrine->em->getRepository('Models\Entities\Member')->findOneBy(['nickName' => $formData['nickName']]); // 중복확인을 위해 DB 접근후 결과값 저장
        if (!empty($formData['nickName'])) {
            // 닉네임 패턴확인
            if (empty($formData['nickName']) || mb_strlen($formData['nickName']) < 2 || mb_strlen($formData['nickName']) > 10 || !preg_match('/^[가-힣a-zA-Z0-9]+$/', $formData['nickName'])) {
                $errorData['errors']['nickName'] = '닉네임은 2~10자의 한글, 영문, 숫자만 사용할 수 있습니다.';
            }
            if ($formData['isNickNameChecked'] !== 'true') {
                $errorData['errors']['nickName'] = '닉네임 중복 확인이 필요합니다. 스크립트 사용을 허용해주세요.';
            } else if ($userNickName) {
                $errorData['errors']['nickName'] = '이미 사용 중인 닉네임입니다.';
            }
        } else {
            $errorData['errors']['nickName'] = '닉네임 입력값이 없습니다. 닉네임을 입력해주세요.';
        }
    }

    private function validateName($formData, &$errorData)
    {

        // 동,서양 문화권의 이름패턴을 종합한 유니코드
        $namePattern = '/^[A-Za-z\x{00C0}-\x{00FF}\x{0100}-\x{017F}\x{0180}-\x{024F}\x{0370}-\x{03FF}\x{0400}-\x{04FF}\x{1E00}-\x{1EFF}\x{2C00}-\x{2C7F}\x{2D00}-\x{2D2F}\x{3000}-\x{303F}\x{3400}-\x{4DBF}\x{4E00}-\x{9FFF}\x{A000}-\x{A48F}\x{A490}-\x{A4CF}\x{AC00}-\x{D7AF}\x{F900}-\x{FAFF}\x{FE30}-\x{FE4F}\-\'\s]+$/u';

        // 이름 유효성검사
        if (!empty($formData['firstName'])) {
            if (!preg_match($namePattern, $formData['firstName'])) {
                $errorData['errors']['firstName'] = '입력한 이름값이 유효하지 않습니다.';
            }
        } else {
            $errorData['errors']['firstName'] = '이름 입력값이 없습니다. 이름을 입력해주세요.';
        }

        // 성 유효성검사
        if (!empty($formData['lastName'])) {
            if (!preg_match($namePattern, $formData['lastName'])) {
                $errorData['errors']['lastName'] = '입력한 성 값이 유효하지 않습니다.';
            }
        } else {
            $errorData['errors']['lastName'] = '성 입력값이 없습니다. 성 을 입력해주세요.';
        }
    }

    private function validateGender($formData, &$errorData)
    {
        // 성별 유효성검사
        if (!empty($formData['gender'])) {
            if ($formData['gender'] !== 'true' && $formData['gender'] !== 'false') {
                $errorData['errors']['gender'] = '입력한 성별 값이 유효하지 않습니다.';
            }
        } else {
            $formData['gender'] = NULL;
        }
    }

    private function validateBirthDate($formData, &$errorData)
    {
        $datePattern = '/^\d{4}-\d{2}-\d{2}$/';
        $currentYear = date('Y');
        $minYear = $currentYear - 120; // 지금으로부터 120년 전

        if (empty($formData['birth'])) {
            $formData['birth'] = NULL;
            return true;
        }

        if (!preg_match($datePattern, $formData['birth'])) {
            $errorData['errors']['birth'] = '올바른 날짜 형식을 입력해주세요.';
            return false;
        }

        list($year, $month, $day) = explode('-', $formData['birth']);
        $inputDateObj = DateTime::createFromFormat('Y-m-d', $formData['birth']);
        $isDateValid = $inputDateObj && $inputDateObj->format('Y-m-d') === $formData['birth'];

        if (!$isDateValid) {
            $errorData['errors']['birth'] = '존재하지 않는 날짜입니다.';
            return false;
        }

        if ($year < $minYear || $year > $currentYear) {
            $errorData['errors']['birth'] = "생년월일의 년도는 " . $minYear . "년부터 " . $currentYear . "년 사이여야 합니다.";
            return false;
        }

        $today = new DateTime();
        $today->setTime(0, 0, 0); // 오늘 날짜의 시작으로 설정
        if ($inputDateObj > $today) {
            $errorData['errors']['birth'] = '생년월일은 오늘 날짜를 초과할 수 없습니다.';
            return false;
        }
        return true;
    }

    private function processProfileImage(&$formData, &$errorData)
    {
        $config['upload_path'] = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'file' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'memberImgs' . DIRECTORY_SEPARATOR;
        $config['allowed_types'] = 'jpg|jpeg|png|bmp';
        $config['max_size'] = '51200';

        $this->load->library('upload', $config);

        if (isset($_FILES['file']) && $_FILES['file']['name'] != '') {
            if ($this->upload->do_upload('file')) {
                $uploadData = $this->upload->data();
                $originalName = trim(pathinfo($uploadData['client_name'], PATHINFO_FILENAME));
                $fileExt = $uploadData['file_ext'];
                $uploadDate = date('Ymd');
                $uuid = uniqid();
                $newFileName = "{$originalName}-{$uploadDate}-{$uuid}{$fileExt}"; // 새 파일명 생성 => {원본파일명}-{파일등록일}-{uuid}.{확장자}

                rename($uploadData['full_path'], $uploadData['file_path'] . $newFileName);

                $formData['memberFilePath'] = $config['upload_path'] . $newFileName;
                $formData['memberFileName'] = $newFileName;
            } else {
                $errorData['errors']['file'] = $this->upload->display_errors('', '');
            }
        } else if (!isset($_FILES['file']) || $_FILES['file']['name'] == '') {
            $defaultImagePath = $config['upload_path'] . 'defaultImg' . DIRECTORY_SEPARATOR . 'default.png';
            $defaultImageName = 'default.png';
            $formData['memberFilePath'] = $defaultImagePath;
            $formData['memberFileName'] = $defaultImageName;
        } else {
            $errorData['errors']['file'] = $this->upload->display_errors('', '');
        }
    }
}
