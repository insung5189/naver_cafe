<?
defined('BASEPATH') or exit('No direct script access allowed');
class UserActivityController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('member/UserActivityModel', 'UserActivityModel');
    }
    public function index($memberId = NULL)
    {

        if ($memberId === null || !isset($memberId)) {
            $this->loadErrorView('잘못된 접근입니다.', '정상적인 경로로 접근해주세요.');
            return;
        } else if ($memberId === 'manager') {
            $memberId = 58;
        }
        $member = $this->em->getRepository('Models\Entities\Member')->find($memberId);

        if (isset($member)) {
            // 회원 정보 및 관련 데이터 로드
            $articleCount = count($this->UserActivityModel->getArticlesByMemberId($memberId));

            $currentPage = $this->input->get('page', TRUE) ?? 1;
            $articlesPerPage = $this->input->get('articlesPerPage', TRUE) ?? 10;
            $totalArticleCount = count($this->UserActivityModel->getArticlesByMemberId($memberId));
            $totalPages = ceil($totalArticleCount / $articlesPerPage);

            // 필요한 데이터 로드
            $articlesByPage = $this->UserActivityModel->getArticlesByMemberIdByPage($memberId, $currentPage, $articlesPerPage);

            // 목록에 표시되는 게시물의 댓글 갯수 확인하기
            $articlesByMemberId = $this->UserActivityModel->getArticlesByMemberId($memberId);
            $articleIds = array_map(function ($article) {
                return $article->getId();
            }, $articlesByMemberId);
            $commentCountByArticle = $this->UserActivityModel->getCommentCountForArticles($articleIds); // 해당 게시글의 댓글 갯수 데이터

            $parentArticlesExist = $this->UserActivityModel->checkParentArticlesExist($articlesByPage);

            // 페이지 데이터 준비
            $page_view_data = [
                'title' => '회원 활동내역',
                'member' => $member,
                'articleCount' => $articleCount,
                'articlesByPage' => $articlesByPage,
                'commentCountByArticle' => $commentCountByArticle,
                'parentArticlesExist' => $parentArticlesExist,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'articlesPerPage' => $articlesPerPage
            ];

            // 뷰 로드
            $this->layout->view('member/user_activity', $page_view_data);
        } else {
            // 회원 정보 없을 경우 오류 페이지 로드
            $this->loadErrorView('잘못된 접근입니다.', '사용자가 존재하지 않습니다.');
        }
    }

    /**
     * 나의활동 페이지에서 my_activity_my_articles_area.php 파일 로드하는 메서드(나의활동 초기탭)
     */

    public function loadUserArticles()
    {
        // 작성글 탭의 데이터 로드 및 뷰 렌더링
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {
                $memberId = $this->input->get('memberId', TRUE) ?? null;

                // 작성글 탭의 페이지네이션 관련 데이터
                $currentPage = $this->input->get('page', TRUE) ?? 1;
                $articlesPerPage = $this->input->get('articlesPerPage', TRUE) ?? 10;
                $totalArticleCount = count($this->UserActivityModel->getArticlesByMemberId($memberId));
                $totalPages = ceil($totalArticleCount / $articlesPerPage);

                // 필요한 데이터 로드
                $articlesByPage = $this->UserActivityModel->getArticlesByMemberIdByPage($memberId, $currentPage, $articlesPerPage);

                // 목록에 표시되는 게시물의 댓글 갯수 확인하기
                $articlesByMemberId = $this->UserActivityModel->getArticlesByMemberId($memberId);
                $articleIds = array_map(function ($article) {
                    return $article->getId();
                }, $articlesByMemberId);
                $commentCountByArticle = $this->UserActivityModel->getCommentCountForArticles($articleIds); // 해당 게시글의 댓글 갯수 데이터

                $parentArticlesExist = $this->UserActivityModel->checkParentArticlesExist($articlesByPage);

                // 데이터를 배열로 변환
                $userActivityMyArticlesData = [
                    'articlesByPage' => $articlesByPage,
                    'commentCountByArticle' => $commentCountByArticle,
                    'parentArticlesExist' => $parentArticlesExist,
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                    'articlesPerPage' => $articlesPerPage
                ];

                $html = $this->load->view('member/user_activity_user_articles_area', $userActivityMyArticlesData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '작성글 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            $this->loadErrorView('잘못된 접근입니다.', '정상적인 경로로 접근해주세요.');
        }
    }

    public function loadUserCommentedArticles()
    {
        // 댓글단 글 탭의 데이터 로드 및 뷰 렌더링
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {
                $memberId = $this->input->get('memberId', TRUE) ?? null;

                // 댓글단 글 탭의 페이지네이션 관련 데이터
                $currentPage = $this->input->get('page', TRUE) ?? 1;
                $commentedArticlesPerPage = $this->input->get('commentedArticlesPerPage', TRUE) ?? 10;
                $totalCommentedArticlesByMemberIdCount = count($this->UserActivityModel->getArticlesCommentedByMember($memberId));
                $totalPages = ceil($totalCommentedArticlesByMemberIdCount / $commentedArticlesPerPage);

                // 필요한 데이터 로드 로직
                $commentedArticlesByMemberIdAndPage = $this->UserActivityModel->getArticlesCommentedByMemberIdAndPage($memberId, $currentPage, $commentedArticlesPerPage);

                // 목록에 표시되는 게시물의 댓글 갯수 확인하기
                $articlesCommentedByMemberId = $this->UserActivityModel->getArticlesCommentedByMember($memberId);
                $articleIds = array_map(function ($commentedArticle) {
                    return $commentedArticle->getId();
                }, $articlesCommentedByMemberId);
                $commentCountByArticle = $this->UserActivityModel->getCommentCountForArticles($articleIds); // 해당 게시글의 댓글 갯수 데이터

                $parentArticlesExist = $this->UserActivityModel->checkParentArticlesExist($commentedArticlesByMemberIdAndPage);

                // 데이터를 배열로 변환하는 로직
                $userActivityMyCommentedArticlesData = [
                    'commentedArticlesByMemberIdAndPage' => $commentedArticlesByMemberIdAndPage,
                    'commentCountByArticle' => $commentCountByArticle,
                    'parentArticlesExist' => $parentArticlesExist,
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                    'commentedArticlesPerPage' => $commentedArticlesPerPage
                ];
                $html = $this->load->view('member/user_activity_commented_articles_area', $userActivityMyCommentedArticlesData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '댓글단 글 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            $this->loadErrorView('잘못된 접근입니다.', '정상적인 경로로 접근해주세요.');
        }
    }

    public function loadErrorView($title, $message)
    {
        $page_view_data = [
            'title' => $title ?? '잘못된 접근입니다.',
            'message' => $message ?? '정상적인 경로로 접근해주세요.',
        ];
        $this->layout->view('errors/error_page', $page_view_data);
    }
}
