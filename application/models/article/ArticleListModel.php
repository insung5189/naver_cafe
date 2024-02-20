<?
defined('BASEPATH') or exit('직접적인 스크립트 접근은 허용되지 않습니다.');

class ArticleListModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('doctrine');
        $this->load->library('session');
        $this->em = $this->doctrine->em;
    }

    public function getAllArticles()
    {
        $query = $this->em->createQuery('SELECT a FROM Models\Entities\Article a');
        return $query->getResult();
    }

    public function getTotalArticleCount()
    {
        $query = $this->em->createQuery('SELECT COUNT(a.id) FROM Models\Entities\Article a');
        return $query->getSingleScalarResult();
    }

    public function getArticlesByPage($currentPage, $articlesPerPage)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->orderBy('a.createDate', 'DESC') // 최신순 정렬
            ->setFirstResult(($currentPage - 1) * $articlesPerPage)
            ->setMaxResults($articlesPerPage);

        return $queryBuilder->getQuery()->getResult();
    }
}
