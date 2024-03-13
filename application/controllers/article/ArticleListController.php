<?
defined('BASEPATH') or exit('No direct script access allowed');
class ArticleListController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article/ArticleListModel', 'ArticleListModel');
    }

    public function index($boardId)
    {
        $keyword = $this->input->get('keyword', TRUE) ?? '';
        // $boardId = $this->input->get('boardId', TRUE);

        $page_view_data = [
            'title' => !empty($keyword) ? '검색 결과' : '전체글보기',
            'boardId' => $boardId
        ];

        // 데이터와 함께 뷰 로드
        $this->layout->view('article/article_list_by_board', $page_view_data);
    }



    public function loadFreeBoardAjax()
    {
        // 자유게시판의 목록 로드
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {
                $boardId = $this->input->get('boardId', TRUE) ?? 1;
                $articleBoard = $this->em->find('Models\Entities\ArticleBoard', $boardId);

                $freeBoardListData = [
                    'articleBoard' => $articleBoard,
                    'title' => '게시판이름',
                    'boardGuide' => '게시판설명',
                    'errors' => $errors ?? []
                ];

                $html = $this->load->view('article/article_list_by_board_content', $freeBoardListData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '데이터를 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }

    // 공통 로직을 처리하는 메서드
    public function loadBoard()
    {
        if ($this->input->is_ajax_request()) {
            try {
                $boardId = $this->input->get('boardId', TRUE) ?? 1;
                $articleBoard = $this->em->find('Models\Entities\ArticleBoard', $boardId);

                if ($boardId === "1") {
                    $title = '자유게시판';
                    $boardGuide = '자유로운 주제로 글을 작성해주세요.';
                } else if ($boardId === "2") {
                    $title = '건의게시판';
                    $boardGuide = '팀에게 건의하고 싶은 것을 작성합니다.';
                } else if ($boardId === "3") {
                    $title = '아무말게시판';
                    $boardGuide = '아무말이나 하는 게시판입니다.';
                } else if ($boardId === "4") {
                    $title = '지식공유게시판';
                    $boardGuide = '개발 관련 지식을 공유해주세요.';
                } else if ($boardId === "5") {
                    $title = '질문/답변게시판';
                    $boardGuide = '개발 관련 질문을 자유롭게 해주세요.';
                } else {
                    $title = '잘못된 접근';
                    $boardGuide = '기존의 게시판 이외의 게시판으로 접근하셨습니다.';
                }

                $currentPage = $this->input->get('page', TRUE) ?? 1;
                $articlesPerPage = $this->input->get('articlesPerPage', TRUE) ? (int)$this->input->get('articlesPerPage') : 15;
                $keyword = $this->input->get('keyword', TRUE) ?? '';
                $element = $this->input->get('element', TRUE) ?? '';
                $period = $this->input->get('period', TRUE) ?? '';
                $startDate = $this->input->get('startDate', TRUE) ?? '';
                $endDate = $this->input->get('endDate', TRUE) ?? '';

                if (!empty($keyword) && !empty($period) || !empty($startDate) || !empty($endDate)) {

                    $searchResults = $this->ArticleListModel->searchArticles($boardId, $keyword, $element, $period, $startDate, $endDate, $currentPage, $articlesPerPage);

                    if (isset($searchResults['errors'])) {
                        $errors = $searchResults['errors'];
                        $articles = $this->ArticleListModel->getArticlesByBoardIdAndPage($boardId, $currentPage, $articlesPerPage);
                        $totalArticleCount = count($this->ArticleListModel->getAllArticlesByBoardId($boardId));
                    } else {
                        $articles = $searchResults['results'];
                        $totalArticleCount = $searchResults['total'];
                    }
                } else {
                    $articles = $this->ArticleListModel->getArticlesByBoardIdAndPage($boardId, $currentPage, $articlesPerPage);
                    $totalArticleCount = count($this->ArticleListModel->getAllArticlesByBoardId($boardId));
                }

                $totalPages = ceil($totalArticleCount / $articlesPerPage);
                $parentArticlesExist = $this->ArticleListModel->checkParentArticlesExist($articles);

                // 게시글 ID 배열 생성
                $articleIds = array_map(function ($article) {
                    return $article->getId();
                }, $articles);

                // 게시글별 댓글 개수 조회
                $commentCounts = $this->ArticleListModel->getCommentCountForArticles($articleIds);

                $boardListData = [
                    'articleBoard' => $articleBoard,
                    'title' => !empty($keyword) ? '검색 결과' : $title,
                    'boardGuide' => !empty($keyword) ? '검색조건을 이용한 검색결과입니다.' : $boardGuide,
                    'articles' => $articles,
                    'commentCounts' => $commentCounts,
                    'totalArticleCountAll' => $totalArticleCount,
                    'parentArticlesExist' => $parentArticlesExist,
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

                $html = $this->load->view('article/article_list_by_board_content', $boardListData, TRUE);

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
