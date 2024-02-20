<?
defined('BASEPATH') or exit('No direct script access allowed');
class ArticleListController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article/ArticleListModel', 'ArticleListModel');
        $this->load->library('doctrine');
        $this->em = $this->doctrine->em;
    }

    public function index()
    {
        $currentPage = $this->input->get('page') ?? 1;
        $articlesPerPage = isset($_GET['articlesPerPage']) ? (int)$_GET['articlesPerPage'] : 5;

        $totalArticleCount = $this->ArticleListModel->getTotalArticleCount();
        $totalPages = ceil($totalArticleCount / $articlesPerPage);

        $articles = $this->ArticleListModel->getArticlesByPage($currentPage, $articlesPerPage);

        $page_view_data = [
            'title' => '전체글보기',
            'articles' => $articles,
            'totalArticleCount' => $totalArticleCount,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'articlesPerPage' => $articlesPerPage
        ];

        $this->layout->view('article/article_list_all', $page_view_data);
    }
}
