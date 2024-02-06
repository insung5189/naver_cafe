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
        // POST 데이터 가져오기
        $createDate = new \DateTime(date('Y-m-d H:i'));
        $userName = $this->input->post('userName', TRUE);
        $password = $this->input->post('password1', TRUE);
        $nickName = $this->input->post('nickName', TRUE);
        $postalNum = $this->input->post('postalNum', TRUE);
        $roadAddress = $this->input->post('roadAddress', TRUE);
        $jibunAddress = $this->input->post('jibunAddress', TRUE);
        $detailAddress = $this->input->post('detailAddress', TRUE);
        $extraAddress = $this->input->post('extraAddress', TRUE);
        $phone = $this->input->post('phone', TRUE);
        $firstName = $this->input->post('firstName', TRUE);
        $lastName = $this->input->post('lastName', TRUE);
        $gender = $this->input->post('gender', TRUE);
        $birth = $this->input->post('birth', TRUE);

        if ($userName != NULL) {
            if (!filter_var($userName, FILTER_VALIDATE_EMAIL)) {
                echo "<script>alert('올바른 이메일 형식이 아닙니다.(백엔드)'); history.back();</script>";
                return;
            }
        } else {
            echo "<script>alert('이메일 입력값이 없습니다. 이메일을 입력해주세요(백엔드)'); history.back();</script>";
        }

        if ($password != NULL) {
            if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $password)) {
                echo "<script>alert('비밀번호는 영문, 숫자, 특수문자를 포함한 8자 이상이어야 합니다.(백엔드)'); history.back();</script>";
                return;
            }
        }

        if ($birth != NULL) {
            $currentYear = date('Y');
            $birthYear = explode('-', $birth)[0];
            if ($birthYear > $currentYear || $birthYear < 1900) {
                echo "<script>alert('유효하지 않은 생년월일입니다.(백엔드)'); history.back();</script>";
                return;
            }
        }

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
        if (!empty($birth)) {
            $member->setBirth(new \DateTime($birth));
        } else {
            $member->setBirth(null); // 빈 문자열인 경우 null 할당
        }
        $member->setIsActive(true);
        $member->setBlacklist(false);
        $member->setRole('ROLE_ADMIN');
        $member->setIntroduce(NULL);

        // 파일 업로드 설정
        $config['upload_path'] = FCPATH.'assets'.DIRECTORY_SEPARATOR.'file'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR;
        $config['allowed_types'] = 'jpg|jpeg|png|bmp';
        $config['max_size'] = '51200';
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {
            // 파일 업로드 성공
            $uploadData = $this->upload->data();
            $originalName = pathinfo($uploadData['client_name'], PATHINFO_FILENAME); // 원본 파일명에서 확장자 제거
            $fileExt = $uploadData['file_ext'];
            $uploadDate = date('Ymd');
            $uuid = uniqid();
            $newFileName = "{$originalName}-{$uploadDate}-{$uuid}{$fileExt}"; // 새 파일명 생성
    
            // 새 파일명으로 파일 이동
            rename($uploadData['full_path'], $uploadData['file_path'] . $newFileName);
    
            // 회원 엔티티 업데이트
            $filePath = $config['upload_path'] . $newFileName; // 파일 전체 경로
            $member->setMemberFilePath($filePath);
            $member->setMemberFileName($newFileName);
    
        } else {
            // 파일 업로드 실패. 에러 처리 로직
            $error = array('error' => $this->upload->display_errors());
            echo "
            <script>
                alert('파일업로드 중 오류가 발생했습니다.'); history.back();
            </script>
        ";
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

    public function checkEmail() {
        $userName = $this->input->post('userName', TRUE);
        
        $em = $this->doctrine->em;
        $userRepo = $em->getRepository('Models\Entities\Member');
        $user = $userRepo->findOneBy(['userName' => $userName]);

        if ($user) {
            // 이메일이 중복됨
            echo json_encode(['isDuplicate' => true]);
        } else {
            // 이메일 사용 가능
            echo json_encode(['isDuplicate' => false]);
        }
    }

    public function checkNickname() {
        $nickName = $this->input->post('nickName', TRUE);
        
        $em = $this->doctrine->em;
        $userRepo = $em->getRepository('Models\Entities\Member');
        $user = $userRepo->findOneBy(['nickName' => $nickName]);
    
        if ($user) {
            // 닉네임이 중복됨
            echo json_encode(['isDuplicate' => true]);
        } else {
            // 닉네임 사용 가능
            echo json_encode(['isDuplicate' => false]);
        }
    }
}