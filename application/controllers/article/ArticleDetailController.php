<?
defined('BASEPATH') or exit('No direct script access allowed');
class ArticleDetailController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article/ArticleDetailModel', 'ArticleDetailModel');
    }

    public function index($articleId)
    {
        // 로그인한 사용자가 있다면 좋아요 여부 확인
        $userLikedArticle = false;

        if (isset($_SESSION['user_data'])) {
            $user = $_SESSION['user_data'];
            $userLikedArticle = $this->ArticleDetailModel->userLikedArticle($articleId, $user['user_id']);
        } else {
            $user = NULL;
        }

        $article = $this->ArticleDetailModel->getArticleById($articleId);
        if (!$article) {
            show_404();
            return;
        }

        $parentArticlesExist = $this->ArticleDetailModel->checkParentArticlesExist($article);

        $comments = $this->ArticleDetailModel->getCommentsByArticleId($articleId);

        $articleFiles = $this->ArticleDetailModel->getFilesByArticleId($articleId);
        $likes = $this->ArticleDetailModel->getLikesByArticleId($articleId);
        $likeCountByArticle = COUNT($likes);

        $articleFilesInfo = [];
        foreach ($articleFiles as $file) {
            $articleFilesInfo[] = [
                'name' => $file->getCombinedName() ?: '첨부파일없음',
                'fullPath' => $file->getPath(),
            ];
        }

        $publicScope = $article->getPublicScope();
        $userData = $this->session->userdata('user_data') ?? null;
        $userRole = $userData['role'] ?? null;

        if ($publicScope === 'public') {
            $this->loadArticleDetailView($article, $comments, $user, $articleFilesInfo, $likeCountByArticle, $userLikedArticle, $parentArticlesExist);
        } else if ($publicScope === 'members' && $userData) {
            $this->loadArticleDetailView($article, $comments, $user, $articleFilesInfo, $likeCountByArticle, $userLikedArticle, $parentArticlesExist);
        } else if ($publicScope === 'admins' && $userData && ($userRole === 'ROLE_ADMIN' || $userRole === 'ROLE_MASTER')) {
            $this->loadArticleDetailView($article, $comments, $user, $articleFilesInfo, $likeCountByArticle, $userLikedArticle, $parentArticlesExist);
        } else {
            if (!$userData) {
                $this->setRedirectCookie(current_url());
                redirect('/member/logincontroller');
            } else {
                $this->loadErrorView();
            }
        }
    }

    private function loadArticleDetailView($article, $comments, $user, $articleFilesInfo, $likeCountByArticle, $userLikedArticle, $parentArticlesExist)
    {
        $memberPrflFileUrl = "/assets/file/images/memberImgs/";
        $commentFileUrl = "/assets/file/commentFiles/img/";

        $page_view_data = [
            'title' => '글 상세보기',
            'article' => $article,
            'comments' => $comments,
            'user' => $user,
            'parentArticlesExist' => $parentArticlesExist,
            'articleFilesInfo' => $articleFilesInfo,
            'likeCountByArticle' => $likeCountByArticle,
            'userLikedArticle' => $userLikedArticle,
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

    // 공통 로직을 처리하는 메서드
    public function relatedArticles()
    {
        if ($this->input->is_ajax_request()) {
            try {
                $boardId = $this->input->get('boardId', TRUE) ?? 1;
                $articleBoard = $this->em->find('Models\Entities\ArticleBoard', $boardId);
                $articleId = $this->input->get('articleId', TRUE) ?? NULL;
                $relatedArticlesPerPage = 5;

                $targetPage = $this->ArticleDetailModel->findPageForCurrentArticle($boardId, $articleId, $relatedArticlesPerPage);
                $currentPage = $this->input->get('page', TRUE) ?? $targetPage;
                $relatedArticles = $this->ArticleDetailModel->getArticlesByBoardIdAndPage($boardId, $currentPage, $relatedArticlesPerPage);
                $totalArticleCount = $this->ArticleDetailModel->getTotalArticleCount($boardId);
                $totalPages = ceil($totalArticleCount / $relatedArticlesPerPage);

                // 게시글 ID 배열 생성
                $articleIds = array_map(function ($article) {
                    return $article->getId();
                }, $relatedArticles);

                // 게시글별 댓글 개수 조회
                $commentCounts = $this->ArticleDetailModel->getCommentCountForArticles($articleIds);

                $relatedArticlesData = [
                    'articleBoard' => $articleBoard,
                    'relatedArticles' => $relatedArticles,
                    'commentCounts' => $commentCounts,
                    'totalArticleCountAll' => $totalArticleCount,
                    'currentPage' => $currentPage,
                    'targetPage' => $targetPage,
                    'totalPages' => $totalPages,
                    'boardId' => $boardId,
                    'articleId' => $articleId,
                    'relatedArticlesPerPage' => $relatedArticlesPerPage,
                    'errors' => $errors ?? []
                ];

                $html = $this->load->view('article/article_detail_view_related_articles', $relatedArticlesData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '데이터를 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
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

    public function articleLike()
    {
        if (isset($_SESSION['user_data'])) {
            $memberId = $_SESSION['user_data']['user_id'];
        } else {
            echo json_encode(['success' => false, 'loginRequired' => true]);
            return;
        }

        $articleId = $this->input->post('articleId');

        // Likes 테이블에 레코드 추가 로직
        $result = $this->ArticleDetailModel->processAddArticleLike($articleId, $memberId);

        // 게시글의 좋아요 수 업데이트 로직
        $articleLikeCount = count($this->ArticleDetailModel->getLikesByArticleId($articleId));

        if ($result['success']) {
            echo json_encode(['success' => true, 'action' => $result['action'], 'likeCount' => $articleLikeCount]);
        } else {
            echo json_encode(['success' => false, 'message' => $result['message'], 'likeCount' => $articleLikeCount]);
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
        $depthOrder = $this->input->get('depthOption');
        $articleId = $this->input->get('articleId');
        $treeOption = $this->input->get('treeOption');

        try {
            $comments = $this->ArticleDetailModel->getCommentsByArticleId($articleId, $sortOrder, $depthOrder, $treeOption);
            $article = $this->ArticleDetailModel->getArticleById($articleId);
            $memberPrflFileUrl = "/assets/file/images/memberImgs/";
            $commentFileUrl = "/assets/file/commentFiles/img/";

            $comments_view_data = [
                'article' => $article,
                'comments' => $comments,
                'user' => $user,
                'memberPrflFileUrl' => $memberPrflFileUrl,
                'commentFileUrl' => $commentFileUrl,
            ];

            $html = $this->load->view('article/comments_list', $comments_view_data, TRUE);

            echo json_encode(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
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
            $errorMessages = ['content & file' => '댓글 내용이나 파일을 첨부해주세요.'];
            $this->session->set_flashdata('error_messages', $errorMessages);
            redirect('/article/articledetailcontroller/index/' . $formData['articleId']);
            return;
        }

        $result = $this->ArticleDetailModel->processCreateComment($formData);

        if ($result['success']) {
            redirect('/article/articledetailcontroller/index/' . $formData['articleId']);
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

    public function editComment($commentId)
    {
        if ($this->input->is_ajax_request()) {
            $formData = [
                'content' => $this->input->post('commentEditContent', TRUE),
                'articleId' => $this->input->post('articleId', TRUE),
                'memberId' => $this->input->post('memberId', TRUE),
                'file' => $_FILES['commentImage'] ?? null
            ];

            if (!isset($_SESSION['user_data'])) {
                echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.']);
                $this->setRedirectCookie(current_url());
                redirect('/member/logincontroller');
                return;
            }

            if (empty($formData['content']) && empty($formData['file']['name'])) {
                echo json_encode(['success' => false, 'message' => '댓글 내용이나 파일을 첨부해주세요.']);
                return;
            }

            // 모델의 댓글 수정 메서드 호출
            $result = $this->ArticleDetailModel->processEditComment($commentId, $formData);

            if ($result['success']) {
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => $result['message']]);
            }
        } else {
            show_404();
        }
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

    public function deleteArticle()
    {
        $articleId = $this->input->post('articleId');

        if (!empty($articleId)) {
            $result = $this->ArticleDetailModel->processDeleteArticle($articleId);
            $boardId = $this->ArticleDetailModel->getBoardIdByArticleId($articleId);

            if ($result) {
                echo json_encode(['success' => true, 'articleboardId' => $boardId]);
            } else {
                echo json_encode(['success' => false, 'message' => '게시글 삭제 실패']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => '']);
        }
    }
}
