<?
defined('BASEPATH') or exit('No direct script access allowed');
class MypageController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('member/MypageModel', 'MypageModel');
    }

    public function index()
    {
        $userData = $this->session->userdata('user_data');
        $memberId = isset($userData['user_id']) ? $userData['user_id'] : null;

        if ($memberId) {
            $member = $this->em->getRepository('Models\Entities\Member')->find($memberId);
            $articleCount = count($this->MypageModel->getArticlesByMemberId($memberId));

            $page_view_data = [
                'title' => '마이페이지',
                'member' => $member,
                'articleCount' => $articleCount,
            ];
            $this->layout->view('member/my_page', $page_view_data);
        } else {
            $page_view_data['title'] = '오류 발생';
            $this->layout->view('errors/error_page', $page_view_data);
        }
    }

    public function index2()
    {
        $userData = $this->session->userdata('user_data');
        $memberId = isset($userData['user_id']) ? $userData['user_id'] : null;

        if ($memberId) {
            $member = $this->em->getRepository('Models\Entities\Member')->find($memberId);
            $articleCount = count($this->MypageModel->getArticlesByMemberId($memberId));

            $page_view_data = [
                'title' => '마이페이지',
                'member' => $member,
                'articleCount' => $articleCount,
            ];
            $this->layout->view('member/my_page_copy', $page_view_data);
        } else {
            $page_view_data['title'] = '오류 발생';
            $this->layout->view('errors/error_page', $page_view_data);
        }
    }

    public function processUpdateProfile()
    {
        $formData = [
            'memberId' => trim($this->input->post('memberId', TRUE)),
            'nickName' => trim($this->input->post('nickName', TRUE)),
            'introduce' => trim($this->input->post('introduce', TRUE)),
            'phone' => trim($this->input->post('phone', TRUE)),
            'firstName' => trim($this->input->post('firstName', TRUE)),
            'lastName' => trim($this->input->post('lastName', TRUE)),
            'gender' => trim($this->input->post('gender', TRUE)),
            'birth' => trim($this->input->post('birth', TRUE)),
            'isNickNameChecked' => $this->input->post('isNickNameChecked', TRUE),
            'postalNum' => trim($this->input->post('postalNum', TRUE)),
            'roadAddress' => trim($this->input->post('roadAddress', TRUE)),
            'jibunAddress' => trim($this->input->post('jibunAddress', TRUE)),
            'detailAddress' => trim($this->input->post('detailAddress', TRUE)),
            'extraAddress' => trim($this->input->post('extraAddress', TRUE)),
        ];

        $result = $this->MypageModel->updateProfile($formData);

        if ($result['success']) {
            echo json_encode(['success' => true, 'message' => '회원정보 변경 성공']);
        } else {
            echo json_encode(['success' => false, 'errors' => $result['errors']]);
            $page_view_data['title'] = '마이페이지';
            $page_view_data['errors'] = $result['errors'];
            $this->layout->view('member/my_page', $page_view_data);
        }
    }

    // 마이페이지 비밀번호 변경은 js로 토글되는 화면이라 ajax응답만으로 구성함
    public function processModifyPassword()
    {
        $userData = [
            'userId' => $this->input->post('memberId', TRUE),
            'oldPassword' => $this->input->post('oldpassword', TRUE),
            'newPassword' => $this->input->post('newpassword', TRUE),
            'newPasswordConfirm' => $this->input->post('newpasswordcf', TRUE),
        ];

        $result = $this->MypageModel->updatePassword($userData);
        if ($result['success']) {
            $this->session->unset_userdata(['resetMemberId', 'resetMemberEmail', 'resetMemberCreateDate']);
            $this->session->set_userdata('passwordChanged', true);
            echo json_encode(['success' => true, 'message' => '비밀번호 변경 성공']);
        } else {
            echo json_encode(['success' => false, 'errors' => $result['errors']]);
        }
    }

    public function modifyPasswordDone()
    {
        $page_view_data['title'] = '비밀번호 변경완료';
        $this->layout->view('member/modify_password_done', $page_view_data);
    }

    public function processUpdateProfileImage()
    {

        $config['upload_path'] = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'file' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'memberImgs' . DIRECTORY_SEPARATOR;
        $config['allowed_types'] = 'jpg|jpeg|png|bmp';
        $config['max_size'] = '51200';
        $config['encrypt_name'] = TRUE; // 파일명 암호화

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('member-prfl-img')) {
            echo json_encode(['success' => false, 'errors' => [$this->upload->display_errors()]]);
        } else {
            $uploadData = $this->upload->data();
            $filePath = $uploadData['file_name'];

            // DB에 파일 경로 저장 로직 (예: 회원 정보 업데이트)
            $result = $this->MypageModel->updateProfileImage($this->input->post('memberId'), $filePath);

            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'errors' => ['프로필 이미지 업데이트 실패']]);
            }
        }
    }


    // 기존 회원가입 과정의 로직에서 가져옴
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
