<?
defined('BASEPATH') or exit('직접적인 스크립트 접근은 허용되지 않습니다.');

class ArticleDetailModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('doctrine');
        $this->load->library('session');
        $this->em = $this->doctrine->em;
    }

    private $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

    public function getArticleById($articleId)
    {
        $article = $this->doctrine->em->find('Models\Entities\Article', $articleId);
        if (!$article) {
            return null;
        }
        return $article;
    }

    public function getAuthorByArticle($articleId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('m.article = :articleId')
            ->setParameter('articleId', $articleId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function getCommentsByArticleId($articleId, $sortOption = 'ASC')
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('c')
            ->from('Models\Entities\Comment', 'c')
            ->where('c.article = :articleId')
            ->setParameter('articleId', $articleId)
            ->orderBy('c.orderGroup', $sortOption)
            // ->addOrderBy('c.depth', 'ASC') // 주석 해제하면 뎁스별로 모아서 정렬 가능.
            ->addOrderBy('c.createDate', 'ASC'); // 네이버카페는 기본적으로 뎁스 구별없이 등록시간순으로 정렬함.

        return $queryBuilder->getQuery()->getResult();
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
            $basePath = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'file' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'articleFiles' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;
        } else if (in_array($file->getExt(), $this->documentExtensions)) {
            $basePath = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'file' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'articleFiles' . DIRECTORY_SEPARATOR . 'doc' . DIRECTORY_SEPARATOR;
        } else {
            $basePath = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'file' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'articleFiles' . DIRECTORY_SEPARATOR . 'others' . DIRECTORY_SEPARATOR;
        }
        return base_url($basePath . $file->getCombinedName());
    }

    public function getLikesByArticleId($articleId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('l')
            ->from('Models\Entities\Likes', 'l')
            ->where('l.article = :articleId')
            ->setParameter('articleId', $articleId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function processCreateComment($formData)
    {

        $errorData = ['errors' => []];

        $this->processCommentImage($formData, $errorData);

        if (!empty($errorData['errors'])) {
            return ['success' => false, 'errors' => $errorData['errors']];
        }

        try {
            // 고유 식별 정보 생성
            $uniqueIdentifier = $this->generateUniqueIdentifier($formData['memberId']);

            $comment = new Models\Entities\Comment();
            $comment->setArticle($this->em->find('Models\Entities\Article', $formData['articleId']));
            $comment->setMember($this->em->find('Models\Entities\Member', $formData['memberId']));
            $comment->setContent($formData['content']);
            $comment->setCreateDate(new \DateTime(date('Y-m-d H:i')));
            $comment->setPublicScope($formData['publicScope']);
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
            $newReply = $this->em->getRepository(Models\Entities\Comment::class)->findOneBy(['uniqueIdentifier' => $uniqueIdentifier]);

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

    public function processDeleteComment($commentId, $memberId)
    {
        try {
            $queryBuilder = $this->em->createQueryBuilder();
            $query = $queryBuilder->delete('Models\Entities\Comment', 'c')
                ->where('c.id = :commentId')
                ->andWhere('c.member = :memberId')
                ->setParameter('commentId', $commentId)
                ->setParameter('memberId', $memberId)
                ->getQuery();

            $result = $query->execute();
            return ['success' => true, 'deletedCount' => $result];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
