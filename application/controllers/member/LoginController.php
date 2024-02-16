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
        if ($this->session->userdata('user_data')) {
            redirect('/');
        }

        // 로그인 페이지 뷰 로드
        $page_view_data['title'] = '로그인';
        $this->layout->view('member/login_form', $page_view_data);
    }

    public function processLogin()
    {
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
            $page_view_data['title'] = '로그인';
            $page_view_data['errors'] = $user['errors'];
            $this->layout->view('member/login_form', $page_view_data);
        }
    }

    public function processLogout()
    {
        // 로그아웃 하기전에 세션에 접속중인 사용자가 있는지 확인하고 있다면 로그아웃 처리하고 없다면 '현재 로그인 되어있는 계정이 없습니다.' 라는 메시지 출력 후 메인페이지('/')로 리다이렉션
        $this->session->sess_destroy();
        redirect('/');
    }

    public function checkSession()
    {
        if ($this->input->is_ajax_request()) {
            $userData = $this->session->userdata('user_data');
            $isLoggedIn = $userData ? true : false;
            $role = $isLoggedIn ? $userData['role'] : 'guest';

            // 세션에 'user_data'가 존재하면 로그인 상태로 간주하고, 사용자의 역할을 반환
            $response = array(
                'isLoggedIn' => $isLoggedIn,
                'role' => $role // 추가된 부분: 사용자의 역할 반환
            );

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            // AJAX 요청이 아닌 경우에는 접근을 거부
            show_404(); // 혹은 다른 적절한 응답
        }

        // $.ajax({
        //     url: '/path/to/checkSession',
        //     type: 'GET',
        //     dataType: 'json',
        //     success: function(response) {
        //         if (response.isLoggedIn) {
        //             if (response.role === 'admin') {
        //                 // 관리자용 UI 요소 표시
        //             } else {
        //                 // 일반 사용자용 UI 요소 표시
        //             }
        //         } else {
        //             // 비로그인 상태용 UI 요소 표시
        //         }
        //     },
        //     error: function(xhr, status, error) {
        //         console.error("Error: ", error);
        //     }
        // });
    }
}
