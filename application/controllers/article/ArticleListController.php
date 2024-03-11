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
        $page_view_data = [
            // 'articleBoard' => $articleBoard,
            'title' => '자유게시판',
            // 'boardGuide' => '자유로운 주제로 글을 작성해주세요',
            // 'articles' => $articles,
            // 'commentCounts' => $commentCounts,
            // 'totalArticleCountAll' => $totalArticleCount,
            // 'currentPage' => $currentPage,
            // 'totalPages' => $totalPages,
            // 'articlesPerPage' => $articlesPerPage,
            // 'keyword' => $keyword,
            // 'element' => $element,
            // 'period' => $period,
            // 'startDate' => $startDate,
            // 'endDate' => $endDate,
            'errors' => $errors ?? [] // 에러처리
        ];
        // 데이터와 함께 뷰 로드
        $this->layout->view('article/article_list_by_board', $page_view_data);
    }

    public function loadFreeBoard()
    {
        // 자유게시판의 목록 로드
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {

                $boardId = $this->input->get('boardId', TRUE) ?? 1;
                $articleBoard = $this->em->find('Models\Entities\ArticleBoard', $boardId);

                $currentPage = $this->input->get('page') ?? 1;
                $articlesPerPage = $this->input->get('articlesPerPage') ? (int)$this->input->get('articlesPerPage') : 15;
                $keyword = $this->input->get('keyword');
                $element = $this->input->get('element');
                $period = $this->input->get('period');
                $startDate = $this->input->get('startDate');
                $endDate = $this->input->get('endDate');

                $articles = $this->ArticleListModel->getArticlesByBoardIdAndPage($boardId, $currentPage, $articlesPerPage);
                $totalArticleCount = count($this->ArticleListModel->getAllArticlesByBoardId($boardId));

                // 게시글 ID 배열 생성
                $articleIds = array_map(function ($article) {
                    return $article->getId();
                }, $articles);

                // 게시글별 댓글 개수 조회
                $commentCounts = $this->ArticleListModel->getCommentCountForArticles($articleIds);

                $totalPages = ceil($totalArticleCount / $articlesPerPage);

                $freeBoardListData = [
                    'articleBoard' => $articleBoard,
                    'title' => '자유게시판',
                    'boardGuide' => '자유로운 주제로 글을 작성해주세요',
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
                    'errors' => $errors ?? [] // 에러처리
                ];

                $html = $this->load->view('article/article_list', $freeBoardListData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '자유게시판 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }

    public function loadSuggestedBoard()
    {
        // 건의게시판의 목록 로드
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {

                $suggestedBoardListData = [
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                ];

                $html = $this->load->view('article/article_list', $suggestedBoardListData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '건의게시판 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }

    public function loadWordVomitBoard()
    {
        // 아무말게시판의 목록 로드
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {

                $wordVomitBoardListData = [
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                ];

                $html = $this->load->view('article/article_list', $wordVomitBoardListData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '아무말게시판 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }

    public function loadKnowledgeSharingBoard()
    {
        // 지식공유게시판의 목록 로드
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {

                $knowledgeSharingBoardListData = [
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                ];

                $html = $this->load->view('article/article_list', $knowledgeSharingBoardListData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '지식공유게시판 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }

    public function loadQnaBoard()
    {
        // 질문/답변게시판의 목록 로드
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {

                $qnaBoardListData = [
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                ];

                $html = $this->load->view('article/article_list', $qnaBoardListData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '질문/답변게시판 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }
}
