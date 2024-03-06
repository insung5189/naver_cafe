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

    public function softDeleteArticles(array $articleIds)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $query = $queryBuilder
            ->update('Models\Entities\Article', 'a')
            ->set('a.isActive', ':isActive')
            ->where('a.id IN (:articleIds)')
            ->setParameter('isActive', 0)
            ->setParameter('articleIds', $articleIds)
            ->getQuery();

        return $query->execute();
    }
}
