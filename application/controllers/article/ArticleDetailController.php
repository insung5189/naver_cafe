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

    public function increaseHitCount()
    {
        $articleId = $this->input->post('articleId');
        $article = $this->ArticleDetailModel->getArticleById($articleId);
        if ($article) {
            $currentHit = $article->getHit();
            $article->setHit($currentHit + 1);
            $this->em->flush();

            echo json_encode(['success' => true, 'newHitCount' => $article->getHit()]);
        } else {
            echo json_encode(['success' => false, 'message' => '유효하지 않은 게시물입니다.']);
        }
    }

    public function commentSortAction()
    {
        if (isset($_SESSION['user_data'])) {
            $user = $_SESSION['user_data'];
        } else {
            $user = NULL;
        }
        $sortOrder = $this->input->get('sortOption');
        $articleId = $this->input->get('articleId');

        try {
            $comments = $this->ArticleDetailModel->getCommentsByArticleId($articleId, $sortOrder);
            $article = $this->ArticleDetailModel->getArticleById($articleId);
            $memberPrflFileUrl = base_url("assets/file/images/memberImgs/");
            $commentFileUrl = base_url("assets/file/commentFiles/img/");

            $comments_view_data = [
                'article' => $article,
                'comments' => $comments,
                'user' => $user,
                'memberPrflFileUrl' => $memberPrflFileUrl,
                'commentFileUrl' => $commentFileUrl,
            ];

            $html = $this->load->view('article/comments_list', $comments_view_data, TRUE);

            echo json_encode(['success' => true, 'html' => $html]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => '댓글 목록을 불러오는 데 실패했습니다.']);
        }
    }

    public function createComment()
    {
        $formData = [
            'content' => $this->input->post('content', TRUE),
            'articleId' => $this->input->post('articleId', TRUE),
            'memberId' => $this->input->post('memberId', TRUE),
            'parentId' => $this->input->post('parentId', TRUE),
            'depth' => $this->input->post('depth', TRUE),
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

    public function createReply()
    {
        $formData = [
            'content' => $this->input->post('content', TRUE),
            'articleId' => $this->input->post('articleId', TRUE),
            'memberId' => $this->input->post('memberId', TRUE),
            'parentId' => $this->input->post('parentId', TRUE),
            'depth' => $this->input->post('depth', TRUE),
            'orderGroup' => $this->input->post('orderGroup', TRUE),
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

        $result = $this->ArticleDetailModel->processCreateReply($formData);

        if ($result['success']) {
            redirect('/article/articledetailcontroller/index/' . $formData['articleId'] . '#comment-' . $result['commentId']);
        } else {
            $this->session->set_flashdata('error_messages', $result['errors']);
            redirect('/article/articledetailcontroller/index/' . $formData['articleId']);
        }
    }

    private function loadArticleDetailView($article, $comments, $user, $articleFilesInfo, $likeCountByArticle)
    {
        $memberPrflFileUrl = base_url("assets/file/images/memberImgs/");
        $commentFileUrl = base_url("assets/file/commentFiles/img/");

        $page_view_data = [
            'title' => '글 상세보기',
            'article' => $article,
            'comments' => $comments,
            'user' => $user,
            'articleFilesInfo' => $articleFilesInfo,
            'likeCountByArticle' => $likeCountByArticle,
            'memberPrflFileUrl' => $memberPrflFileUrl,
            'commentFileUrl' => $commentFileUrl,
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

    public function deleteComment($commentId)
    {
        if (!isset($_SESSION['user_data'])) {
            echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.']);
            return;
        }

        $memberId = $_SESSION['user_data']['user_id'];
        $result = $this->ArticleDetailModel->processDeleteComment($commentId, $memberId);

        if ($result['success']) {
            if ($result['deletedCount'] > 0) {
                echo json_encode(['success' => true, 'message' => '댓글이 삭제되었습니다.']);
            } else {
                echo json_encode(['success' => false, 'message' => '댓글 삭제 권한이 없거나 댓글이 존재하지 않습니다.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => '댓글 삭제 중 오류가 발생했습니다.', 'error' => $result['error']]);
        }
    }
}
