<?
defined('BASEPATH') or exit('No direct script access allowed');

class LoginController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('member/LoginModel', 'loginModel');
    }

    public function index()
    {
        $page_view_data['title'] = '로그인';
        $this->layout->view('member/login_form', $page_view_data);
    }

    public function processLogin()
    {
        if ($this->session->userdata('user_data')) {
            redirect('/');
        }
        $formData = [
            'userName' => trim($this->input->post('userName', TRUE)),
            'password' => trim($this->input->post('password', TRUE))
        ];

        $user = $this->loginModel->authenticate($formData);

        if ($user['success']) {
            redirect('/');
        } else {
            $page_view_data['title'] = '로그인';
            $page_view_data['errors'] = $user['errors'];
            $this->layout->view('member/login_form', $page_view_data);
        }
    }

    public function processLogout()
    {
        $this->session->sess_destroy();
        redirect('/');
    }

    public function processLogoutAndRedirectLoginPage()
    {
        $this->session->sess_destroy();
        redirect('/member/logincontroller');
    }

    public function checkSession()
    {
        if ($this->input->is_ajax_request()) {
            $userData = $this->session->userdata('user_data');
            $isLoggedIn = $userData ? true : false;
            $role = $isLoggedIn ? $userData['role'] : 'guest';

            $response = array(
                'isLoggedIn' => $isLoggedIn,
                'role' => $role
            );

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
}
