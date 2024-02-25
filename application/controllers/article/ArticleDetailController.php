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

        $comments = $this->ArticleDetailModel->getCommentsByArticleId($articleId,);

        $articleFiles = $this->ArticleDetailModel->getFilesByArticleId($articleId);
        $likes = $this->ArticleDetailModel->getLikesByArticleId($articleId);
        $likeCountByArticle = COUNT($likes);

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
            $this->loadArticleDetailView($article, $comments, $user, $articleFilesInfo, $likeCountByArticle);
        } else if ($publicScope === 'members' && $userData) {
            $this->loadArticleDetailView($article, $comments, $user, $articleFilesInfo, $likeCountByArticle);
        } else if ($publicScope === 'admins' && $userData && ($userRole === 'ROLE_ADMIN' || $userRole === 'ROLE_MASTER')) {
            $this->loadArticleDetailView($article, $comments, $user, $articleFilesInfo, $likeCountByArticle);
        } else {
            if (!$userData) {
                $this->setRedirectCookie(current_url());
                redirect('/member/logincontroller');
            } else {
                $this->loadErrorView();
            }
        }
    }

    public function commentSortAction()
    {
        $articleId = $this->input->get('articleId');
        $sortOption = $this->input->get('sortOption') === 'DESC' ? 'DESC' : 'ASC';

        $comments = $this->ArticleDetailModel->getCommentsByArticleId($articleId, $sortOption);

        $commentsData = [];

        foreach ($comments as $comment) {
            $commentData = [
                'id' => $comment->getId(),
                'authorName' => $comment->getMember()->getNickName(),
                'profileImageUrl' => base_url("assets/file/images/memberImgs/") . ($comment->getMember()->getMemberFileName() ?: 'defaultImg/default.png'),
                'content' => $comment->getContent(),
                'commentImageUrl' => $comment->getCommentFilePath() ? base_url("assets/file/commentFiles/img/") . $comment->getCommentFileName() : null,
                'date' => $comment->getCreateDate()->format('Y.m.d H:i'),
                'isArticleAuthor' => $comment->getMember()->getId() == $articleId,
                'isCommentAuthor' => $_SESSION['user_data']['user_id'] === $comment->getMember()->getId(),
                'isRecent' => $comment->getCreateDate()->diff(new DateTime())->i < 1
            ];
            $commentsData[] = $commentData;
        }

        // JSON으로 반환
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['comments' => $commentsData]));
    }

    public function createComment()
    {
        $formData = [
            'content' => $this->input->post('content', TRUE),
            'articleId' => $this->input->post('articleId', TRUE),
            'memberId' => $this->input->post('memberId', TRUE),
            'file' => $_FILES['commentImage'] ?? null
        ];

        if (empty($formData['content']) && empty($formData['file']['name'])) {
            $errorMessages = [
                'content & file' => '댓글 내용이나 파일을 첨부해주세요.'
            ];
            $this->session->set_flashdata('error_messages', $errorMessages);
            redirect('/article/articledetailcontroller/index/' . $formData['articleId']);
            return;
        }

        $article = $this->ArticleDetailModel->getArticleById($formData['articleId']);
        if (!$article) {
            show_error('게시물을 찾을 수 없습니다.');
            return;
        }
        $formData['publicScope'] = $article->getPublicScope();

        $result = $this->ArticleDetailModel->processCreateComment($formData);

        if ($result['success']) {
            redirect('/article/articledetailcontroller/index/' . $formData['articleId'] . '#comment-' . $result['commentId']);
        } else {
            $this->session->set_flashdata('error_messages', $result['errors']);
            redirect('/article/articledetailcontroller/index/' . $formData['articleId']);
        }
    }

    private function loadArticleDetailView($article, $comments, $user, $articleFilesInfo, $likeCountByArticle)
    {
        $page_view_data = [
            'title' => '글 상세보기',
            'article' => $article,
            'comments' => $comments,
            'user' => $user,
            'articleFilesInfo' => $articleFilesInfo,
            'likeCountByArticle' => $likeCountByArticle,
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
