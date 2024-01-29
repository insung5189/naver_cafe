<?
defined('BASEPATH') OR exit('No direct script access allowed');

class MainController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database(); // 생성자에 직접 db 연결정보를 넣어줘서 클래스가 읽힐 때 무조건 db부터 조회하도록 함.
    }

    public $mainPage ="메인 화면입니다.";

    public function index()
	{
		$this->load->view('메인화면이여.');
	}
}