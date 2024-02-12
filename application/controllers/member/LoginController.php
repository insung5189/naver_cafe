<?
defined('BASEPATH') OR exit('No direct script access allowed');

class LoginController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('member/LoginModel', 'loginModel');
        $this->load->library('doctrine');
    }

    public function index() {
        $this->load->view('templates/header');
        $this->load->view('member/login_form');
        // $this->output->enable_profiler(true);
        $this->load->view('templates/footer');
    }

    public function processLogin($formData) {

    }
}