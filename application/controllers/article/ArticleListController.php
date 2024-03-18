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

        $boardNames = [
            1 => '자유게시판',
            2 => '건의게시판',
            3 => '아무말게시판',
            4 => '지식공유',
            5 => '질문/답변게시판',
        ];

        if (!empty($keyword)) {
            $title = '검색 결과';
        } elseif (array_key_exists($boardId, $boardNames)) {
            $title = $boardNames[$boardId];
        } else {
            $title = '잘못된 접근';
        }

        $page_view_data = [
            'title' => $title,
            'boardId' => $boardId
        ];

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

                $memberId = $this->session->userdata('user_data') ? $this->session->userdata('user_data')['user_id'] : NULL;
                $boardId = $this->input->get('boardId', TRUE) ?? NULL;

                if (isset($memberId) && isset($boardId)) {
                    $isBookmarked = $this->ArticleListModel->isBookmarkedByMember($memberId, $boardId);
                } else {
                    $isBookmarked = FALSE;
                }

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
                    return;
                }
                $articleBoard = $this->em->find('Models\Entities\ArticleBoard', $boardId);

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
                    'isBookmarked' => $isBookmarked,
                    // 'likes' => $likes, 각 게시글의 좋아요 수 확인 후 입력예정
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

    public function processBookMark()
    {
        if ($this->input->is_ajax_request()) {
            $boardId = $this->input->post('boardId');
            $memberId = $this->input->post('memberId');
            $isBookmarked = $this->input->post('isBookmarked');

            $board = $this->em->find('Models\Entities\ArticleBoard', $boardId);
            $member = $this->em->find('Models\Entities\Member', $memberId);

            if ($board && $member) {
                $bookmark = $this->em->getRepository('Models\Entities\BoardBookmark')->findOneBy([
                    'articleBoard' => $board,
                    'member' => $member
                ]);

                if ($bookmark) {
                    $this->em->remove($bookmark);
                    $message = '즐겨찾기가 해제되었습니다.';
                    $isBookmarked = false;
                } else {
                    // 즐겨찾기 추가
                    $newBookmark = new Models\Entities\BoardBookmark();
                    $newBookmark->setArticleBoard($board);
                    $newBookmark->setMember($member);
                    $newBookmark->setCreateDate(new \DateTime());

                    $this->em->persist($newBookmark);
                    $message = '게시판이 즐겨찾기에 추가되었습니다.';
                    $isBookmarked = true;
                }

                $this->em->flush();
                echo json_encode(['success' => true, 'message' => $message, 'isBookmarked' => $isBookmarked]);
            } else {
                echo json_encode(['success' => false, 'message' => '게시판 또는 사용자를 찾을 수 없습니다.']);
            }
        } else {
            show_404();
        }
    }
}
