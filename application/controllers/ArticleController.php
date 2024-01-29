<?
defined('BASEPATH') OR exit('No direct script access allowed');

class ArticleController extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->model('articlemodel'); 
        $data['articles'] = $this->articlemodel;  // 모델의 메서드 호출
    }
}