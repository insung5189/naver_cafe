<?
defined('BASEPATH') or exit('No direct script access allowed');
class ArticleListController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article/ArticleListModel', 'ArticleListModel');
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
                    'errors' => $errors ?? [] // 에러처리
                ];

                $html = $this->load->view('article/article_list', $freeBoardListData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '데이터를 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }

    // 자유게시판을 로드하는 메서드
    public function loadFreeBoard()
    {
        $this->loadBoard(1, '자유게시판', '자유로운 주제로 글을 작성해주세요.', 'loadFreeBoard');
    }

    // 건의게시판을 로드하는 메서드
    public function loadSuggestedBoard()
    {
        $this->loadBoard(2, '건의게시판', '팀에게 건의하고 싶은 것을 작성합니다.', 'loadSuggestedBoard');
    }

    // 아무말게시판을 로드하는 메서드
    public function loadWordVomitBoard()
    {
        $this->loadBoard(3, '아무말게시판', '아무말이나 하는 게시판입니다.', 'loadWordVomitBoard');
    }

    // 지식공유게시판을 로드하는 메서드
    public function loadKnowledgeSharingBoard()
    {
        $this->loadBoard(4, '지식공유게시판', '개발 관련 지식을 공유해주세요.', 'loadKnowledgeSharingBoard');
    }

    // 질문/답변게시판을 로드하는 메서드
    public function loadQnaBoard()
    {
        $this->loadBoard(5, '질문/답변게시판', '개발 관련 질문을 자유롭게 해주세요.', 'loadQnaBoard');
    }

    // 공통 로직을 처리하는 메서드
    private function loadBoard($boardId, $title, $boardGuide, $methodName)
    {
        $articleBoard = $this->em->find('Models\Entities\ArticleBoard', $boardId);
        $currentPage = $this->input->get('page') ?? 1;
        $articlesPerPage = $this->input->get('articlesPerPage') ? (int)$this->input->get('articlesPerPage') : 15;
        $keyword = $this->input->get('keyword');
        $element = $this->input->get('element');
        $period = $this->input->get('period');
        $startDate = $this->input->get('startDate');
        $endDate = $this->input->get('endDate');

        if (!empty($keyword) || !empty($period) || !empty($startDate) || !empty($endDate)) {

            $result = $this->ArticleListAllModel->searchArticles($keyword, $element, $period, $startDate, $endDate, $currentPage, $articlesPerPage);
        }

        $articles = $this->ArticleListModel->getArticlesByBoardIdAndPage($boardId, $currentPage, $articlesPerPage);
        $totalArticleCount = count($this->ArticleListModel->getAllArticlesByBoardId($boardId));
        $commentCounts = $this->ArticleListModel->getCommentCountForArticles(array_map(function ($article) {
            return $article->getId();
        }, $articles));
        $totalPages = ceil($totalArticleCount / $articlesPerPage);

        $page_view_data = [
            'articleBoard' => $articleBoard,
            'title' => $title,
            'boardGuide' => $boardGuide,
            'methodName' => $methodName,
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
            'errors' => []
        ];

        // 사용 레이아웃에 데이터를 전달하여 뷰 로드
        $this->layout->view('article/article_list', $page_view_data);
    }
}
