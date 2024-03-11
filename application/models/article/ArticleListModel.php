<?
defined('BASEPATH') or exit('No direct script access allowed');

class ArticleListModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getArticlesByBoardIdAndPage($boardId, $currentPage, $articlesPerPage)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.articleBoard = :boardId')
            ->orderBy('a.createDate', 'DESC')
            ->setFirstResult(($currentPage - 1) * $articlesPerPage)
            ->setMaxResults($articlesPerPage)
            ->setParameter('boardId', $boardId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function getAllArticlesByBoardId($boardId)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.articleBoard = :boardId')
            ->orderBy('a.createDate', 'DESC')
            ->setParameter('boardId', $boardId);

        return $queryBuilder->getQuery()->getResult();
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
