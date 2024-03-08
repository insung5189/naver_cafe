<?
defined('BASEPATH') or exit('No direct script access allowed');
class MyActivityController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('member/MyActivityModel', 'MyActivityModel');
    }
    public function index()
    {
        // 페이지 번호와 기본 설정
        $currentPage = $this->input->get('page') ?? 1;
        $articlesPerPage = $this->input->get('articlesPerPage') ? (int)$this->input->get('articlesPerPage') : 10;

        $userData = $this->session->userdata('user_data');
        $memberId = isset($userData['user_id']) ? $userData['user_id'] : null;

        if ($memberId) {
            // 회원 정보 및 관련 데이터 로드
            $member = $this->em->getRepository('Models\Entities\Member')->find($memberId);
            $totalArticleCount = $this->MyActivityModel->getArticleCountByMemberId($memberId);
            $totalPages = ceil($totalArticleCount / $articlesPerPage);

            // '작성글' 탭의 초기 콘텐츠 로드
            $initialTabContent = $this->loadInitialTabContent($memberId, $currentPage, $articlesPerPage);

            /**
             * 
            // 회원 관련 데이터 로드
            $articlesByPage = $this->MyActivityModel->getArticlesByMemberIdByPage($memberId, $currentPage, $articlesPerPage);
            $commentsByPage = $this->MyActivityModel->getCommentsByMemberIdByPage($memberId, $currentPage, $articlesPerPage);
            $commentIds = array_map(function ($comment) {
                return $comment->getId();
            }, $commentsByPage);
            $commentCountByArticle = $this->MyActivityModel->getCommentCountByMemberArticles($commentIds);
            $commentedArticlesByMemberId = $this->MyActivityModel->getArticlesCommentedByMember($memberId);
            $deletedArticles = $this->MyActivityModel->getDeletedArticlesByMemberIdByPage($memberId, $currentPage, $articlesPerPage);
            $totalArticleCount = $this->MyActivityModel->getArticleCountByMemberId($memberId);
            $totalPages = ceil($totalArticleCount / $articlesPerPage);
             */

            // 페이지 데이터 준비
            $page_view_data = [
                'title' => '나의 활동',
                'member' => $member,
                'initialTabContent' => $initialTabContent,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'articlesPerPage' => $articlesPerPage,
            ];

            // 뷰 로드
            $this->layout->view('member/my_activity', $page_view_data);
        } else {
            // 회원 정보 없을 경우 오류 페이지 로드
            $this->layout->view('errors/error_page', ['title' => '오류 발생']);
        }
    }

    /**
     * 나의활동 페이지에서 my_activity_my_articles_area.php 파일 로드하는 메서드(나의활동 초기탭)
     */
    private function loadInitialTabContent($memberId, $currentPage, $articlesPerPage)
    {
        $articlesByPage = $this->MyActivityModel->getArticlesByMemberIdByPage($memberId, $currentPage, $articlesPerPage);
        $totalArticleCount = $this->MyActivityModel->getArticleCountByMemberId($memberId);
        $totalPages = ceil($totalArticleCount / $articlesPerPage);

        $myActivityMyArticlesData = [
            'articlesByPage' => $articlesByPage,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'articlesPerPage' => $articlesPerPage
        ];

        // '작성글' 탭의 콘텐츠 로드 및 반환
        return $this->load->view('member/my_activity_my_articles_area', $myActivityMyArticlesData, TRUE);
    }

    public function loadMyArticles()
    {
        // 작성글 탭의 데이터 로드 및 뷰 렌더링
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {
                $userData = $this->session->userdata('user_data');
                $memberId = isset($userData['user_id']) ? $userData['user_id'] : null;
                $currentPage = $this->input->get('page', TRUE) ?? 1;
                $articlesPerPage = $this->input->get('articlesPerPage', TRUE) ?? 10;
                $totalArticleCount = $this->MyActivityModel->getArticleCountByMemberId($memberId);
                $totalPages = ceil($totalArticleCount / $articlesPerPage);

                // 필요한 데이터 로드 로직
                $articlesByPage = $this->MyActivityModel->getArticlesByMemberIdByPage($memberId, $currentPage, $articlesPerPage);

                // 데이터를 배열로 변환하는 로직
                $myActivityMyArticlesData = [
                    'articlesByPage' => $articlesByPage,
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                    'articlesPerPage' => $articlesPerPage
                ];

                $html = $this->load->view('member/my_activity_my_articles_area', $myActivityMyArticlesData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '작성글 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }

    public function loadMyComments()
    {
        // 작성댓글 탭의 데이터 로드 및 뷰 렌더링
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {
                $userData = $this->session->userdata('user_data');
                $memberId = isset($userData['user_id']) ? $userData['user_id'] : null;
                $currentPage = $this->input->get('page', TRUE) ?? 1;
                $commentsPerPage = $this->input->get('commentsPerPage', TRUE) ?? 10;
                $totalCommentCount = $this->MyActivityModel->getCommentCountByMemberId($memberId);
                $totalPages = ceil($totalCommentCount / $commentsPerPage);

                // 필요한 데이터 로드 로직
                $commentsByPage = $this->MyActivityModel->getCommentsByMemberIdByPage($memberId, $currentPage, $commentsPerPage);
                $commentsByMemberId = $this->MyActivityModel->getCommentsByMemberId($memberId);
                $commentIds = array_map(function ($comment) {
                    return $comment->getId();
                }, $commentsByMemberId);
                $commentCountByArticle = $this->MyActivityModel->getCommentCountByMemberArticles($commentIds);

                // 데이터를 배열로 변환하는 로직
                $myActivityMyCommentsData = [
                    'commentsByPage' => $commentsByPage,
                    'commentCountByArticle' => $commentCountByArticle,
                    'commentFileUrl' => "/assets/file/commentFiles/img/",
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                    'commentsPerPage' => $commentsPerPage
                ];

                $html = $this->load->view('member/my_activity_my_comments_area', $myActivityMyCommentsData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '작성댓글 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }

    public function loadMyCommentedArticles()
    {
        // 이 메서드에서 댓글단 글 탭의 데이터 로드 및 뷰 렌더링 로직 구현
    }

    public function loadMyLikedArticles()
    {
        // 이 메서드에서 좋아요한 글 탭의 데이터 로드 및 뷰 렌더링 로직 구현
    }

    public function loadMyDeletedArticles()
    {
        // 이 메서드에서 삭제한 게시글 탭의 데이터 로드 및 뷰 렌더링 로직 구현
    }

    public function fetchArticles()
    {
        // AJAX 요청 검사
        if ($this->input->is_ajax_request()) {
            $userData = $this->session->userdata('user_data');
            $memberId = isset($userData['user_id']) ? $userData['user_id'] : null;
            $currentPage = $this->input->get('page', TRUE) ?? 1;
            $articlesPerPage = $this->input->get('articlesPerPage', TRUE) ?? 10;

            $articlesByPage = $this->MyActivityModel->getArticlesByMemberIdByPage($memberId, $currentPage, $articlesPerPage);
            $totalArticleCount = $this->MyActivityModel->getArticleCountByMemberId($memberId);
            $totalPages = ceil($totalArticleCount / $articlesPerPage);

            // 데이터를 배열로 변환
            $myActivityMyArticlesData = [
                'articlesByPage' => $articlesByPage,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'articlesPerPage' => $articlesPerPage
            ];

            $html = $this->load->view('member/my_activity_my_articles_area', $myActivityMyArticlesData, TRUE);

            // 데이터를 JSON 형태로 반환
            echo json_encode(['success' => true, 'html' => $html]);
        } else {
            show_404();
        }
    }

    public function myActivityArticlesSoftDelete()
    {
        $articleIds = $this->input->post('articles');

        if (!empty($articleIds) && is_array($articleIds)) {
            $result = $this->MyActivityModel->softDeleteArticles($articleIds);

            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => '게시글 삭제 실패']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => '선택된 게시글이 없습니다.']);
        }
    }
}
