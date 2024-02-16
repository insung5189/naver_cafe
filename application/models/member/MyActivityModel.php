<?
defined('BASEPATH') or exit('직접적인 스크립트 접근은 허용되지 않습니다.');

class MyActivityModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('doctrine');
        $this->load->library('session');
        $this->em = $this->doctrine->em;
    }

    public function getArticleCount($userId) {
        $qb = $this->em->createQueryBuilder();
        $qb->select('count(a.id)')
           ->from('Models\Entities\Article', 'a')
           ->where('a.member = :userId')
           ->setParameter('userId', $userId);
        
        $query = $qb->getQuery();
        return $query->getSingleScalarResult();
    }

    public function getCommentCount($userId) {
        $qb = $this->em->createQueryBuilder();
        $qb->select('count(c.id)')
           ->from('Models\Entities\Comment', 'c')
           ->where('c.member = :userId')
           ->setParameter('userId', $userId);
        
        $query = $qb->getQuery();
        return $query->getSingleScalarResult();
    }

}