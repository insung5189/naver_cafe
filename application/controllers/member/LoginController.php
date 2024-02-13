<?
defined('BASEPATH') OR exit('No direct script access allowed');

class LoginController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('member/LoginModel', 'loginModel');
        $this->load->library('doctrine');
        $this->load->library('session');
    }

    public function index() {
        if ($this->session->userdata('user_data')) {
            // 이미 로그인한 사용자는 메인 페이지로 리다이렉션
            redirect('/');
        }
    
        // 로그인 페이지 뷰 로드
        $this->load->view('templates/header');
        $this->load->view('member/login_form');
        $this->output->enable_profiler(true);
        $this->load->view('templates/footer');
    }

    public function processLogin() {
        if ($this->session->userdata('user_data')) {
            // js의 confirm을 사용해서 '현재 로그인되어있는 사용자가 있습니다. 로그아웃하고 새로운 계정으로 접속하시겠습니까?' 라고 물어본 뒤 확인 을 누르면 기존 계정을 로그아웃하고 취소를 누르면 메인페이지로 리다이렉트 시킴
            redirect('/');
        }
        $formData = [
            'userName' => trim($this->input->post('userName', TRUE)),
            'password' => trim($this->input->post('password', TRUE))
        ];

        $user = $this->loginModel->authenticate($formData);

        if ($user['success']) {
            // 로그인 성공 후 리다이렉션
            redirect('/');
            echo "<script>alert('반갑다.');</script>";
        } else {
            $this->load->view('templates/header');
            $this->load->view('member/login_form', ['errors' => $user['errors']]);
            $this->load->view('templates/footer');
        }
    }

    public function processLogout() {
        // 로그아웃 하기전에 세션에 접속중인 사용자가 있는지 확인하고 있다면 로그아웃 처리하고 없다면 '현재 로그인 되어있는 계정이 없습니다.' 라는 메시지 출력 후 메인페이지('/')로 리다이렉션
        $this->session->sess_destroy();
        redirect('/');
    }
}