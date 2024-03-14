<?
defined('BASEPATH') or exit('No direct script access allowed');

class ArticleListAllModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getTotalArticleCount()
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('count(a.id)')
            ->from('Models\Entities\Article', 'a')
            ->where('a.isActive = 1')
            ->andWhere('a.depth = 0')
            ->orderBy('a.orderGroup', 'DESC');

        $query = $queryBuilder->getQuery();
        return $query->getSingleScalarResult();
    }

    public function getArticlesByPage($currentPage, $articlesPerPage)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.isActive = 1')
            ->andWhere('a.depth = 0')
            ->orderBy('a.orderGroup', 'DESC')
            ->setFirstResult(($currentPage - 1) * $articlesPerPage)
            ->setMaxResults($articlesPerPage);

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

    // 부모글의 id를 기준으로 찾음.
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

    public function checkChildArticlesParentExist($childArticles)
    {
        $result = [];
        foreach ($childArticles as $orderGroup => $articles) {
            foreach ($articles as $article) {
                $articleId = $article->getId();
                if (!empty($article->getParent())) {
                    $articleParentId = $article->getParent()->getId();
                    $parentArticleExists = (bool)$this->getArticleById($articleParentId);
                    $result[$articleId] = $parentArticleExists;
                } else {
                    $result[$articleId] = false;
                }
            }
        }
        return $result;
    }

    // 부모글의 orderGroup를 key로 하고 자식글들을 배열에 담는 메서드(전체글보기에서 자식글이 존재하면 접었다 폈다 하기 위함.)
    public function getChildArticles()
    {
        $childArticles = [];

        // 쿼리 빌더를 사용하여 자식 게시글들을 가져오는 쿼리 구성
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('article')
            ->from('Models\Entities\Article', 'article')
            ->innerJoin('article.parent', 'parent')
            ->where('article.depth > 0') // 자식 게시글인 것만 확보
            ->andWhere('article.isActive = 1') // 활성화된 게시글만
            ->orderBy('article.orderGroup', 'ASC');

        $results = $queryBuilder->getQuery()->getResult();

        // 결과를 부모 게시글 ID별로 정리
        foreach ($results as $article) {
            $orderGroup = $article->getOrderGroup();
            if (!isset($childArticles[$orderGroup])) {
                $childArticles[$orderGroup] = [];
            }
            $childArticles[$orderGroup][] = $article;
        }

        return $childArticles;
    }

    public function searchArticles($keyword, $element, $period, $startDate = null, $endDate = null, $currentPage, $articlesPerPage)
    {
        $errors = $this->validateInputs($keyword, $element, $period, $startDate, $endDate);
        if (!empty($errors)) {
            return ['errors' => $errors];
        }
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.isActive = 1');

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

        $queryBuilder->orderBy('a.orderGroup', 'DESC')
            ->setFirstResult(($currentPage - 1) * $articlesPerPage)
            ->setMaxResults($articlesPerPage);

        return $queryBuilder->getQuery()->getResult();
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

    public function getTotalArticleCountWithSearch($keyword, $element, $period, $startDate = null, $endDate = null)
    {
        // 검색 결과는 게시글의 갯수만 카운트 함.

        $errors = $this->validateInputs($keyword, $element, $period, $startDate, $endDate);
        if (!empty($errors)) {
            return ['errors' => $errors];
        }
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('COUNT(DISTINCT a.id)') // 댓글 검색 조건이 포함된 경우에도, 그 결과가 속한 게시글의 개수만을 카운트함.
            ->from('Models\Entities\Article', 'a')
            ->where('a.isActive = 1');

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
                        $queryBuilder->leftJoin('a.member', 'am')
                            ->andWhere('a.title LIKE :keyword')
                            ->andWhere('am.isActive = 1')
                            ->andWhere('am.blacklist = 0');
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

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
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
}
