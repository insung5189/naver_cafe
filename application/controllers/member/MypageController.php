<?
defined('BASEPATH') or exit('No direct script access allowed');
class MypageController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('member/MypageModel', 'MypageModel');
        $this->load->library('doctrine');
        $this->em = $this->doctrine->em;
    }

    public function index()
    {
        if ($this->session->userdata('user_data')) {
            $page_view_data['title'] = '마이페이지';
            $this->layout->view('member/my_page', $page_view_data);
        } else {
            $page_view_data['title'] = '오류 발생';
            $this->layout->view('errors/error_page', $page_view_data);
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

        // 페이지 접근 후 세션 변수 제거하여 재접근 방지
        $this->session->unset_userdata('passwordChanged');
    }
}
