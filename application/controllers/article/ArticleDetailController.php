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
        if (isset($_SESSION['user_data'])) {
            $user = $_SESSION['user_data'];
        } else {
            $user = NULL;
        }
        $article = $this->ArticleDetailModel->getArticleById($articleId);
        if (!$article) {
            show_404();
            return;
        }

        $comments = $this->ArticleDetailModel->getCommentsByArticleId($articleId);
        $articleFiles = $this->ArticleDetailModel->getFilesByArticleId($articleId);

        $articleFilesInfo = [];
        foreach ($articleFiles as $file) {
            $articleFilesInfo[] = [
                'name' => $file->getCombinedName() ?: '첨부파일없음',
                'fullPath' => $this->ArticleDetailModel->getFileFullPath($file),
            ];
        }

        $publicScope = $article->getPublicScope();
        $userData = $this->session->userdata('user_data') ?? null;
        $userRole = $userData['role'] ?? null;

        if ($publicScope === 'public') {
            $this->loadArticleDetailView($article, $comments, $user, $articleFilesInfo);
        } else if ($publicScope === 'members' && $userData) {
            $this->loadArticleDetailView($article, $comments, $user, $articleFilesInfo);
        } else if ($publicScope === 'admins' && $userData && ($userRole === 'ROLE_ADMIN' || $userRole === 'ROLE_MASTER')) {
            $this->loadArticleDetailView($article, $comments, $user, $articleFilesInfo);
        } else {
            if (!$userData) {
                $this->setRedirectCookie(current_url());
                redirect('/member/logincontroller');
            } else {
                $this->loadErrorView();
            }
        }
    }

    private function loadArticleDetailView($article, $comments, $user, $articleFilesInfo)
    {
        $page_view_data = [
            'title' => '글 상세보기',
            'article' => $article,
            'comments' => $comments,
            'user' => $user,
            'articleFilesInfo' => $articleFilesInfo,
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
