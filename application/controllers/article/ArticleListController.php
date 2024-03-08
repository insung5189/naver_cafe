<?
defined('BASEPATH') or exit('No direct script access allowed');
class ArticleListController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article/ArticleListModel', 'ArticleListModel');
    }

    public function index()
    {
        $currentPage = $this->input->get('page') ?? 1;
        $articlesPerPage = $this->input->get('articlesPerPage') ? (int)$this->input->get('articlesPerPage') : 15;
        $keyword = $this->input->get('keyword');
        $element = $this->input->get('element');
        $period = $this->input->get('period');
        $startDate = $this->input->get('startDate');
        $endDate = $this->input->get('endDate');

        if (!empty($keyword) || !empty($period) || !empty($startDate) || !empty($endDate)) {

            $result = $this->ArticleListModel->searchArticles($keyword, $element, $period, $startDate, $endDate, $currentPage, $articlesPerPage);

            if (isset($result['errors'])) {
                $errors = $result['errors'];
                $articles = $this->ArticleListModel->getArticlesByPage($currentPage, $articlesPerPage);
                $totalArticleCount = $this->ArticleListModel->getTotalArticleCount();
            } else {
                $articles = $result;
                $totalArticleCount = $this->ArticleListModel->getTotalArticleCountWithSearch($keyword, $element, $period, $startDate, $endDate);
            }
        } else {
            $articles = $this->ArticleListModel->getArticlesByPage($currentPage, $articlesPerPage);
            $totalArticleCount = $this->ArticleListModel->getTotalArticleCount();
        }

        // 게시글 ID 배열 생성
        $articleIds = array_map(function ($article) {
            return $article->getId();
        }, $articles);

        // 게시글별 댓글 개수 조회
        $commentCounts = $this->ArticleListModel->getCommentCountForArticles($articleIds);

        $totalPages = ceil($totalArticleCount / $articlesPerPage);

        $page_view_data = [
            'title' => !empty($keyword) ? '검색 결과' : '전체글보기',
            'articles' => $articles,
            'commentCounts' => $commentCounts,
            'totalArticleCountAll' => $totalArticleCount,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'articlesPerPage' => $articlesPerPage,
            'keyword' => $keyword,
            'element' => $element,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'errors' => $errors ?? []
        ];

        // 데이터와 함께 뷰 로드
        $this->layout->view('article/article_list_all', $page_view_data);
    }
}
