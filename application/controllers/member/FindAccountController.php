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

    public function modifyPassword()
    {
        $page_view_data['title'] = '비밀번호 변경';
        $this->layout->view('member/modify_password', $page_view_data);
    }

    public function processFindPassword()
    {
        $formData = [
            'userName' => $this->input->post('userName', TRUE),
            'firstName' => $this->input->post('firstName', TRUE),
            'lastName' => $this->input->post('lastName', TRUE),
            'phone' => $this->input->post('phone', TRUE),
        ];

        $result = $this->FindAccountModel->validateMember($formData);

        if ($result['success']) {
            $member = $result['member'];
            $this->session->set_userdata([
                'resetMemberId' => $member->getId(),
                'resetMemberEmail' => $member->getUserName(),
                'resetMemberCreateDate' => $member->getCreateDate()->format('Y-m-d')
            ]);
            redirect('/member/findaccountcontroller/modifypassword');
        } else {
            $this->session->set_flashdata('findPasswordError', $result['message']);
            redirect('/member/findaccountcontroller/findEmailResult');
        }
    }

    public function processModifyPassword()
    {
        $userData = [
            'userId' => $this->session->userdata('resetMemberId'),
            'newPassword' => $this->input->post('newpassword'),
            'newPasswordConfirm' => $this->input->post('newpasswordcf'),
        ];

        $result = $this->FindAccountModel->updatePassword($userData);
        if ($result['success']) {
            $this->session->unset_userdata(['resetMemberId', 'resetMemberEmail', 'resetMemberCreateDate']);
            $this->session->set_userdata('passwordChanged', true);
            redirect('/member/findaccountcontroller/modifyPasswordDone');
        } else {
            $page_view_data['title'] = '비밀번호 변경';
            $page_view_data['errors'] = $result['errors'];
            $this->layout->view('member/modify_password', $page_view_data);
        }
    }

    public function oldpasswordcf()
    {
        try {
            $userId = $this->session->userdata('resetMemberId');
            $oldPassword = $this->input->post('oldpassword');

            $user = $this->em->getRepository('Models\Entities\Member')->find($userId);

            if (!$user) {
                throw new Exception('사용자를 찾을 수 없습니다.');
            }

            // 기존 비밀번호와 데이터베이스에 있는 비밀번호가 일치하는지 확인
            if (!password_verify($oldPassword, $user->getPassword())) {
                echo json_encode(['success' => false, 'message' => '❌ 기존 비밀번호가 일치하지 않습니다.']);
                return;
            }

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function modifyPasswordDone()
    {
        $page_view_data['title'] = '비밀번호 변경완료';
        $this->layout->view('member/modify_password_done', $page_view_data);

        // 페이지 접근 후 세션 변수 제거하여 재접근 방지
        $this->session->unset_userdata('passwordChanged');
    }

    public function processLogoutAndRedirectFindAccount()
    {
        $this->session->sess_destroy();
        redirect('/member/findaccountcontroller');
    }
}
