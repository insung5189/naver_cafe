<?
defined('BASEPATH') OR exit('No direct script access allowed');

class SignupController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('doctrine');
    }

    public function index() {
        $this->load->view('templates/header');
        $this->load->view('member/signup_form');
        $this->load->view('templates/footer');
    }

    public function processMemberSignup() {
        // POST 데이터 가져오기(TRUE 인자를 사용하여 XSS 필터링)
        $createDate = new \DateTime(date('Y-m-d H:i'));
        $userName = trim($this->input->post('userName', TRUE));
        $isUserNameChecked = $this->input->post('isUserNameChecked', TRUE);
        $password = trim($this->input->post('password1', TRUE));
        $password2 = trim($this->input->post('password2', TRUE));
        $nickName = trim($this->input->post('nickName', TRUE));
        $isNickNameChecked = $this->input->post('isNickNameChecked', TRUE);
        $postalNum = trim($this->input->post('postalNum', TRUE));
        $roadAddress = trim($this->input->post('roadAddress', TRUE));
        $jibunAddress = trim($this->input->post('jibunAddress', TRUE));
        $detailAddress = trim($this->input->post('detailAddress', TRUE));
        $extraAddress = trim($this->input->post('extraAddress', TRUE));
        $phone = trim($this->input->post('phone', TRUE));
        $firstName = trim($this->input->post('firstName', TRUE));
        $lastName = trim($this->input->post('lastName', TRUE));
        $gender = trim($this->input->post('gender', TRUE));
        $birth = trim($this->input->post('birth', TRUE));

        $errorData['errors'] = [];

        // // Email 형식 검증
        // $userEmailName = $this->doctrine->em->getRepository('Models\Entities\Member')->findOneBy(['userName' => $userName]);
        // if (!empty($userName)) {
        //     if (!filter_var($userName, FILTER_VALIDATE_EMAIL)) {
        //         $errorData['errors']['email'] = '올바른 이메일 형식이 아닙니다.';
        //     } 
        //     if ($isUserNameChecked !== 'true') {
        //         $errorData['errors']['email'] = '이메일 중복 확인이 필요합니다. 스크립트 사용을 허용해주세요.';
        //     } else if ($userEmailName) { // Email 중복확인
        //         $errorData['errors']['email'] = '이미 사용 중인 이메일입니다.';
        //     }
        // } else {
        //     $errorData['errors']['email'] = '이메일 입력값이 없습니다. 이메일을 입력해주세요.';
        // }

        // // 비밀번호 유효성검사
        // if (!empty($password) && !empty($password2)) {
        //     // 비밀번호 패턴확인
        //     if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $password)) {
        //         $errorData['errors']['password1'] = '비밀번호는 영문, 숫자, 특수문자를 포함한 8자 이상이어야 합니다.';
        //     } else if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $password2)) { // 비밀번호 확인필드 패턴확인
        //         $errorData['errors']['password2'] = '비밀번호 확인란의 패턴이 부합하지 않습니다.';
        //     } else if ($password != $password2) { // 비밀번호 확인필드 일치여부
        //         $errorData['errors']['password2'] = '비밀번호 확인이 일치하지 않습니다.';
        //     }
        // } else {
        //     $errorData['errors']['password1'] = '비밀번호 입력값이 없습니다. 비밀번호를 입력해주세요.';
        // }

        // // 연락처 유효성검사
        // if (!empty($phone)) {
        //     if (!preg_match('/^(\+\d{1,3}-?)?(01[016-9]|02|0[3-6][1-5]?|070)-?([1-9]\d{2,3}-?\d{4})$/', $phone)) {
        //         $errorData['errors']['phone'] = '유효하지 않은 전화번호 형식입니다.';
        //     }
        // } else {
        //     $errorData['errors']['phone'] = '연락처 입력값이 없습니다. 연락처를 입력해주세요.';
        // }

        // // 닉네임 유효성검사
        // $userNickName = $this->doctrine->em->getRepository('Models\Entities\Member')->findOneBy(['nickName' => $nickName]);
        // if (!empty($nickName)) {
        //     // 닉네임 패턴확인
        //     if (empty($nickName) || mb_strlen($nickName) < 2 || mb_strlen($nickName) > 10 || !preg_match('/^[가-힣a-zA-Z0-9]+$/', $nickName)) {
        //         $errorData['errors']['nickName'] = '닉네임은 2~10자의 한글, 영문, 숫자만 사용할 수 있습니다.';
        //     }
        //     if ($isNickNameChecked !== 'true') {
        //         $errorData['errors']['nickName'] = '닉네임 중복 확인이 필요합니다. 스크립트 사용을 허용해주세요.';
        //     } else if ($userNickName) { // 닉네임 중복확인
        //         $errorData['errors']['nickName'] = '이미 사용 중인 닉네임입니다.';
        //     }
        // } else {
        //     $errorData['errors']['nickName'] = '닉네임 입력값이 없습니다. 닉네임을 입력해주세요.';
        // }

        // $namePattern = '/^[A-Za-z\x{00C0}-\x{00FF}\x{0100}-\x{017F}\x{0180}-\x{024F}\x{0370}-\x{03FF}\x{0400}-\x{04FF}\x{1E00}-\x{1EFF}\x{2C00}-\x{2C7F}\x{2D00}-\x{2D2F}\x{3000}-\x{303F}\x{3400}-\x{4DBF}\x{4E00}-\x{9FFF}\x{A000}-\x{A48F}\x{A490}-\x{A4CF}\x{AC00}-\x{D7AF}\x{F900}-\x{FAFF}\x{FE30}-\x{FE4F}\-\'\s]+$/u';
        
        // // 이름 유효성검사
        // if (!empty($firstName)) {
        //     if (!preg_match($namePattern, $firstName)) {
        //         $errorData['errors']['firstName'] = '입력한 이름값이 유효하지 않습니다.';
        //     }
        // } else {
        //     $errorData['errors']['firstName'] = '이름 입력값이 없습니다. 이름을 입력해주세요.';
        // }

        // // 성 유효성검사
        // if (!empty($lastName)) {
        //     if (!preg_match($namePattern, $lastName)) {
        //         $errorData['errors']['lastName'] = '입력한 성 값이 유효하지 않습니다.';
        //     }
        // } else {
        //     $errorData['errors']['lastName'] = '성 입력값이 없습니다. 성 을 입력해주세요.';
        // }

        // // 성별 유효성검사
        // if (!empty($gender)) {
        //     if ($gender !== 'true' && $gender !== 'false') {
        //         $errorData['errors']['gender'] = '입력한 성별 값이 유효하지 않습니다.';
        //     }
        // } else {
        //     $gender = NULL;
        // }

        // 회원 엔티티 생성
        $member = new Models\Entities\Member();
        $member->setCreateDate($createDate);
        $member->setUserName($userName);
        $member->setPassword($password);
        $member->setNickName($nickName);
        $member->setPostalNum($postalNum);
        $member->setRoadAddress($roadAddress);
        $member->setJibunAddress($jibunAddress);
        $member->setDetailAddress($detailAddress);
        $member->setExtraAddress($extraAddress);
        $member->setPhone($phone);
        $member->setFirstName($firstName);
        $member->setLastName($lastName);
        $member->setGender($gender);
        $member->setBirth($birth); // 빈 문자열인 경우 null 할당
        $member->setIsActive(true);
        $member->setBlacklist(false);
        $member->setRole('ROLE_ADMIN');
        $member->setIntroduce(NULL);

        // 파일 업로드 설정
        $config['upload_path'] = FCPATH.'assets'.DIRECTORY_SEPARATOR.'file'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR;
        $config['allowed_types'] = 'jpg|jpeg|png|bmp';
        $config['max_size'] = '51200';

        $this->load->library('upload', $config);

        if (isset($_FILES['file']) && $_FILES['file']['name'] != '') { // 파일이 한 개 인지 확인
            if ($this->upload->do_upload('file')) {
                // 파일 업로드 성공
                $uploadData = $this->upload->data();
                $originalName = trim(pathinfo($uploadData['client_name'], PATHINFO_FILENAME)); // 원본 파일명에서 확장자 제거
                $fileExt = $uploadData['file_ext'];
                $uploadDate = date('Ymd');
                $uuid = uniqid();
                $newFileName = "{$originalName}-{$uploadDate}-{$uuid}{$fileExt}"; // 새 파일명 생성
        
                // 새 파일명으로 파일 이동
                rename($uploadData['full_path'], $uploadData['file_path'] . $newFileName);

                // 회원 엔티티 업데이트
                $filePath = $config['upload_path'] . $newFileName; // 파일 전체 경로
                $member->setMemberFilePath(trim($filePath));
                $member->setMemberFileName(trim($newFileName));
            } else {
                // 파일 업로드 실패. 에러 처리 로직
                // $error = array('error' => $this->upload->display_errors());
                // $errorData['errors']['file'] = '파일업로드 중 오류가 발생했습니다.';
                $errorData['errors']['file'] = $this->upload->display_errors('', '');
            }
        } else if (!isset($_FILES['file']) || $_FILES['file']['name'] == '') {
            // 파일이 업로드되지 않았을 경우 기본 이미지 처리 로직
            $defaultImagePath = $config['upload_path'].'default.png';
            $defaultImageName = 'default.png';
            $member->setMemberFilePath($defaultImagePath);
            $member->setMemberFileName($defaultImageName);
        } else {
            // 파일 업로드 실패. 에러 처리 로직
            // $error = array('error' => $this->upload->display_errors());
            // $errorData['errors']['file'] = '파일업로드 중 오류가 발생했습니다.';
            $errorData['errors']['file'] = $this->upload->display_errors('', '');
        }

        if (!empty($errorData['errors'])) {
            // 헤더, 에러 메시지와 함께 폼, 푸터 뷰 로드
            $this->load->view('templates/header');
            $this->load->view('member/signup_form', $errorData); // 에러 메시지를 포함하여 뷰 로드
            $this->load->view('templates/footer');
            return;
        }
        // EntityManager를 통한 데이터 저장
        $em = $this->doctrine->em;
        $em->persist($member);
        $em->flush();

        // 성공 또는 실패 메시지 처리
        if ($member->getId()) {
            echo "
                <script>
                    alert('회원이 등록되었습니다.');
                    location.href='/';
                </script>
            ";
        } else {
            echo "
                <script>
                    alert('회원 등록 중 오류가 발생했습니다.\n메인 화면으로 이동합니다.');
                    location.href='/';
                </script>
            ";
        }
    }

    // public function checkEmail() {
    //     $userName = $this->input->post('userName', TRUE);
        
    //     $em = $this->doctrine->em;
    //     $userRepo = $em->getRepository('Models\Entities\Member');
    //     $user = $userRepo->findOneBy(['userName' => $userName]);

    //     if ($user) {
    //         // 이메일이 중복됨
    //         echo json_encode(['isDuplicate' => true]);
    //     } else {
    //         // 이메일 사용 가능
    //         echo json_encode(['isDuplicate' => false]);
    //     }
    // }

    // public function checkNickname() {
    //     $nickName = $this->input->post('nickName', TRUE);
        
    //     $em = $this->doctrine->em;
    //     $userRepo = $em->getRepository('Models\Entities\Member');
    //     $user = $userRepo->findOneBy(['nickName' => $nickName]);
    
    //     if ($user) {
    //         // 닉네임이 중복됨
    //         echo json_encode(['isDuplicate' => true]);
    //     } else {
    //         // 닉네임 사용 가능
    //         echo json_encode(['isDuplicate' => false]);
    //     }
    // }

    // function validateBirthDate(&$birth) { // 참조에 의한 인자 전달을 사용하여 $birth 직접 수정
    //     $errorData = ['errors' => []];
    //     $datePattern = '/^\d{4}-\d{2}-\d{2}$/';
    //     $currentYear = date('Y');
    //     $minYear = $currentYear - 120; // 지금으로부터 120년 전
    //     $maxYear = $currentYear; // 현재 연도
    
    //     if (empty($birth)) {
    //         $birth = NULL; // 입력하지 않은 경우 $birth를 NULL로 설정
    //         return true; // 입력하지 않은 경우에 에러를 반환하지 않음
    //     }
    
    //     if (!preg_match($datePattern, $birth)) {
    //         $errorData['errors']['birth'] = '올바른 날짜 형식을 입력해주세요.';
    //         return false;
    //     }
    
    //     list($year, $month, $day) = explode('-', $birth);
    //     $inputDateObj = DateTime::createFromFormat('Y-m-d', $birth);
    //     $isDateValid = $inputDateObj && $inputDateObj->format('Y-m-d') === $birth;
    
    //     if (!$isDateValid) {
    //         $errorData['errors']['birth'] = '존재하지 않는 날짜입니다.';
    //         return false;
    //     }
    
    //     if ($year < $minYear || $year > $maxYear) {
    //         $errorData['errors']['birth'] = "생년월일의 년도는 ".$minYear."년부터 ".$maxYear."년 사이여야 합니다.";
    //         return false;
    //     }
    
    //     // 입력된 생년월일이 오늘 날짜를 초과하는지 검사
    //     $today = new DateTime();
    //     $today->setTime(0, 0, 0); // 오늘 날짜의 시작으로 설정
    //     if ($inputDateObj > $today) {
    //         $errorData['errors']['birth'] = '생년월일은 오늘 날짜를 초과할 수 없습니다.';
    //         return false;
    //     }
    
    //     // 모든 검사를 통과한 경우
    //     return true;
    // }
}