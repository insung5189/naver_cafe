<?
defined('BASEPATH') or exit('No direct script access allowed');

class UserActivityModel extends MY_Model
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
        return $query->getOneOrNullResult();
    }

    public function getArticlesByMemberId($memberId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.member = :memberId')
            ->andWhere('a.isActive = 1')
            ->setParameter('memberId', $memberId);

        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    public function getArticlesByMemberIdByPage($memberId, $currentPage, $articlesPerPage)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.member = :memberId')
            ->andWhere('a.isActive = 1')
            ->orderBy('a.createDate', 'DESC')
            ->setParameter('memberId', $memberId)
            ->setFirstResult(($currentPage - 1) * $articlesPerPage)
            ->setMaxResults($articlesPerPage);

        return $queryBuilder->getQuery()->getResult();
    }

    /*
    위 getArticlesByMemberIdByPage($memberId, $currentPage, $articlesPerPage) 메서드를 SQL로 변경하면 아래와 같은 형태임
    SELECT a.* FROM article a
    WHERE a.member_id = 1
    AND a.is_active = 1
    ORDER BY a.create_date DESC
    LIMIT (최대값) OFFSET (현재세팅값)
     */


    public function checkParentArticlesExist($articles)
    {
        $result = [];
        foreach ($articles as $article) {
            $articleId = $article->getId();
            if (!empty($article->getParent())) {
                $articleParentId = $article->getParent()->getId();
                $parentArticleIsExsist = (bool)$this->getArticleById($articleParentId);
                $result[$articleId] = $parentArticleIsExsist;
            } else {
                // 부모 게시글이 없는 경우(최상위 게시글) 또는 게시글 자체가 존재하지 않는 경우
                $result[$articleId] = true;
            }
        }
        return $result;
    }

    /**
     * 내가 남긴 댓글을 가지고있는 게시물들을 객체화 하여 리턴함.
     */
    public function getArticlesCommentedByMember($memberId)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        // 댓글을 남긴 게시글의 ID를 찾음, 댓글과 게시글 모두 활성 상태인 경우만
        $queryBuilder->select('DISTINCT IDENTITY(c.article) as articleId')
            ->from('Models\Entities\Comment', 'c')
            ->where('c.member = :memberId')
            ->andWhere('c.isActive = 1') // 댓글이 활성 상태인지 확인
            ->setParameter('memberId', $memberId);

        $articleIds = $queryBuilder->getQuery()->getResult();

        // 찾은 게시글 ID를 기반으로 게시글 정보 조회, 게시글도 활성 상태인지 확인
        $articles = [];
        foreach ($articleIds as $articleId) {
            $article = $this->em->getRepository('Models\Entities\Article')->findOneBy([
                'id' => $articleId['articleId'],
                'isActive' => 1 // 게시글이 활성 상태인지 확인
            ]);
            if ($article) {
                $articles[] = $article;
            }
        }
        return $articles;
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

    /**
     * 내가 남긴 댓글을 가지고있는 게시물들을 페이징 처리 후 객체화 하여 리턴함.
     */
    public function getArticlesCommentedByMemberIdAndPage($memberId, $currentPage, $commentedArticlesPerPage)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder->select('DISTINCT IDENTITY(c.article) as articleId')
            ->from('Models\Entities\Comment', 'c')
            ->where('c.member = :memberId')
            ->andWhere('c.isActive = 1')
            ->orderBy('c.createDate', 'DESC')
            ->setParameter('memberId', $memberId)
            ->setFirstResult(($currentPage - 1) * $commentedArticlesPerPage)
            ->setMaxResults($commentedArticlesPerPage);

        $articleIds = $queryBuilder->getQuery()->getResult();

        // 찾은 게시글 ID를 기반으로 게시글 정보 조회, 게시글도 활성 상태인지 확인
        $commentedArticles = [];
        foreach ($articleIds as $articleId) {
            $article = $this->em->getRepository('Models\Entities\Article')->findOneBy([
                'id' => $articleId['articleId'],
                'isActive' => 1
            ]);
            if ($article) {
                $commentedArticles[] = $article;
            }
        }
        return $commentedArticles;
    }
}
