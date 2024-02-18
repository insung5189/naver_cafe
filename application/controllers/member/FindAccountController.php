<?
defined('BASEPATH') or exit('No direct script access allowed');

class FindAccountController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('member/FindAccountModel', 'FindAccountModel');
    }

    public function index()
    {
        $page_view_data['title'] = 'Email / 비밀번호 찾기';
        $this->layout->view('member/find_account', $page_view_data);
    }

    public function processFindEmail()
    {
        $formData = [
            'firstName' => trim($this->input->post('firstName', TRUE)),
            'lastName' => trim($this->input->post('lastName', TRUE)),
            'phone' => trim($this->input->post('phone', TRUE))
        ];

        $result = $this->FindAccountModel->findMemberEmail($formData);

        if ($result['success']) {
            $this->session->set_flashdata('foundEmails', $result['emails']);
            redirect('/member/findaccountcontroller/findEmailResult');
        } else {
            $this->session->set_flashdata('findEmailError', $result['errors']['message']);
            redirect('/member/findaccountcontroller/findEmailResult');
        }
    }

    public function findEmailResult()
    {
        $page_view_data['title'] = 'Email 확인 결과';
        $this->layout->view('member/find_email_result', $page_view_data);
    }

    public function processFindPassword()
    {
        $formData = [
            'userName' => $this->input->post('userName', TRUE),
            'firstName' => $this->input->post('firstName', TRUE),
            'lastName' => $this->input->post('lastName', TRUE),
            'phone' => $this->input->post('phone', TRUE),
        ];

        $user = $this->FindAccountModel->validateMember($formData);

        if ($user) {
            $this->session->set_userdata([
                'resetMemberId' => $user->getId(),
                'resetMemberEmail' => $user->getUserName(),
                'resetMemberCreateDate' => $user->getCreateDate()->format('Y-m-d')
            ]);
            redirect('/member/mypagecontroller/modifypassword');
        } else {
            $this->session->set_flashdata('error', '일치하는 회원이 없습니다.');
            redirect('/member/findaccountcontroller');
        }
    }

    public function processLogoutAndRedirectFindAccount()
    {
        $this->session->sess_destroy();
        redirect('/member/findaccountcontroller');
    }
}
