<?
defined('BASEPATH') OR exit('No direct script access allowed');
class SignupController extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('member/SignupModel', 'signupModel');
    }

    public function index() {
        $page_view_data['title'] = '회원가입';
        $this->layout->view('member/signup_form', $page_view_data);
    }

    public function processMemberSignup() {
        $formData = [
            'userName' => trim($this->input->post('userName', TRUE)),
            'password' => trim($this->input->post('password1', TRUE)),
            'password2' => trim($this->input->post('password2', TRUE)),
            'nickName' => trim($this->input->post('nickName', TRUE)),
            'phone' => trim($this->input->post('phone', TRUE)),
            'firstName' => trim($this->input->post('firstName', TRUE)),
            'lastName' => trim($this->input->post('lastName', TRUE)),
            'gender' => trim($this->input->post('gender', TRUE)),
            'birth' => trim($this->input->post('birth', TRUE)),
            'isUserNameChecked' => $this->input->post('isUserNameChecked', TRUE),
            'isNickNameChecked' => $this->input->post('isNickNameChecked', TRUE),
            'postalNum' => trim($this->input->post('postalNum', TRUE)),
            'roadAddress' => trim($this->input->post('roadAddress', TRUE)),
            'jibunAddress' => trim($this->input->post('jibunAddress', TRUE)),
            'detailAddress' => trim($this->input->post('detailAddress', TRUE)),
            'extraAddress' => trim($this->input->post('extraAddress', TRUE)),
            'file' => $_FILES['file'] ?? null
        ];
    
        $result = $this->signupModel->processSignup($formData);
    
        if ($result['success']) {
            redirect('/');
        } else {
            $page_view_data['title'] = '회원가입';
            $page_view_data['errors'] = $result['errors'];
            $this->layout->view('member/signup_form', $page_view_data);
        }
    }

    public function checkEmail() {
        $userName = $this->input->post('userName', TRUE);

        $em = $this->doctrine->em;
        $userRepo = $em->getRepository('Models\Entities\Member');
        $user = $userRepo->findOneBy(['userName' => $userName]);

        if ($user) {
            echo json_encode(['isDuplicate' => true]);
        } else {
            echo json_encode(['isDuplicate' => false]);
        }
    }

    public function checkNickname() {
        $nickName = $this->input->post('nickName', TRUE);
        
        $em = $this->doctrine->em;
        $userRepo = $em->getRepository('Models\Entities\Member');
        $user = $userRepo->findOneBy(['nickName' => $nickName]);
    
        if ($user) {
            echo json_encode(['isDuplicate' => true]);
        } else {
            echo json_encode(['isDuplicate' => false]);
        }
    }

}