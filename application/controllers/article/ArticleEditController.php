<?
defined('BASEPATH') or exit('No direct script access allowed');
class ArticleEditController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article/ArticleEditModel', 'ArticleEditModel');
    }

    public function index()
    {
        if (!$this->session->userdata('user_data')) {
            show_error('로그인이 필요한 기능입니다.');
            return;
        }

        $parentId = $this->input->get('parentId');
        $boardId = $this->input->get('boardId');
        $prefix = $this->input->get('prefix');

        $rqMethod = empty($parentId) ? 'createArticle' : 'createReplyArticle';

        // 게시판 데이터 조회
        $boardRepo = $this->em->getRepository('Models\Entities\ArticleBoard');
        $boards = $boardRepo->findBy(['isDeleted' => 0], ['createDate' => 'DESC']);

        // ID가 6인 게시판 제외
        $boards = array_filter($boards, function ($board) {
            return $board->getId() !== "6";
        });

        $parentArticle = null;

        if ($parentId) {
            $parentArticle = $this->ArticleEditModel->getArticleById($parentId);
        }

        $page_view_data = [
            'title' => !empty($parentId) ? '답글 달기' : '카페 글쓰기',
            'boards' => $boards,
            'parentArticle' => $parentArticle,
        ];

        $this->layout->view('article/article_form', $page_view_data);
    }

    public function createArticle()
    {
        if (!$this->session->userdata('user_data')) {
            show_error('로그인이 필요한 기능입니다.');
            return;
        }

        $formData = [
            'boardId' => $this->input->post('board', TRUE),
            'prefix' => $this->input->post('prefix', TRUE),
            'title' => $this->input->post('title', TRUE),
            'content' => $this->input->post('content', TRUE),
            'parentId' => $this->input->post('parentId', TRUE),
            'publicScope' => $this->input->post('publicScope', TRUE),
            'memberId' => $this->session->userdata('user_data')['user_id'],
        ];

        if (empty($formData['parentId'])) {
            $result = $this->ArticleEditModel->processCreateNewArticle($formData);
        } else {
            $result = $this->ArticleEditModel->processCreateReplyArticle($formData);
        }

        if ($result['success']) {
            redirect('/article/articledetailcontroller/index/' . $result['articleId']);
        } else {
            $this->session->set_flashdata('error_messages', $result['errors']);
            redirect('back');
        }
    }
}
