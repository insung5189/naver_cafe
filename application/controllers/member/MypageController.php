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

        if (!$this->validateNewPassword($newPassword, $newPasswordConfirm)) {
            $this->session->set_flashdata('error', '신규 비밀번호 유효성 검사에 실패했습니다.');
            redirect('/member/mypagecontroller/modifypassword');
            return;
        }

        try {
            $result = $this->MypageModel->updatePassword($userId, $oldPassword, $newPassword);
            if ($result) {
                $this->session->unset_userdata(['resetMemberId', 'resetMemberEmail', 'resetMemberCreateDate']);
                $this->session->set_flashdata('success', '비밀번호가 성공적으로 변경되었습니다. 다시 로그인해 주세요.');
                redirect('/member/logincontroller');
            } else {
                throw new Exception('비밀번호 변경에 실패했습니다.');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('/member/mypagecontroller/modifypassword');
        }
    }

    private function validateNewPassword($newPassword, $newPasswordConfirm) {
        $regex = '/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()\-_=+\[\]{}|;:\'",<.>/?]).{8,}$/';
        return preg_match($regex, $newPassword) && $newPassword === $newPasswordConfirm;
    }
    

    public function CT_validation() {
        $userId = $this->session->userdata('resetMemberId');
        $oldPassword = $this->input->post('oldpassword');
        $newPassword = $this->input->post('newpassword');
        $newPasswordConfirm = $this->input->post('newpasswordcf');
    
        $user = $this->em->getRepository('Models\Entities\Member')->find($userId);
    
        // 1. 기존 비밀번호와 데이터베이스에 있는 비밀번호가 일치하는지 확인
        if (!password_verify($oldPassword, $user->getPassword())) {
            echo json_encode(['success' => false, 'message' => '기존 비밀번호가 일치하지 않습니다.']);
            return;
        }
    
        // 2. 신규 비밀번호와 기존 비밀번호가 다른지 확인
        if ($oldPassword === $newPassword) {
            echo json_encode(['success' => false, 'message' => '신규 비밀번호는 기존 비밀번호와 동일할 수 없습니다.']);
            return;
        }
    
        // 3. 신규 비밀번호와 신규 비밀번호 확인이 일치하는지 확인
        if ($newPassword !== $newPasswordConfirm) {
            echo json_encode(['success' => false, 'message' => '신규 비밀번호 확인이 일치하지 않습니다.']);
            return;
        }
    
        // 모든 유효성 검사를 통과한 경우
        echo json_encode(['success' => true, 'message' => '비밀번호 변경이 가능합니다.']);
    }
}
