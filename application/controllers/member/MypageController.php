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

    public function modifyPassword()
    {
        $page_view_data['title'] = '비밀번호 변경';
        $this->layout->view('member/modify_password', $page_view_data);
    }

    public function processModifyPassword()
    {
        $userId = $this->session->userdata('resetMemberId');
        $oldPassword = $this->input->post('oldpassword');
        $newPassword = $this->input->post('newpassword');
        $newPasswordConfirm = $this->input->post('newpasswordcf');
    
        $regex = '/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

        if (!preg_match($regex, $newPassword)) {
            $this->session->set_flashdata('error', '신규 비밀번호는 영문, 숫자, 특수문자를 포함한 8자 이상이어야 합니다.');
            redirect('/member/mypagecontroller/modifypassword');
            return;
        }
    
        if ($newPassword !== $newPasswordConfirm) {
            $this->session->set_flashdata('error', '신규 비밀번호와 신규 비밀번호 확인이 일치하지 않습니다.');
            redirect('/member/mypagecontroller/modifypassword');
            return;
        }
    
        try {
            $result = $this->MypageModel->updatePassword($userId, $oldPassword, $newPassword);
            if ($result) {
                $this->session->unset_userdata(['resetMemberId', 'resetMemberEmail', 'resetMemberCreateDate']);
                $this->session->set_userdata('passwordChanged', true);
                redirect('/member/findaccountcontroller/modifyPasswordDone');
            } else {
                throw new Exception('비밀번호 변경에 실패했습니다.');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('/member/mypagecontroller/modifypassword');
        }
    }
}
