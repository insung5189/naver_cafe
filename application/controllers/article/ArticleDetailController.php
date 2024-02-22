<?
defined('BASEPATH') or exit('No direct script access allowed');
class ArticleDetailController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article/ArticleDetailModel', 'ArticleDetailModel');
        $this->em = $this->doctrine->em;
    }
    public function index($articleId)
    {
        $article = $this->ArticleDetailModel->getArticleById($articleId);
        $comments = $this->ArticleDetailModel->getCommentsByArticleId($articleId);
        if (!$article) {
            show_404();
            return;
        }

        $publicScope = $article->getPublicScope();
        $userData = $this->session->userdata('user_data') ?? null;
        $userRole = $userData['role'] ?? null;

        // 공개 범위가 'public'인 경우, 바로 글 상세보기 페이지 로드
        if ($publicScope === 'public') {
            $this->loadArticleDetailView($article, $comments);
        }
        // 공개 범위가 'members'이며 사용자가 로그인한 경우, 글 상세보기 페이지 로드
        elseif ($publicScope === 'members' && $userData) {
            $this->loadArticleDetailView($article, $comments);
        }
        // 공개 범위가 'admins'이며 사용자가 관리자 권한을 가진 경우, 글 상세보기 페이지 로드
        elseif ($publicScope === 'admins' && $userData && ($userRole === 'ROLE_ADMIN' || $userRole === 'ROLE_MASTER')) {
            $this->loadArticleDetailView($article, $comments);
        }
        // 그 외의 경우 (접근 권한이 없는 경우)
        else {
            // 로그인 상태가 아니라면 로그인 페이지로 리다이렉션
            if (!$userData) {
                $this->setRedirectCookie(current_url());
                redirect('/member/logincontroller');
            } else {
                // 로그인은 되어 있지만, 필요한 권한이 없는 경우
                $this->loadErrorView();
            }
        }
    }

    private function loadArticleDetailView($article, $comments)
    {
        $page_view_data = [
            'title' => '글 상세보기',
            'article' => $article,
            'comments' => $comments,
        ];
        $this->layout->view('article/article_detail_view', $page_view_data);
    }

    private function loadErrorView()
    {
        $page_view_data = [
            'title' => '오류 발생',
            'message' => '접근 권한이 없습니다.',
        ];
        $this->layout->view('errors/error_page', $page_view_data);
    }
}
