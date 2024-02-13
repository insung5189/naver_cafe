<?
defined('BASEPATH') OR exit('No direct script access allowed');

class MainController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('session');

    }

    public $mainPage ="메인 화면입니다.";

    public function index()
	{
		$this->load->view('dashboard/dashboard');
	}
}