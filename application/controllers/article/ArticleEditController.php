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
            // 게시판 데이터 조회
            $boardRepo = $this->em->getRepository('Models\Entities\ArticleBoard');
            $boards = $boardRepo->findBy(['isDeleted' => 0], ['createDate' => 'DESC']);

            // 데이터를 뷰에 전달
            $page_view_data['title'] = '카페 글쓰기';
            $page_view_data['boards'] = $boards;
            $this->layout->view('article/article_form', $page_view_data);
        } else {
            $page_view_data['title'] = '오류 발생';
            $this->layout->view('errors/error_page', $page_view_data);
        }
    }

    public function processWrite()
    {
        if (!$this->session->userdata('user_data')) {
            show_error('로그인이 필요한 기능입니다.');
            return;
        }

        try {
            $formData = [
                'boardId' => $this->input->post('board'),
                'prefix' => $this->input->post('prefix'),
                'title' => $this->input->post('title'),
                'content' => $this->input->post('content'),
                'parentId' => $this->input->post('parent_id'),
                'publicScope' => $this->input->post('publicScope'),
            ];

            $this->ArticleEditModel->createArticle($formData);

            redirect('/');
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            log_message('error', $errorMessage);
            show_error($errorMessage);
        }
    }
}
