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
            ->join('Models\Entities\Article', 'a', 'WITH', 'c.article = a.id')
            ->where('c.member = :memberId')
            ->andWhere('c.isActive = 1') // 댓글이 활성 상태인지 확인
            ->andWhere('a.isActive = 1') // 게시글이 활성 상태인지 확인
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

    public function getCommentsByMemberIdByPage($memberId, $currentPage, $commentsPerPage)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('c')
            ->from('Models\Entities\Comment', 'c')
            ->join('Models\Entities\Article', 'a', 'WITH', 'c.article = a.id') // 댓글이 속한 게시글 조인
            ->where('c.member = :memberId')
            ->andWhere('c.isActive = 1') // 댓글이 활성 상태인지 확인
            ->andWhere('a.isActive = 1') // 게시글이 활성 상태인지 확인
            ->orderBy('c.createDate', 'DESC')
            ->setParameter('memberId', $memberId)
            ->setFirstResult(($currentPage - 1) * $commentsPerPage)
            ->setMaxResults($commentsPerPage);

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
            ->andWhere('c.isActive = 1')
            ->groupBy('c.article')
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

    /**
     * 내가 좋아요를 누른 게시물들을 페이징 처리 후 객체화 하고 그 총 갯수까지 리턴함.
     */
    public function getLikedArticlesByMemberIdWithCount($memberId, $currentPage, $articlesPerPage)
    {
        $articlesByPage = [];
        $totalCountLikedArticles = 0;

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

    /**
     * 게시글 삭제(비활성화)로직, 사용자와 작성자를 검증하는 과정에서 파라미터가 중복되게 인식되어 select와 update문을 메서드 단위로 분리함. 
     */
    public function softDeleteArticles($memberId, array $articleIds)
    {
        try {
            $queryBuilder = $this->em->createQueryBuilder();
            $allArticleIds = $this->getAllArticleIds($articleIds);

            // 사용자가 권한을 가진 게시글 ID 확인
            $query = $queryBuilder
                ->select('a.id')
                ->from('Models\Entities\Article', 'a')
                ->where('a.member = :memberId AND a.id IN (:articleIds)')
                ->setParameter('memberId', $memberId)
                ->setParameter('articleIds', $articleIds)
                ->getQuery();
            $ownedArticleIds = array_map(function ($article) {
                return $article['id'];
            }, $query->getResult());

            // 실제로 존재하면서 권한이 있는 게시글 삭제하고 삭제된 게시글 ID 기록
            $deletedArticleIds = $this->updateArticleStatus($ownedArticleIds);

            // 권한이 없는 게시글 ID 구분
            $unauthorizedIds = array_diff($allArticleIds, $ownedArticleIds);
            $unauthorizedCount = count($unauthorizedIds);

            // 존재하지 않는 게시글 ID 구분
            $nonExistentIds = array_diff($articleIds, $allArticleIds);
            $nonExistentCount = count($nonExistentIds);

            $messages = [];
            if (!empty($deletedArticleIds)) {
                $messages[] = count($deletedArticleIds) . "개의 게시글이 성공적으로 삭제되었습니다.(id : " . implode(", ", $deletedArticleIds) . ")";
            }
            if (!empty($unauthorizedIds)) {
                $messages[] = "삭제 권한이 없는 게시글이 " . $unauthorizedCount . "개 있습니다.(id : " . implode(", ", $unauthorizedIds) . ")";
            }
            if (!empty($nonExistentIds)) {
                $messages[] = "존재하지 않는 게시글이 " . $nonExistentCount . "개 있습니다.(id : " . implode(", ", $nonExistentIds) . ")";
            }

            $message = implode("\n", $messages);

            return ['success' => true, 'message' => $message];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => '게시글 삭제 중 오류가 발생했습니다. 오류 메시지: ' . $e->getMessage()];
        }
    }

    private function getAllArticleIds(array $articleIds)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $query = $queryBuilder
            ->select('a.id')
            ->from('Models\Entities\Article', 'a')
            ->where('a.id IN (:articleIds)')
            ->setParameter('articleIds', $articleIds)
            ->getQuery();

        return array_map(function ($article) {
            return $article['id'];
        }, $query->getResult());
    }

    private function updateArticleStatus(array $ownedArticleIds)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $deletedArticleIds = [];

        foreach ($ownedArticleIds as $articleId) {
            $query = $queryBuilder
                ->update('Models\Entities\Article', 'a')
                ->set('a.isActive', 0)
                ->set('a.deletedDate', ':deleteTime')
                ->where('a.id = :articleId')
                ->setParameter('deleteTime', new \DateTime())
                ->setParameter('articleId', $articleId)
                ->getQuery();
            $query->execute();
            $deletedArticleIds[] = $articleId;
        }

        return $deletedArticleIds; // 삭제된 게시글 ID 반환
    }

    /**
     * 댓글 삭제(비활성화)로직, 사용자와 작성자를 검증하는 과정에서 파라미터가 중복되게 인식되어 select와 update문을 메서드 단위로 분리함. 
     */
    public function softDeleteComments($memberId, array $commentIds)
    {
        try {
            $queryBuilder = $this->em->createQueryBuilder();
            $allCommentIds = $this->getAllCommentIds($commentIds);

            // 사용자가 권한을 가진 댓글 ID 확인
            $query = $queryBuilder
                ->select('c.id')
                ->from('Models\Entities\Comment', 'c')
                ->where('c.member = :memberId AND c.id IN (:commentIds)')
                ->setParameter('memberId', $memberId)
                ->setParameter('commentIds', $commentIds)
                ->getQuery();
            $ownedCommentIds = array_map(function ($comment) {
                return $comment['id'];
            }, $query->getResult());

            // 실제로 존재하면서 권한이 있는 댓글 삭제하고 삭제된 댓글 ID 기록
            $deletedCommentIds = $this->updateCommentStatus($ownedCommentIds);

            // 권한이 없는 댓글 ID 구분
            $unauthorizedIds = array_diff($allCommentIds, $ownedCommentIds);
            $unauthorizedCount = count($unauthorizedIds);

            // 존재하지 않는 댓글 ID 구분
            $nonExistentIds = array_diff($commentIds, $allCommentIds);
            $nonExistentCount = count($nonExistentIds);

            $messages = [];
            if (!empty($deletedCommentIds)) {
                $messages[] = count($deletedCommentIds) . "개의 댓글이 성공적으로 삭제되었습니다.(id : " . implode(", ", $deletedCommentIds) . ")";
            }
            if (!empty($unauthorizedIds)) {
                $messages[] = "삭제 권한이 없는 댓글이 " . $unauthorizedCount . "개 있습니다.(id : " . implode(", ", $unauthorizedIds) . ")";
            }
            if (!empty($nonExistentIds)) {
                $messages[] = "존재하지 않는 댓글이 " . $nonExistentCount . "개 있습니다.(id : " . implode(", ", $nonExistentIds) . ")";
            }

            $message = implode("\n", $messages);

            return ['success' => true, 'message' => $message];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => '댓글 삭제 중 오류가 발생했습니다. 오류 메시지: ' . $e->getMessage()];
        }
    }

    private function getAllCommentIds(array $commentIds)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $query = $queryBuilder
            ->select('c.id')
            ->from('Models\Entities\Comment', 'c')
            ->where('c.id IN (:commentIds)')
            ->setParameter('commentIds', $commentIds)
            ->getQuery();

        return array_map(function ($comment) {
            return $comment['id'];
        }, $query->getResult());
    }

    private function updateCommentStatus(array $ownedCommentIds)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $deletedCommentIds = [];

        foreach ($ownedCommentIds as $commentId) {
            $query = $queryBuilder
                ->update('Models\Entities\Comment', 'c')
                ->set('c.isActive', 0)
                ->set('c.deletedDate', ':deleteTime')
                ->where('c.id = :commentId')
                ->setParameter('deleteTime', new \DateTime())
                ->setParameter('commentId', $commentId)
                ->getQuery();
            if ($query->execute()) {
                $deletedCommentIds[] = $commentId;
            }
        }

        return $deletedCommentIds; // 삭제된 댓글 ID 반환
    }

    /**
     * 좋아요 취소(Likes맵핑테이블에서 row제거)로직.
     */
    public function cancelArticleLikes($memberId, array $articleIds)
    {
        try {
            $queryBuilder = $this->em->createQueryBuilder();

            // 존재하는 모든 게시글 ID 확인
            $allArticleIds = $this->getAllArticleIds($articleIds);

            // 현재 사용자가 좋아요한 게시글 ID 확인
            $likedArticleIdsQuery = $queryBuilder
                ->select('IDENTITY(l.article) as articleId')
                ->from('Models\Entities\Likes', 'l')
                ->where('l.member = :memberId AND l.article IN (:articleIds)')
                ->setParameter('memberId', $memberId)
                ->setParameter('articleIds', $articleIds)
                ->getQuery();
            $likedArticleIdsResult = $likedArticleIdsQuery->getResult();
            $likedArticleIds = array_column($likedArticleIdsResult, 'articleId');

            // 좋아요 해제 처리 및 처리된 ID들 확인
            $deletedLikesIds = $this->removeLikes($memberId, $likedArticleIds);

            // 권한이 없는 게시글 ID 및 존재하지 않는 게시글 ID 구분
            $unauthorizedArticleIds = array_diff($articleIds, $likedArticleIds);
            $nonExistentArticleIds = array_diff($articleIds, $allArticleIds);

            $messages = [];
            if (!empty($deletedLikesIds)) {
                $messages[] = count($deletedLikesIds) . "개의 좋아요가 성공적으로 해제되었습니다.(id : " . implode(", ", $deletedLikesIds) . ")";
            }
            if (!empty($unauthorizedArticleIds)) {
                $messages[] = "해당 id는 좋아요 취소권한이 없습니다.(id : " . implode(", ", $unauthorizedArticleIds) . ")";
            }
            if (!empty($nonExistentArticleIds)) {
                $messages[] = "존재하지 않는 게시글id 입니다.(id : " . implode(", ", $nonExistentArticleIds) . ")";
            }

            $message = implode("\n", $messages);

            return ['success' => true, 'message' => $message];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => '좋아요 해제 중 오류가 발생했습니다. 오류 메시지: ' . $e->getMessage()];
        }
    }

    private function removeLikes($memberId, array $likedArticleIds)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        // 좋아요 해제 처리
        $query = $queryBuilder
            ->delete('Models\Entities\Likes', 'l')
            ->where('l.member = :memberId AND l.article IN (:articleIds)')
            ->setParameter('memberId', $memberId)
            ->setParameter('articleIds', $likedArticleIds)
            ->getQuery();

        $query->execute();
        // 성공적으로 처리된 좋아요(ID) 반환
        return $likedArticleIds;
    }
}
