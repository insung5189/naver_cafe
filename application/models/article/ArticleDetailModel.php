<?
defined('BASEPATH') or exit('No direct script access allowed');

class ArticleDetailModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

    public function getArticleById($articleId)
    {
        $queryBuilder = $this->doctrine->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.id = :articleId')
            ->andwhere('a.isActive = 1')
            ->setParameter('articleId', $articleId);

        $query = $queryBuilder->getQuery();
        $article = $query->getOneOrNullResult();

        return $article;
    }

    public function checkParentArticlesExist($article)
    {
        if (!empty($article->getParent())) {
            $articleParentId = $article->getParent()->getId();
            $parentArticleIsExsist = (bool)$this->getArticleById($articleParentId);
            $result = $parentArticleIsExsist;
        } else {
            $result = true;
        }

        return $result;
    }

    public function getCommentsByArticleId($articleId, $sortOption = '', $depthOption = '', $treeOption = '')
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('c')
            ->from('Models\Entities\Comment', 'c')
            ->where('c.article = :articleId')
            ->andwhere('c.isActive = 1')
            ->setParameter('articleId', $articleId)
            ->orderBy('c.orderGroup', $sortOption);

        // 기존 depthOption 처리 로직
        if ($depthOption === 'ASC') {
            $queryBuilder->addOrderBy('c.depth', $depthOption)
                ->addOrderBy('c.createDate', 'ASC');
        }

        // 모든 댓글 조회
        $comments = $queryBuilder->getQuery()->getResult();

        // treeOption이 활성화되어 있으면, 트리 구조로 재정렬
        if ($treeOption === 'enabled') {
            $comments = $this->reorderCommentsAsTree($comments, $sortOption);
        } else if ($treeOption === 'disabled') {
            $comments = $queryBuilder->getQuery()->getResult();
        }

        return $comments;
    }

    private function reorderCommentsAsTree($comments, $sortOption)
    {
        // 댓글을 parentId를 키로 하는 배열로 재구성
        $commentsByParentId = [];
        foreach ($comments as $comment) {
            $parentId = $comment->getParent() ? $comment->getParent()->getId() : 0;
            $commentsByParentId[$parentId][] = $comment;
        }

        // 루트 댓글부터 트리 구조로 재구성하여 1차원 배열로 변환
        $flattenedComments = [];
        $this->buildTree($commentsByParentId, 0, $flattenedComments);

        return $flattenedComments;
    }

    private function buildTree(&$commentsByParentId, $parentId, &$flattenedComments)
    {
        if (isset($commentsByParentId[$parentId])) {
            foreach ($commentsByParentId[$parentId] as $comment) {
                // 현재 댓글을 1차원 배열에 추가
                $flattenedComments[] = $comment;

                // 현재 댓글의 자식 댓글이 있다면 재귀적으로 처리
                if (isset($commentsByParentId[$comment->getId()])) {
                    $this->buildTree($commentsByParentId, $comment->getId(), $flattenedComments);
                }
            }
        }
    }

    public function getFilesByArticleId($articleId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('f')
            ->from('Models\Entities\File', 'f')
            ->where('f.article = :articleId')
            ->setParameter('articleId', $articleId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function getFileFullPath($file)
    {
        if (in_array($file->getExt(), $this->imageExtensions)) {
            $basePath = '/assets/file/articleFiles/img/';
        } else if (in_array($file->getExt(), $this->documentExtensions)) {
            $basePath = '/assets/file/articleFiles/doc/';
        } else {
            $basePath = '/assets/file/articleFiles/others/';
        }
        return $basePath . $file->getCombinedName();
    }

    // 게시글의 좋아요 수를 불러오는 쿼리
    public function getLikesByArticleId($articleId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('l')
            ->from('Models\Entities\Likes', 'l')
            ->where('l.article = :articleId')
            ->setParameter('articleId', $articleId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function userLikedArticle($articleId, $userId)
    {
        $like = $this->em->getRepository('Models\Entities\Likes')->findOneBy([
            'article' => $articleId,
            'member' => $userId
        ]);

        return $like !== null;
    }

    public function processAddArticleLike($articleId, $memberId)
    {
        try {
            $likeExists = $this->em->getRepository('Models\Entities\Likes')->findOneBy([
                'article' => $articleId,
                'member' => $memberId
            ]);

            if ($likeExists) {
                $this->em->remove($likeExists);
                $this->em->flush();
                return ['success' => true, 'action' => 'removed'];
            } else {
                $like = new Models\Entities\Likes();

                $article = $this->em->find('Models\Entities\Article', $articleId);
                $member = $this->em->find('Models\Entities\Member', $memberId);

                if (!$article) {
                    return ['success' => false, 'message' => '게시글 정보를 찾을 수 없습니다.'];
                } else if (!$member) {
                    return ['success' => false, 'message' => '사용자 정보를 찾을 수 없습니다.'];
                } else {
                    $like->setArticle($article);
                    $like->setMember($member);

                    $this->em->persist($like);
                    $this->em->flush();
                    return ['success' => true, 'action' => 'added'];
                }
            }
        } catch (\Exception $e) {
            log_message('error', '좋아요 처리 실패: ' . $e->getMessage());
            return ['success' => false, 'message' => '좋아요 처리 중 오류가 발생했습니다.'];
        }
    }

    public function processCreateComment($formData)
    {

        $errorData = ['errors' => []];

        $this->processCommentImage($formData, $errorData);

        if (!empty($errorData['errors'])) {
            return ['success' => false, 'errors' => $errorData['errors']];
        }

        try {
            $article = $this->em->getRepository('Models\Entities\Article')->find($formData['articleId']);
            if (!$article) {
                throw new \Exception('게시물을 찾을 수 없습니다.');
            }
            // 고유 식별 정보 생성
            $uniqueIdentifier = $this->generateUniqueIdentifier($formData['memberId']);

            $comment = new Models\Entities\Comment();
            $comment->setArticle($this->em->find('Models\Entities\Article', $formData['articleId']));
            $comment->setMember($this->em->find('Models\Entities\Member', $formData['memberId']));
            $comment->setContent($formData['content']);
            $comment->setCreateDate(new \DateTime(date('Y-m-d H:i')));
            $comment->setPublicScope($article->getPublicScope());
            $comment->setUniqueIdentifier($uniqueIdentifier);
            $comment->setParent($this->em->find('Models\Entities\Comment', $formData['parentId']));
            $comment->setDepth($formData['depth']);

            $orderGroup = 0;
            if (!$formData['parentId']) {
                $queryBuilder = $this->em->createQueryBuilder();
                $queryBuilder->select($queryBuilder->expr()->max('c.orderGroup'))
                    ->from('Models\Entities\Comment', 'c');
                $maxOrderGroup = $queryBuilder->getQuery()->getSingleScalarResult();
                $orderGroup = $maxOrderGroup ? $maxOrderGroup + 1 : 1;
                $comment->setOrderGroup($orderGroup);
            }

            if (!empty($formData['commentFilePath']) && !empty($formData['commentFileName'])) {
                $comment->setCommentFilePath($formData['commentFilePath']);
                $comment->setCommentFileName($formData['commentFileName']);
            } else {
                $comment->setCommentFilePath(null);
                $comment->setCommentFileName(null);
            }
            $this->em->persist($comment);
            $this->em->flush();

            // 저장된 댓글의 고유 식별 정보를 기반으로 댓글 조회
            // 댓글, 답글 저장 후 해당 위치로 스크롤 하기 위해서 해시값을 추가하려는데 동시에 여러 댓글이 등록될 수 있으니 고유값으로 구분하기 위함.
            $newComment = $this->em->getRepository(Models\Entities\Comment::class)->findOneBy(['uniqueIdentifier' => $uniqueIdentifier]);

            return ['success' => true, 'commentId' => $newComment->getId()];
        } catch (\Exception $e) {
            log_message('error', '댓글 등록 실패: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => '댓글 등록 중 오류가 발생했습니다.']];
        }
    }

    public function processCreateReply($formData)
    {
        $errorData = ['errors' => []];

        $this->processCommentImage($formData, $errorData);

        if (!empty($errorData['errors'])) {
            return ['success' => false, 'errors' => $errorData['errors']];
        }

        try {
            $uniqueIdentifier = $this->generateUniqueIdentifier($formData['memberId']);

            $reply = new Models\Entities\Comment();
            $reply->setArticle($this->em->find('Models\Entities\Article', $formData['articleId']));
            $reply->setMember($this->em->find('Models\Entities\Member', $formData['memberId']));
            $reply->setContent($formData['content']);
            $reply->setCreateDate(new \DateTime());
            $reply->setPublicScope($formData['publicScope']);
            $reply->setUniqueIdentifier($uniqueIdentifier);
            $reply->setParent($this->em->find('Models\Entities\Comment', $formData['parentId']));
            $reply->setDepth($formData['depth']);
            $reply->setOrderGroup($formData['orderGroup']);

            if (!empty($formData['commentFilePath']) && !empty($formData['commentFileName'])) {
                $reply->setCommentFilePath($formData['commentFilePath']);
                $reply->setCommentFileName($formData['commentFileName']);
            } else {
                $reply->setCommentFilePath(null);
                $reply->setCommentFileName(null);
            }

            $this->em->persist($reply);
            $this->em->flush();

            // 저장된 답글의 고유 식별 정보를 기반으로 답글 조회
            // 댓글, 답글 저장 후 해당 위치로 스크롤 하기 위해서 해시값을 추가하려는데 동시에 여러 댓글이 등록될 수 있으니 고유값으로 구분하기 위함.
            $newReply = $this->em->getRepository('Models\Entities\Comment')->findOneBy(['uniqueIdentifier' => $uniqueIdentifier]);

            return ['success' => true, 'commentId' => $newReply->getId()];
        } catch (\Exception $e) {
            log_message('error', '답글 등록 실패: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => '답글 등록 중 오류가 발생했습니다.']];
        }
    }

    private function processCommentImage(&$formData, &$errorData)
    {

        $config['upload_path'] = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'file' . DIRECTORY_SEPARATOR . 'commentFiles' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;
        $config['allowed_types'] = 'jpg|jpeg|png|bmp|webp|gif'; // 허용하는 이미지파일 확장자
        $config['max_size'] = '20480'; // 20메가로 제한

        $this->load->library('upload', $config);

        if (isset($_FILES['commentImage']) && $_FILES['commentImage']['name'] != '') {
            if ($this->upload->do_upload('commentImage')) {
                $uploadData = $this->upload->data();
                $originalName = trim(pathinfo($uploadData['client_name'], PATHINFO_FILENAME));
                $fileExt = $uploadData['file_ext'];
                $uploadDate = date('Ymd');
                $uuid = uniqid();
                $newFileName = "{$originalName}-{$uploadDate}-{$uuid}{$fileExt}";

                rename($uploadData['full_path'], $uploadData['file_path'] . $newFileName);

                $formData['commentFilePath'] = $config['upload_path'] . $newFileName;
                $formData['commentFileName'] = $newFileName;
            } else {
                $errorData['errors']['file'] = $this->upload->display_errors('', '');
            }
        }
    }

    // 댓글 앵커(#)를 위한 고유 식별정보 생성 및 부여
    private function generateUniqueIdentifier($memberId)
    {
        $timestamp = microtime(true);
        $sessionId = session_id();
        return hash('sha256', $memberId . $timestamp . $sessionId);
    }

    public function processEditComment($commentId, $formData)
    {
        $errorData = ['errors' => []];

        $this->processCommentImage($formData, $errorData);

        if (!empty($errorData['errors'])) {
            return ['success' => false, 'errors' => $errorData['errors']];
        }

        try {
            $queryBuilder = $this->em->createQueryBuilder();
            $query = $queryBuilder->update('Models\Entities\Comment', 'c')
                ->set('c.content', ':content')
                ->set('c.modifyDate', ':modifyDate')
                ->where('c.id = :commentId')
                ->andWhere('c.member = :memberId')
                ->andWhere('c.article = :articleId')
                ->setParameter('content', $formData['content'])
                ->setParameter('modifyDate', new \DateTime())
                ->setParameter('commentId', $commentId)
                ->setParameter('memberId', $formData['memberId'])
                ->setParameter('articleId', $formData['articleId']);

            if (!empty($formData['commentFilePath']) && !empty($formData['commentFileName'])) {
                $query->set('c.commentFilePath', ':commentFilePath')
                    ->set('c.commentFileName', ':commentFileName')
                    ->setParameter('commentFilePath', $formData['commentFilePath'])
                    ->setParameter('commentFileName', $formData['commentFileName']);
            } else {
                // 새로운 이미지 정보가 없는 경우, 기존 이미지 정보를 확인
                $existingImagePath = isset($formData['existingImagePath']) ? $formData['existingImagePath'] : NULL;
                $existingImageName = isset($formData['existingImageName']) ? $formData['existingImageName'] : NULL;

                // 기존 이미지 정보가 있으면 사용, 없으면 NULL로 설정
                $query->set('c.commentFilePath', ':commentFilePath')
                    ->set('c.commentFileName', ':commentFileName')
                    ->setParameter('commentFilePath', $existingImagePath)
                    ->setParameter('commentFileName', $existingImageName);
            }

            $result = $query->getQuery()->execute();

            if ($result > 0) {
                $updatedComment = $this->em->find('Models\Entities\Comment', $commentId);

                // 성공 응답에 댓글의 최신 내용과 이미지 파일 이름 포함
                $response = [
                    'success' => true,
                    'message' => '댓글이 수정되었습니다.',
                    'content' => $updatedComment->getContent() ?? '',
                    'commentFileName' => $updatedComment->getCommentFileName() ?? ''
                ];

                return $response;
            } else {
                return ['success' => false, 'message' => '댓글 수정 권한이 없거나 댓글이 존재하지 않습니다.'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => '댓글 수정 중 오류가 발생했습니다.', 'error' => $e->getMessage()];
        }
    }

    public function processDeleteComment($commentId, $memberId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $query = $queryBuilder
            ->update('Models\Entities\Comment', 'c')
            ->set('c.isActive', ':isActive')
            ->set('c.deletedDate', ':deleteTime')
            ->where('c.id = :commentId')
            ->andWhere('c.member = :memberId')
            ->setParameter('isActive', 0)
            ->setParameter('deleteTime', new \DateTime())
            ->setParameter('commentId', $commentId)
            ->setParameter('memberId', $memberId)
            ->getQuery();

        try {
            $result = $query->execute();

            return ['success' => true, 'updatedCount' => $result];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function processDeleteArticle($articleId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $query = $queryBuilder
            ->update('Models\Entities\Article', 'a')
            ->set('a.isActive', ':isActive')
            ->set('a.deletedDate', ':deleteTime')
            ->where('a.id = :articleId')
            ->setParameter('isActive', 0)
            ->setParameter('deleteTime', new \DateTime())
            ->setParameter('articleId', $articleId)
            ->getQuery();

        return $query->execute();
    }

    public function getBoardIdByArticleId($articleId)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        $query = $queryBuilder->select('IDENTITY(a.articleBoard) as id')
            ->from('Models\Entities\Article', 'a')
            ->where('a.id = :articleId')
            ->setParameter('articleId', $articleId)
            ->getQuery();

        $boardId = $query->getOneOrNullResult();

        return $boardId ? $boardId['id'] : 1;
    }

    // 관련게시판 부모글만 불러오는 쿼리
    public function getArticlesByBoardIdAndPage($boardId, $currentPage, $articlesPerPage)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.articleBoard = :boardId')
            ->andWhere('a.isActive = 1')
            ->andWhere('a.depth = 0')
            ->setFirstResult(($currentPage - 1) * $articlesPerPage)
            ->setMaxResults($articlesPerPage)
            ->setParameter('boardId', $boardId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findPageForCurrentArticle($boardId, $articleId, $articlesPerPage)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        // 현재 게시글 ID보다 앞서는 게시글의 수를 세어 순서를 알아냄
        $queryBuilder->select('COUNT(a.id) as articleOrder')
            ->from('Models\Entities\Article', 'a')
            ->where('a.articleBoard = :boardId')
            ->andWhere('a.id <= :articleId') // 현재 게시글 ID 포함 이전 게시글 모두 카운트
            ->andWhere('a.isActive = 1')
            ->andWhere('a.depth = 0')
            ->setParameter('boardId', $boardId)
            ->setParameter('articleId', $articleId);

        $order = $queryBuilder->getQuery()->getSingleScalarResult();

        // 게시글의 순서를 바탕으로 해당 게시글이 몇 번째 페이지에 있는지 계산
        $pageNumber = ceil($order / $articlesPerPage);

        return $pageNumber;
    }

    public function getTotalArticleCount($boardId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('count(a.id)')
            ->from('Models\Entities\Article', 'a')
            ->where('a.articleBoard = :boardId')
            ->andWhere('a.isActive = 1')
            ->andWhere('a.depth = 0')
            ->setParameter('boardId', $boardId);

        $query = $queryBuilder->getQuery();
        return $query->getSingleScalarResult();
    }

    public function getCommentCountForArticles($articleIds)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('IDENTITY(c.article) as articleId, COUNT(c.id) as commentCount')
            ->from('Models\Entities\Comment', 'c')
            ->where($queryBuilder->expr()->in('c.article', ':articleIds'))
            ->andWhere('c.isActive = 1')
            ->groupBy('c.article')
            ->setParameter('articleIds', $articleIds);

        $results = $queryBuilder->getQuery()->getResult();

        // 결과를 articleId를 키로 하는 배열로 재구성
        $commentCounts = [];
        foreach ($results as $result) {
            $commentCounts[$result['articleId']] = $result['commentCount'];
        }

        return $commentCounts;
    }
}
