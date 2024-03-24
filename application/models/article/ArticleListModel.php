<?
defined('BASEPATH') or exit('No direct script access allowed');

class ArticleListModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getArticlesByBoardIdAndPage($boardId, $currentPage = null, $articlesPerPage = null)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.articleBoard = :boardId')
            ->andWhere('a.depth >= 0') // 모든 게시글을 선택
            ->andWhere('a.isActive = 1') // 활성화된 게시글만
            ->orderBy('a.orderGroup', 'DESC')
            ->addOrderBy('a.createDate', 'ASC')
            ->setParameter('boardId', $boardId);

        $results = $queryBuilder->getQuery()->getResult();

        // 결과집합이 비어 있는지 확인
        if (empty($results)) {
            // 결과가 없을 경우, 빈 배열 반환 또는 예외 처리
            return [];
        }

        // 게시글을 orderGroup별로 묶어 정렬
        $groupedArticles = [];
        foreach ($results as $article) {
            $groupedArticles[$article->getOrderGroup()][] = $article;
        }

        $orderGroupByArticles = [];
        // 각 orderGroup 내에서 게시글을 부모-자식 관계에 따라 정렬
        foreach ($groupedArticles as $orderGroup => $articles) {
            $orderGroupByArticles[$orderGroup] = $this->sortArticlesByParentChildRelationship($articles);
        }

        // 배열 평탄화
        $flattenedArticles = array_merge(...array_values($orderGroupByArticles));

        // 페이징 처리 조건 확인 및 적용
        if ($currentPage !== null && $articlesPerPage !== null) {
            $pagedArticles = array_slice(
                $flattenedArticles,
                ($currentPage - 1) * $articlesPerPage,
                $articlesPerPage
            );
        } else {
            $pagedArticles = $flattenedArticles;
        }

        return $pagedArticles;
    }

    protected function sortArticlesByParentChildRelationship($articles)
    {
        $tempArray = [];
        foreach ($articles as $article) {
            $parentId = $article->getParent() ? $article->getParent()->getId() : null;
            if ($parentId !== null) {
                $positionFound = false;
                foreach ($tempArray as $index => $tempArticle) {
                    if ($tempArticle->getId() == $parentId) {
                        $insertPosition = $index + 1;
                        while ($insertPosition < count($tempArray) && $tempArray[$insertPosition]->getParent() && $tempArray[$insertPosition]->getParent()->getId() == $parentId) {
                            $insertPosition++;
                        }
                        array_splice($tempArray, $insertPosition, 0, [$article]);
                        $positionFound = true;
                        break;
                    }
                }
                if (!$positionFound) {
                    $tempArray[] = $article; // 부모가 없거나 아직 배열에 추가되지 않은 경우
                }
            } else {
                $tempArray[] = $article; // 최상위 레벨 게시글
            }
        }
        return $tempArray;
    }

    public function getAllArticlesByBoardId($boardId)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.articleBoard = :boardId')
            ->andWhere('a.isActive = 1')
            ->orderBy('a.orderGroup', 'DESC')
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

    public function searchArticles($boardId, $keyword, $element, $period, $startDate = null, $endDate = null, $currentPage, $articlesPerPage)
    {
        $errors = $this->validateInputs($keyword, $element, $period, $startDate, $endDate);
        if (!empty($errors)) {
            return ['errors' => $errors];
        }
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.isActive = 1')
            ->andWhere('a.articleBoard = :boardId')
            ->setParameter('boardId', $boardId);

        // 검색 조건 추가
        if (!empty($keyword) && $element !== 'all') {
            // 요소별 검색 (예: 제목, 작성자 등)
            if ($element !== 'all') {
                switch ($element) {
                    case 'article-comment':
                        $queryBuilder->andWhere(
                            $queryBuilder->expr()->orX(
                                $queryBuilder->expr()->like('a.title', ':keyword'),
                                $queryBuilder->expr()->like('a.content', ':keyword')
                            )
                        );

                        $subQuery = $this->em->createQueryBuilder()
                            ->select('IDENTITY(c.article)')
                            ->from('Models\Entities\Comment', 'c')
                            ->leftJoin('c.member', 'm')
                            ->where('c.content LIKE :keyword')
                            ->andWhere('c.isActive = 1')
                            ->andWhere('m.isActive = 1')
                            ->andWhere('m.blacklist = 0')
                            ->getDQL();

                        $queryBuilder->orWhere($queryBuilder->expr()->in('a.id', $subQuery));

                        // 게시글 작성자에 대한 조건 추가
                        $queryBuilder->leftJoin('a.member', 'am')
                            ->andWhere('am.isActive = 1')
                            ->andWhere('am.blacklist = 0');

                        $queryBuilder->setParameter('keyword', '%' . $keyword . '%');
                        break;
                    case 'title':
                        $queryBuilder->andWhere('a.title LIKE :keyword');
                        break;
                    case 'author':
                        $queryBuilder->join('a.member', 'm')
                            ->andWhere('m.nickName LIKE :keyword')
                            ->andWhere('m.isActive = 1')
                            ->andWhere('m.blacklist = 0');
                        break;
                    case 'comment':
                        $queryBuilder->join('Models\Entities\Comment', 'c', 'WITH', 'c.article = a.id')
                            ->andWhere('c.content LIKE :keyword')
                            ->andWhere('c.isActive = 1')
                            ->join('c.member', 'm2')
                            ->andWhere('m2.isActive = 1')
                            ->andWhere('m2.blacklist = 0');
                        break;
                    case 'commentAuthor':
                        $queryBuilder->join('Models\Entities\Comment', 'c', 'WITH', 'c.article = a.id')
                            ->join('c.member', 'm', 'WITH', 'm.id = c.member')
                            ->andWhere('m.nickName LIKE :keyword')
                            ->andWhere('m.isActive = 1')
                            ->andWhere('m.blacklist = 0');
                        break;
                }
                $queryBuilder->setParameter('keyword', '%' . $keyword . '%');
            }
        }

        // 기간 필터링 로직
        if ($period !== 'all') {
            // custom 기간 로직
            if ($period === 'custom') {
                if (!empty($startDate) && is_string($startDate)) {
                    $startDateObj = \DateTime::createFromFormat('Y-m-d', $startDate);
                    if ($startDateObj) {
                        $startDate = $startDateObj->setTime(0, 0, 0);
                    }
                } else {
                    $startDate = new \DateTime();
                }

                if (!empty($endDate) && is_string($endDate)) {
                    $endDateObj = \DateTime::createFromFormat('Y-m-d', $endDate);
                    if ($endDateObj) {
                        $endDate = $endDateObj->setTime(23, 59, 59);
                    }
                } else {
                    $endDate = new \DateTime();
                }
            } else {
                $endDate = new \DateTime();
                $startDate = $this->calculateStartDateBasedOnPeriod($period);
            }

            $queryBuilder->andWhere('a.createDate BETWEEN :startDate AND :endDate')
                ->setParameter('startDate', $startDate->format('Y-m-d H:i:s'))
                ->setParameter('endDate', $endDate->format('Y-m-d H:i:s'));
        }

        // 게시글 총 수 조회
        $totalQuery = clone $queryBuilder;
        $totalQuery->select('COUNT(a.id)');
        $totalCount = $totalQuery->getQuery()->getSingleScalarResult();

        if (isset($currentPage) || isset($currentPage)) {
            $queryBuilder->orderBy('a.orderGroup', 'DESC')
                ->setFirstResult(($currentPage - 1) * $articlesPerPage)
                ->setMaxResults($articlesPerPage);
        } else {
            $queryBuilder->orderBy('a.orderGroup', 'DESC');
        }
        $pagedResults = $queryBuilder->getQuery()->getResult();

        return [
            'results' => $pagedResults,
            'total' => $totalCount
        ];
    }

    private function calculateStartDateBasedOnPeriod($period)
    {
        switch ($period) {
            case '1day':
                return new \DateTime('-1 day');
            case '1week':
                return new \DateTime('-1 week');
            case '6months':
                return new \DateTime('-6 months');
            case '1year':
                return new \DateTime('-1 year');
            case '3years':
                return new \DateTime('-3 years');
            case '5years':
                return new \DateTime('-5 years');
            default:
                return new \DateTime();
        }
    }

    private function validateInputs($keyword, $element, $period, $startDate, $endDate)
    {
        $errors = [];

        if (!empty($keyword) && strlen($keyword) > 20) {
            $errors['keyword'] = '키워드는 20자를 초과할 수 없습니다.';
        }

        $validElements = ['all', 'article-comment', 'title', 'author', 'comment', 'commentAuthor'];
        if (!empty($element) && !in_array($element, $validElements)) {
            $errors['element'] = '지원되지 않는 검색 요소입니다.';
        }

        $validPeriods = ['all', '1day', '1week', '1month', '6months', '1year', 'custom'];
        if (!empty($period) && !in_array($period, $validPeriods)) {
            $errors['period'] = '지원되지 않는 기간입니다.';
        }

        $today = new DateTime();
        $today->setTime(23, 59, 59);

        $startDateObj = $startDate ? DateTime::createFromFormat('Y-m-d', $startDate) : null;
        $endDateObj = $endDate ? DateTime::createFromFormat('Y-m-d', $endDate) : null;

        if ($startDate && !$startDateObj) {
            $errors['startDate'] = '시작 날짜 형식이 유효하지 않습니다. (예: YYYY-MM-DD)';
        } else if ($startDateObj && $startDateObj > $today) {
            $errors['startDate'] = '시작 날짜는 오늘 날짜를 초과할 수 없습니다.';
        }

        if ($endDate && !$endDateObj) {
            $errors['endDate'] = '종료 날짜 형식이 유효하지 않습니다. (예: YYYY-MM-DD)';
        } else if ($endDateObj && $endDateObj > $today) {
            $errors['endDate'] = '종료 날짜는 오늘 날짜를 초과할 수 없습니다.';
        }

        if ($startDateObj && $endDateObj && $startDateObj > $endDateObj) {
            $errors['dateRange'] = '시작 날짜는 종료 날짜보다 이후일 수 없습니다.';
        }

        return $errors;
    }

    public function isBookmarkedByMember($memberId, $boardId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('count(bmk.boardBookmarkId)')
            ->from('Models\Entities\BoardBookmark', 'bmk')
            ->where('bmk.member = :memberId')
            ->andWhere('bmk.articleBoard = :boardId')
            ->setParameter('memberId', $memberId)
            ->setParameter('boardId', $boardId);

        $count = $queryBuilder->getQuery()->getSingleScalarResult();
        return $count > 0;
    }

    public function getFavoriteBoards($memberId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('ab', 'bb')
            ->from('Models\Entities\BoardBookmark', 'bb')
            ->innerJoin('bb.articleBoard', 'ab')
            ->where('bb.member = :memberId')
            ->setParameter('memberId', $memberId);

        $favoriteBoards = $queryBuilder->getQuery()->getResult();

        return $favoriteBoards;
    }
}
