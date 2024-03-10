<?
defined('BASEPATH') or exit('No direct script access allowed');

class MyActivityModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
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

    public function getCommentsByMemberId($memberId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('c')
            ->from('Models\Entities\Comment', 'c')
            ->where('c.member = :memberId')
            ->andWhere('c.isActive = 1')
            ->setParameter('memberId', $memberId);

        return $queryBuilder->getQuery()->getResult();
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
     * 내가 남긴 댓글을 가지고있는 게시물의 댓글 수를 파악함.(게시글은 내가 작성한것일수도, 내가 작성한 것이 아닐 수도 있음.)
     */
    public function getCommentCountByMemberArticles($commentIds)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        // 댓글 ID 배열을 기반으로 해당 댓글이 속한 게시글의 ID와, 해당 게시글에 대한 총 댓글 수를 조회
        $queryBuilder->select('IDENTITY(c.article) as articleId, COUNT(c.id) as commentCount')
            ->from('Models\Entities\Comment', 'c')
            ->where($queryBuilder->expr()->in('c.id', ':commentIds')) // c.id가 $commentIds 배열에 속하는 경우
            ->andWhere('c.isActive = 1') // 활성 상태인 댓글만 고려
            ->groupBy('c.article') // 댓글이 속한 게시글 기준으로 그룹화
            ->setParameter('commentIds', $commentIds);

        $results = $queryBuilder->getQuery()->getResult();

        // 결과를 게시글 ID를 키로 하고, 해당 게시글의 총 댓글 수를 값으로 하는 배열로 변환
        $commentCountByArticle = [];
        foreach ($results as $result) {
            $commentCountByArticle[$result['articleId']] = $result['commentCount'];
        }

        return $commentCountByArticle;
    }

    /**
     * 내가 남긴 댓글을 가지고있는 게시물들을 객체화 하여 리턴함.
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
            ->orderBy('c.createDate', 'DESC')
            ->setParameter('memberId', $memberId)
            ->setFirstResult(($currentPage - 1) * $commentedArticlesPerPage)
            ->setMaxResults($commentedArticlesPerPage);

        $articleIds = $queryBuilder->getQuery()->getResult();

        // 찾은 게시글 ID를 기반으로 게시글 정보 조회
        $commentedArticles = [];
        foreach ($articleIds as $articleId) {
            $article = $this->em->getRepository('Models\Entities\Article')->find($articleId['articleId']);
            if ($article) {
                $commentedArticles[] = $article;
            }
        }
        return $commentedArticles;
    }

    /**
     * 내가 좋아요를 누른 게시물들을 페이징 처리 후 객체화 하고 그 총 갯수까지 리턴함.
     */
    public function getLikedArticlesByMemberIdWithCount($memberId, $currentPage, $articlesPerPage)
    {
        // Likes 테이블에서 memberId에 해당하는 모든 좋아요 행을 찾습니다.
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('IDENTITY(l.article) as articleId')
            ->from('Models\Entities\Likes', 'l')
            ->where('l.member = :memberId')
            ->setParameter('memberId', $memberId);

        $result = $queryBuilder->getQuery()->getResult();

        // 결과에서 articleId만 추출합니다.
        $articleIds = array_map(function ($row) {
            return $row['articleId'];
        }, $result);

        $articles = [];
        $totalLikedArticles = 0;

        if (!empty($articleIds)) {
            // 전체 좋아요한 게시글 수를 계산합니다.
            $totalCountLikedArticles = count($articleIds);

            // 그 다음, Article 테이블에서 Likes 테이블에서 찾은 ID에 해당하는 게시글을 찾습니다.
            $queryBuilder = $this->em->createQueryBuilder();
            $queryBuilder->select('a')
                ->from('Models\Entities\Article', 'a')
                ->where($queryBuilder->expr()->in('a.id', $articleIds))
                ->andWhere('a.isActive = 1')
                ->orderBy('a.createDate', 'DESC')
                ->setFirstResult(($currentPage - 1) * $articlesPerPage)
                ->setMaxResults($articlesPerPage);

            $articlesByPage = $queryBuilder->getQuery()->getResult();
        }

        // 페이징된 게시글 리스트와 전체 게시글 수를 리턴합니다.
        return [
            'articlesByPage' => $articlesByPage,
            'totalCountLikedArticles' => $totalCountLikedArticles
        ];
    }

    /**
     * 내가 좋아요를 누른 게시물들을 Likes테이블에서 조회한 후 해당article의 id값을 기준으로 Article 테이블에서 해당하는 row들을 반환.
     */
    public function getAllLikedArticlesByMemberId($memberId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('IDENTITY(l.article) as articleId')
            ->from('Models\Entities\Likes', 'l')
            ->where('l.member = :memberId')
            ->setParameter('memberId', $memberId);

        $result = $queryBuilder->getQuery()->getResult();

        $articleIds = array_map(function ($row) {
            return $row['articleId'];
        }, $result);

        if (empty($articleIds)) {
            // 좋아요 한 게시글이 없다면 빈 배열을 반환
            return [];
        }

        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where($queryBuilder->expr()->in('a.id', $articleIds))
            ->orderBy('a.createDate', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * 내가 삭제한 게시물들을 페이징 처리 후 객체화 하여 리턴함.
     */
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

    /**
     * 내가 삭제한 게시물들을 객체화 하여 리턴함.
     */
    public function getDeletedArticlesByMemberId($memberId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.member = :memberId')
            ->andWhere('a.isActive = 0')
            ->orderBy('a.createDate', 'DESC')
            ->setParameter('memberId', $memberId);

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

    public function softDeleteComments(array $commentIds)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $query = $queryBuilder
            ->update('Models\Entities\Comment', 'c')
            ->set('c.isActive', ':isActive')
            ->set('c.deletedDate', ':deleteTime')
            ->where('c.id IN (:commentIds)')
            ->setParameter('isActive', 0)
            ->setParameter('deleteTime', new \DateTime())
            ->setParameter('commentIds', $commentIds)
            ->getQuery();

        return $query->execute();
    }
}
