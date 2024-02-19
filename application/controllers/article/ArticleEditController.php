<?
defined('BASEPATH') or exit('No direct script access allowed');
class ArticleEditController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article/ArticleEditModel', 'ArticleEditModel');
        $this->load->library('doctrine');
        $this->em = $this->doctrine->em;
    }

    public function index()
    {
        if ($this->session->userdata('user_data')) {
            $page_view_data['title'] = '카페 글쓰기';
            $this->layout->view('article/article_form', $page_view_data);
        } else {
            $page_view_data['title'] = '오류 발생';
            $this->layout->view('errors/error_page', $page_view_data);
        }
    }

    public function processWrite ($formData) {
        $formData = [
            'articleBoardId' => trim($this->input->post('articleBoardId', TRUE)),
            'memberId' => trim($this->input->post('memberId', TRUE)),
            'parentId' => trim($this->input->post('parentId', TRUE)),
            'createDate' => trim($this->input->post('createDate', TRUE)),
            'ip' => trim($this->input->post('ip', TRUE)),
            'title' => trim($this->input->post('title', TRUE)),
            'content' => trim($this->input->post('content', TRUE)),
            'hit' => trim($this->input->post('hit', TRUE)),
            'publicScope' => trim($this->input->post('publicScope', TRUE)),
            'depth' => trim($this->input->post('depth', TRUE))
        ];
    }
}