<?
defined('BASEPATH') or exit('No direct script access allowed');
class ArticleListAllController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article/ArticleListAllModel', 'ArticleListAllModel');
    }

    // 기존 로직
    public function index()
    {
        $keyword = $this->input->get('keyword', TRUE) ?? '';

        $page_view_data = [
            'title' => !empty($keyword) ? '검색 결과' : '전체글보기',
        ];

        // 데이터와 함께 뷰 로드
        $this->layout->view('article/article_list_all', $page_view_data);
    }

    public function fetchArticles()
    {
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {

                $currentPage = $this->input->get('page', TRUE) ?? 1;
                $articlesPerPage = $this->input->get('articlesPerPage', TRUE) ? (int)$this->input->get('articlesPerPage') : 15;
                $keyword = $this->input->get('keyword', TRUE) ?? '';
                $element = $this->input->get('element', TRUE) ?? '';
                $period = $this->input->get('period', TRUE) ?? '';
                $startDate = $this->input->get('startDate', TRUE) ?? '';
                $endDate = $this->input->get('endDate', TRUE) ?? '';

                if (!empty($keyword) || !empty($period) || !empty($startDate) || !empty($endDate)) {

                    $result = $this->ArticleListAllModel->searchArticles($keyword, $element, $period, $startDate, $endDate, $currentPage, $articlesPerPage);

                    if (isset($result['errors'])) {
                        $errors = $result['errors'];
                        $articles = $this->ArticleListAllModel->getArticlesByPage($currentPage, $articlesPerPage);
                        $totalArticleCount = $this->ArticleListAllModel->getTotalArticleCount();
                    } else {
                        $articles = $result;
                        $totalArticleCount = $this->ArticleListAllModel->getTotalArticleCountWithSearch($keyword, $element, $period, $startDate, $endDate);
                    }
                } else {
                    $articles = $this->ArticleListAllModel->getArticlesByPage($currentPage, $articlesPerPage);
                    $totalArticleCount = $this->ArticleListAllModel->getTotalArticleCount();
                }

                // 게시글 ID 배열 생성
                $articleIds = array_map(function ($article) {
                    return $article->getId();
                }, $articles);

                // 게시글별 댓글 개수 조회
                $commentCounts = $this->ArticleListAllModel->getCommentCountForArticles($articleIds);

                $totalPages = ceil($totalArticleCount / $articlesPerPage);

                $page_view_data = [
                    'title' => !empty($keyword) ? '검색 결과' : '전체글보기',
                    'articles' => $articles,
                    'commentCounts' => $commentCounts,
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                    'articlesPerPage' => $articlesPerPage,
                    'totalArticleCountAll' => $totalArticleCount,
                    'keyword' => $keyword,
                    'element' => $element,
                    'period' => $period,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'errors' => $errors ?? []
                ];

                $html = $this->load->view('article/article_list_all_content', $page_view_data, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '데이터를 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }
}