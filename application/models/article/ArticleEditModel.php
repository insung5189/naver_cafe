<?
defined('BASEPATH') or exit('No direct script access allowed');

class ArticleEditModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getArticleById($parentId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.id = :parentId')
            ->andWhere('a.isActive = 1')
            ->setParameter('parentId', $parentId);

        $query = $queryBuilder->getQuery();

        try {
            return $query->getOneOrNullResult();
        } catch (\Doctrine\ORM\NonUniqueResultException $e) {
            // 쿼리 결과가 유니크하지 않은 경우의 예외 처리
            throw new \Exception("Expected a single result but got multiple.");
        }
    }

    public function getParentArticleById($articleId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->join('Models\Entities\Article', 'parent', 'WITH', 'a.id = parent.parent')
            ->where('parent.id = :articleId')
            ->andWhere('a.isActive = 1')
            ->setParameter('articleId', $articleId);

        $query = $queryBuilder->getQuery();

        try {
            return $query->getOneOrNullResult();
        } catch (\Doctrine\ORM\NonUniqueResultException $e) {
            throw new \Exception("Expected a single result but got multiple.");
        }
    }

    public function processCreateNewArticle($formData)
    {
        $this->em->getConnection()->beginTransaction(); // 트랜잭션 시작
        try {
            // 게시판 확인
            $board = $this->em->getRepository('Models\Entities\ArticleBoard')->find($formData['boardId']);
            if (!$board) {
                throw new Exception('게시판을 찾을 수 없습니다.');
            }

            // 게시판 ID에 따른 유효한 말머리 설정
            $validPrefixesMap = [
                '4' => ['PHP', 'MYSQL', 'APACHE', 'JS', 'HTML', 'CSS', '기타'],
                '5' => ['질문', '답변'],
            ];

            $boardIdAsString = (string)$formData['boardId'];
            $validPrefixes = array_key_exists($boardIdAsString, $validPrefixesMap) ? $validPrefixesMap[$boardIdAsString] : [];
            $prefix = in_array($formData['prefix'], $validPrefixes) ? $formData['prefix'] : null;

            // orderGroup 계산
            $queryBuilder = $this->em->createQueryBuilder();
            $queryBuilder->select('MAX(a.orderGroup) as maxOrderGroup')
                ->from('Models\Entities\Article', 'a');
            $maxOrderGroupResult = $queryBuilder->getQuery()->getSingleScalarResult();
            $orderGroup = $maxOrderGroupResult ? $maxOrderGroupResult + 1 : 1;

            $article = new Models\Entities\Article();
            // 기본 데이터 설정
            $article->setTitle($formData['title']);
            $article->setContent($formData['content']);
            $member = $this->em->getRepository('Models\Entities\Member')->find($formData['memberId']);
            $article->setMember($member);
            $article->setCreateDate(new \DateTime());
            $article->setArticleBoard($board);
            $article->setPublicScope($formData['publicScope']);
            $article->setPrefix($prefix);
            $article->setOrderGroup($orderGroup);
            $article->setDepth(0);
            $article->setIsActive(true);
            $article->setIp($_SERVER['REMOTE_ADDR']);

            $this->em->persist($article);
            $this->em->flush();

            // 게시글과 연관된 파일정보와 게시글의 id를 매개변수로 File엔티티에 파일정보 등록
            $this->saveFileEntity($formData, $article->getId());

            $this->em->getConnection()->commit(); // 트랜잭션 커밋
            return ['success' => true, 'articleId' => $article->getId()];
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack(); // 에러 발생시 롤백
            return ['success' => false, 'errors' => ['message' => '게시글 작성 중 오류 발생: ' . $e->getMessage()]];
        }
    }

    public function processCreateReplyArticle($formData)
    {
        $this->em->getConnection()->beginTransaction();
        try {
            $parentArticle = $this->getArticleById($formData['parentId']);
            if (!$parentArticle) {
                throw new Exception('부모 게시물을 찾을 수 없습니다.');
            }

            $board = $parentArticle->getArticleBoard();
            if (!$board) {
                throw new Exception('게시판을 찾을 수 없습니다.');
            }

            // 게시판 ID에 따른 유효한 말머리 설정
            $validPrefixesMap = [
                '4' => ['PHP', 'MYSQL', 'APACHE', 'JS', 'HTML', 'CSS', '기타'],
                '5' => ['질문', '답변'],
            ];

            $boardIdAsString = (string)$parentArticle->getArticleBoard()->getId();
            $validPrefixes = array_key_exists($boardIdAsString, $validPrefixesMap) ? $validPrefixesMap[$boardIdAsString] : [];
            $prefix = in_array($formData['prefix'], $validPrefixes) ? $formData['prefix'] : null;
            $publicScope = $parentArticle->getPublicScope();

            // depth와 orderGroup 설정
            $depth = $parentArticle->getDepth() + 1;
            $orderGroup = $parentArticle->getOrderGroup();

            $article = new Models\Entities\Article();
            // 기본 데이터 설정
            $article->setTitle($formData['title']);
            $article->setContent($formData['content']);
            $member = $this->em->getRepository('Models\Entities\Member')->find($formData['memberId']);
            $article->setMember($member);
            $article->setCreateDate(new \DateTime());
            $article->setArticleBoard($board);
            $article->setPublicScope($publicScope);
            $article->setPrefix($prefix);
            $article->setOrderGroup($orderGroup);
            $article->setDepth($depth);
            $article->setIsActive(true);
            $article->setIp($_SERVER['REMOTE_ADDR']);
            $article->setParent($parentArticle);

            $this->em->persist($article);
            $this->em->flush();

            $this->saveFileEntity($formData, $article->getId());
            $this->em->getConnection()->commit(); // 트랜잭션 커밋
            return ['success' => true, 'articleId' => $article->getId()];
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack(); // 에러 발생시 롤백
            return ['success' => false, 'errors' => ['message' => '답글 작성 중 오류 발생: ' . $e->getMessage()]];
        }
    }

    public function processModifyArticle($formData, $articleId)
    {
        $this->em->getConnection()->beginTransaction(); // 트랜잭션 시작
        try {
            $article = $this->em->getRepository('Models\Entities\Article')->find($articleId);
            if (!$article) {
                throw new Exception('수정할 게시글을 찾을 수 없습니다.');
            }

            if ($article->getMember()->getId() !== $formData['memberId']) {
                throw new Exception('게시글 수정권한이 없습니다.');
            }

            $board = $this->em->getRepository('Models\Entities\ArticleBoard')->find($formData['boardId']);
            if (!$board) {
                throw new Exception('게시판을 찾을 수 없습니다.');
            }

            $existingFiles = $this->em->getRepository('Models\Entities\File')->findBy(['article' => $article]);
            $existingFileUrls = array_map(function ($file) {
                return $file->getPath();
            }, $existingFiles);

            // 제출된 파일 URL 배열
            $submittedFileUrls = $formData['fileUrls'];

            // 삭제할 파일 식별
            $filesToDelete = array_diff($existingFileUrls, $submittedFileUrls);

            // 삭제할 파일 처리
            foreach ($filesToDelete as $fileToDeleteUrl) {
                $fileToDelete = $this->em->getRepository('Models\Entities\File')->findOneBy(['path' => $fileToDeleteUrl]);
                if ($fileToDelete) {
                    $this->em->remove($fileToDelete);
                }
            }

            $article->setTitle($formData['title']);
            $article->setContent($formData['content']);
            $article->setModifyDate(new \DateTime());
            $article->setArticleBoard($board);
            $article->setPublicScope($formData['publicScope']);
            $article->setPrefix($formData['prefix']);
            $article->setOrderGroup($article->getOrderGroup());
            $article->setDepth($article->getDepth());
            $article->setIsActive(true);
            $article->setIp($_SERVER['REMOTE_ADDR']);
            if (isset($formData['parentId'])) {
                $parentArticle = $this->getArticleById($formData['parentId']);
                if ($parentArticle) {
                    $article->setParent($parentArticle);
                }
            }

            $this->em->persist($article);
            $this->em->flush();

            // 새로운 파일 정보 저장
            $this->saveFileEntity($formData, $article->getId());

            $this->em->getConnection()->commit(); // 트랜잭션 커밋
            return ['success' => true, 'articleId' => $article->getId()];
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack(); // 에러 발생시 롤백
            return ['success' => false, 'errors' => ['message' => '게시글 수정 중 오류 발생: ' . $e->getMessage()]];
        }
    }

    protected function saveFileEntity($formData, $articleId)
    {
        $submittedFileUrls = $formData['fileUrls']; // 제출된 모든 파일 URL
        $existingFiles = $this->em->getRepository('Models\Entities\File')->findBy(['article' => $articleId]);
        $existingFileUrls = array_map(function ($file) {
            return $file->getPath();
        }, $existingFiles);

        $newFileUrls = array_diff($submittedFileUrls, $existingFileUrls);
        foreach ($newFileUrls as $fileUrl) {
            $filenameWithUuidExt = basename($fileUrl);
            $pathInfo = pathinfo($filenameWithUuidExt);
            $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
            $filenameWithUuid = $pathInfo['filename'];

            $lastDashPosition = strrpos($filenameWithUuid, '-');
            $name = substr($filenameWithUuid, 0, $lastDashPosition);
            $uuid = substr($filenameWithUuid, $lastDashPosition + 1);

            $filePath = FCPATH . $fileUrl;
            $size = file_exists($filePath) ? filesize($filePath) : 0;

            $fileEntity = new \Models\Entities\File();
            $fileEntity->setName($name);
            $fileEntity->setUuid($uuid);
            $fileEntity->setExt($extension);
            $fileEntity->setSize($size / 1024);
            $fileEntity->setPath($fileUrl);
            $fileEntity->setCreateDate(new \DateTime());
            $fileEntity->setCombinedName($filenameWithUuidExt);

            $article = $this->em->getRepository('Models\Entities\Article')->find($articleId);
            if ($article) {
                $fileEntity->setArticle($article);
            }

            $this->em->persist($fileEntity);
        }
        $this->em->flush();
    }
}
