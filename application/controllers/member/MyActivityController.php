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
        $userData = $this->session->userdata('user_data');
        $memberId = isset($userData['user_id']) ? $userData['user_id'] : null;

        if ($memberId) {
            // 회원 정보 및 관련 데이터 로드
            $member = $this->em->getRepository('Models\Entities\Member')->find($memberId);

            // 페이지 데이터 준비
            $page_view_data = [
                'title' => '나의 활동',
                'member' => $member,
                // 'initialTabContent' => $initialTabContent,
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

    public function loadMyArticles()
    {
        // 작성글 탭의 데이터 로드 및 뷰 렌더링
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {
                $userData = $this->session->userdata('user_data');
                $memberId = isset($userData['user_id']) ? $userData['user_id'] : null;

                // 작성글 탭의 페이지네이션 관련 데이터
                $currentPage = $this->input->get('page', TRUE) ?? 1;
                $articlesPerPage = $this->input->get('articlesPerPage', TRUE) ?? 10;
                $totalArticleCount = count($this->MyActivityModel->getArticlesByMemberId($memberId));
                $totalPages = ceil($totalArticleCount / $articlesPerPage);

                // 필요한 데이터 로드
                $articlesByPage = $this->MyActivityModel->getArticlesByMemberIdByPage($memberId, $currentPage, $articlesPerPage);

                // 목록에 표시되는 게시물의 댓글 갯수 확인하기
                $articlesByMemberId = $this->MyActivityModel->getArticlesByMemberId($memberId);
                $articleIds = array_map(function ($article) {
                    return $article->getId();
                }, $articlesByMemberId);
                $commentCountByArticle = $this->MyActivityModel->getCommentCountForArticles($articleIds); // 해당 게시글의 댓글 갯수 데이터

                $parentArticlesExist = $this->MyActivityModel->checkParentArticlesExist($articlesByPage);

                // 데이터를 배열로 변환
                $myActivityMyArticlesData = [
                    'articlesByPage' => $articlesByPage,
                    'commentCountByArticle' => $commentCountByArticle,
                    'parentArticlesExist' => $parentArticlesExist,
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

                // 작성댓글 탭의 페이지네이션 관련 데이터
                $currentPage = $this->input->get('page', TRUE) ?? 1;
                $commentsPerPage = $this->input->get('commentsPerPage', TRUE) ?? 10;
                $totalCommentCount = count($this->MyActivityModel->getCommentsByMemberId($memberId));
                $totalPages = ceil($totalCommentCount / $commentsPerPage);

                // 필요한 데이터 로드 로직
                $commentsByPage = $this->MyActivityModel->getCommentsByMemberIdByPage($memberId, $currentPage, $commentsPerPage);

                // 목록에 표시되는 게시물의 댓글 갯수 확인하기
                $commentsByMemberId = $this->MyActivityModel->getArticlesCommentedByMember($memberId);
                $articleIds = array_map(function ($comment) {
                    return $comment->getId();
                }, $commentsByMemberId);
                $commentCountByArticle = $this->MyActivityModel->getCommentCountForArticles($articleIds); // 해당 게시글의 댓글 갯수 데이터

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
        // 댓글단 글 탭의 데이터 로드 및 뷰 렌더링
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {
                $userData = $this->session->userdata('user_data');
                $memberId = isset($userData['user_id']) ? $userData['user_id'] : null;

                // 댓글단 글 탭의 페이지네이션 관련 데이터
                $currentPage = $this->input->get('page', TRUE) ?? 1;
                $commentedArticlesPerPage = $this->input->get('commentedArticlesPerPage', TRUE) ?? 10;
                $totalCommentedArticlesByMemberIdCount = count($this->MyActivityModel->getArticlesCommentedByMember($memberId));
                $totalPages = ceil($totalCommentedArticlesByMemberIdCount / $commentedArticlesPerPage);

                // 필요한 데이터 로드 로직
                $commentedArticlesByMemberIdAndPage = $this->MyActivityModel->getArticlesCommentedByMemberIdAndPage($memberId, $currentPage, $commentedArticlesPerPage);

                // 목록에 표시되는 게시물의 댓글 갯수 확인하기
                $articlesCommentedByMemberId = $this->MyActivityModel->getArticlesCommentedByMember($memberId);
                $articleIds = array_map(function ($commentedArticle) {
                    return $commentedArticle->getId();
                }, $articlesCommentedByMemberId);
                $commentCountByArticle = $this->MyActivityModel->getCommentCountForArticles($articleIds); // 해당 게시글의 댓글 갯수 데이터

                $parentArticlesExist = $this->MyActivityModel->checkParentArticlesExist($commentedArticlesByMemberIdAndPage);

                // 데이터를 배열로 변환하는 로직
                $myActivityMyCommentedArticlesData = [
                    'commentedArticlesByMemberIdAndPage' => $commentedArticlesByMemberIdAndPage,
                    'commentCountByArticle' => $commentCountByArticle,
                    'parentArticlesExist' => $parentArticlesExist,
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                    'commentedArticlesPerPage' => $commentedArticlesPerPage
                ];
                $html = $this->load->view('member/my_activity_my_commented_articles_area', $myActivityMyCommentedArticlesData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '댓글단 글 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }

    public function loadMyLikedArticles()
    {
        // 좋아요한 글 탭의 데이터 로드 및 뷰 렌더링
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {
                $userData = $this->session->userdata('user_data');
                $memberId = isset($userData['user_id']) ? $userData['user_id'] : null;

                // 작성글 탭의 페이지네이션 관련 데이터
                $currentPage = $this->input->get('page', TRUE) ?? 1;
                $likedArticlesPerPage = $this->input->get('likedArticlesPerPage', TRUE) ?? 10;

                // 필요한 데이터 로드
                $likedArticlesByPage = $this->MyActivityModel->getLikedArticlesByMemberIdWithCount($memberId, $currentPage, $likedArticlesPerPage);

                $totalPages = ceil($likedArticlesByPage['totalCountLikedArticles'] / $likedArticlesPerPage);

                // 목록에 표시되는 게시물의 댓글 갯수 확인하기
                $likedArticlesByMemberId = $this->MyActivityModel->getAllLikedArticlesByMemberId($memberId);
                $articleIds = array_map(function ($likedArticle) {
                    return $likedArticle->getId();
                }, $likedArticlesByMemberId);
                $commentCountByArticle = $this->MyActivityModel->getCommentCountForArticles($articleIds); // 해당 게시글의 댓글 갯수 데이터

                $parentArticlesExist = $this->MyActivityModel->checkParentArticlesExist($likedArticlesByPage['articlesByPage']);

                // 데이터를 배열로 변환
                $myActivityMyLikedArticlesData = [
                    'likeArticlesByPage' => $likedArticlesByPage['articlesByPage'],
                    'commentCountByArticle' => $commentCountByArticle,
                    'parentArticlesExist' => $parentArticlesExist,
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                    'likedArticlesPerPage' => $likedArticlesPerPage
                ];

                $html = $this->load->view('member/my_activity_my_liked_articles_area', $myActivityMyLikedArticlesData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '좋아요한 글 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }

    public function loadMyDeletedArticles()
    {
        // 삭제한 게시글 탭의 데이터 로드 및 뷰 렌더링
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {
                $userData = $this->session->userdata('user_data');
                $memberId = isset($userData['user_id']) ? $userData['user_id'] : null;

                // 삭제한 게시글 탭의 페이지네이션 관련 데이터
                $currentPage = $this->input->get('page', TRUE) ?? 1;
                $deletedArticlesPerPage = $this->input->get('deletedArticlesPerPage', TRUE) ?? 10;
                $totalDeletedArticlesCount = count($this->MyActivityModel->getDeletedArticlesByMemberId($memberId));
                $totalPages = ceil($totalDeletedArticlesCount / $deletedArticlesPerPage);

                // 필요한 데이터 로드 로직
                $deletedArticlesByPage = $this->MyActivityModel->getDeletedArticlesByMemberIdByPage($memberId, $currentPage, $deletedArticlesPerPage);

                // 데이터를 배열로 변환하는 로직
                $myActivityMyDeletedArticlesData = [
                    'deletedArticlesByPage' => $deletedArticlesByPage,
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                    'deletedArticlesPerPage' => $deletedArticlesPerPage
                ];
                $html = $this->load->view('member/my_activity_my_deleted_articles_area', $myActivityMyDeletedArticlesData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '댓글단 글 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }

    public function myActivityArticlesSoftDelete()
    {
        if (!isset($_SESSION['user_data'])) {
            echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.']);
            return;
        }

        $memberId = $_SESSION['user_data']['user_id'];
        $articleIds = $this->input->post('articles');

        if (!empty($articleIds) && is_array($articleIds)) {
            $result = $this->MyActivityModel->softDeleteArticles($memberId, $articleIds);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => '선택된 게시글이 없습니다.']);
        }
    }

    public function myActivityCommentsSoftDelete()
    {
        if (!isset($_SESSION['user_data'])) {
            echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.']);
            return;
        }

        $memberId = $_SESSION['user_data']['user_id'];
        $commentIds = $this->input->post('comments');

        if (!empty($commentIds) && is_array($commentIds)) {
            $result = $this->MyActivityModel->softDeleteComments($memberId, $commentIds);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => '선택된 댓글이 없습니다.']);
        }
    }

    public function myActivityArticlesLikedCancel()
    {
        if (!isset($_SESSION['user_data'])) {
            echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.']);
            return;
        }

        $memberId = $_SESSION['user_data']['user_id'];
        $articleIds = $this->input->post('articles');

        if (!empty($articleIds) && is_array($articleIds)) {
            $result = $this->MyActivityModel->cancelArticleLikes($memberId, $articleIds);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => '좋아요를 취소할 글을 선택해주세요.']);
        }
    }
}
