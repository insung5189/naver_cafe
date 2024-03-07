<?
defined('BASEPATH') or exit('No direct script access allowed');

class MyActivityModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getArticleCountByMemberId($memberId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('count(a.id)')
            ->from('Models\Entities\Article', 'a')
            ->where('a.member = :memberId')
            ->andWhere('a.isActive = 1')
            ->setParameter('memberId', $memberId);

        $query = $queryBuilder->getQuery();
        return $query->getSingleScalarResult();
    }

    public function getCommentCountByMemberId($memberId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('count(c.id)')
            ->from('Models\Entities\Comment', 'c')
            ->where('c.member = :memberId')
            ->andWhere('c.isActive = 1')
            ->setParameter('memberId', $memberId);

        $query = $queryBuilder->getQuery();
        return $query->getSingleScalarResult();
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

    public function getCommentsByMemberIdByPage($memberId, $currentPage, $articlesPerPage)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('c')
            ->from('Models\Entities\Comment', 'c')
            ->where('c.member = :memberId')
            ->andWhere('c.isActive = 1')
            ->orderBy('c.createDate', 'DESC')
            ->setParameter('memberId', $memberId)
            ->setFirstResult(($currentPage - 1) * $articlesPerPage)
            ->setMaxResults($articlesPerPage);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * 내가 남긴 댓글을 가지고있는 게시물의 댓글 수를 파악함.
     */
    public function getCommentCountByMemberArticles($memberId)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        // 사용자가 작성한 게시글에 대한 댓글 수를 조회
        $queryBuilder->select('a.id AS articleId, COUNT(c.id) AS commentCount')
            ->from('Models\Entities\Article', 'a')
            ->leftJoin('Models\Entities\Comment', 'c', 'WITH', 'a.id = c.article AND c.isActive = 1')
            ->where('a.member = :memberId')
            ->groupBy('a.id') // 각 게시글별로 그룹화
            ->setParameter('memberId', $memberId);

        $results = $queryBuilder->getQuery()->getResult();

        // 결과를 게시글 ID를 키로 하고, 댓글 수를 값으로 하는 배열로 변환
        $commentCountByArticle = [];
        foreach ($results as $result) {
            $commentCountByArticle[$result['articleId']] = $result['commentCount'];
        }

        return $commentCountByArticle;
    }

    /**
     * 내가 남긴 댓글을 가지고있는 게시물들을 객체화 하여 리턴함함.
     */
    public function getArticlesCommentedByMember($memberId)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        // 댓글을 남긴 게시글의 ID를 찾음
        $queryBuilder->select('DISTINCT IDENTITY(c.article) as articleId')
            ->from('Models\Entities\Comment', 'c')
            ->where('c.member = :memberId')
            ->setParameter('memberId', $memberId);

        $articleIds = $queryBuilder->getQuery()->getResult();

        // 찾은 게시글 ID를 기반으로 게시글 정보 조회
        $articles = [];
        foreach ($articleIds as $articleId) {
            $article = $this->em->getRepository('Models\Entities\Article')->find($articleId['articleId']);
            if ($article) {
                $articles[] = $article;
            }
        }

        return $articles;
    }

    public function getDeletedArticlesByMemberIdByPage($memberId, $currentPage, $articlesPerPage)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.member = :memberId')
            ->andWhere('a.isActive = 0')
            ->orderBy('a.createDate', 'DESC')
            ->setParameter('memberId', $memberId)
            ->setFirstResult(($currentPage - 1) * $articlesPerPage)
            ->setMaxResults($articlesPerPage);

        return $queryBuilder->getQuery()->getResult();
    }

    public function softDeleteArticles(array $articleIds)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $query = $queryBuilder
            ->update('Models\Entities\Article', 'a')
            ->set('a.isActive', ':isActive')
            ->set('a.deletedDate', ':deleteTime')
            ->where('a.id IN (:articleIds)')
            ->setParameter('isActive', 0)
            ->setParameter('deleteTime', new \DateTime())
            ->setParameter('articleIds', $articleIds)
            ->getQuery();

        return $query->execute();
    }
}
