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

    public function processCreateNewArticle($formData)
    {
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

            return ['success' => true, 'articleId' => $article->getId()];
        } catch (\Exception $e) {
            return ['success' => false, 'errors' => ['message' => '게시글 작성 중 오류 발생: ' . $e->getMessage()]];
        }
    }

    public function processCreateReplyArticle($formData)
    {
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

            return ['success' => true, 'articleId' => $article->getId()];
        } catch (\Exception $e) {
            return ['success' => false, 'errors' => ['message' => '답글 작성 중 오류 발생: ' . $e->getMessage()]];
        }
    }
}
