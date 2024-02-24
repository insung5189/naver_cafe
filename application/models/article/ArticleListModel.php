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
            ->orderBy('a.createDate', 'DESC')
            ->setFirstResult(($currentPage - 1) * $articlesPerPage)
            ->setMaxResults($articlesPerPage);

        return $queryBuilder->getQuery()->getResult();
    }

    public function searchArticles($keyword, $element, $period, $startDate = null, $endDate = null, $currentPage, $articlesPerPage)
    {
        $errors = $this->validateInputs($keyword, $element, $period, $startDate, $endDate);
        if (!empty($errors)) {
            return ['errors' => $errors];
        }
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')->from('Models\Entities\Article', 'a');

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
                            ->where('c.content LIKE :keyword')
                            ->getDQL();

                        // 서브쿼리 결과(키워드를 포함하는 댓글이 있는 게시글 ID)를 기준으로 게시글 검색
                        $queryBuilder->orWhere($queryBuilder->expr()->in('a.id', $subQuery));

                        $queryBuilder->setParameter('keyword', '%' . $keyword . '%');
                        break;
                    case 'title':
                        $queryBuilder->andWhere('a.title LIKE :keyword');
                        break;
                    case 'author':
                        $queryBuilder->join('a.member', 'm')
                            ->andWhere('m.nickName LIKE :keyword');
                        break;
                    case 'comment':
                        $queryBuilder->join('Models\Entities\Comment', 'c', 'WITH', 'c.article = a.id')
                            ->andWhere('c.content LIKE :keyword');
                        break;
                    case 'commentAuthor':
                        $queryBuilder->join('Models\Entities\Comment', 'c', 'WITH', 'c.article = a.id')
                            ->join('Models\Entities\Member', 'm', 'WITH', 'c.member = m.id')
                            ->andWhere('m.userName LIKE :keyword');
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

        $queryBuilder->orderBy('a.createDate', 'DESC')
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

        $errors = $this->validateInputs($keyword, $element, $period, $startDate, $endDate);
        if (!empty($errors)) {
            return ['errors' => $errors];
        }
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('COUNT(a.id)')->from('Models\Entities\Article', 'a');

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
                            ->where('c.content LIKE :keyword')
                            ->getDQL();

                        $queryBuilder->orWhere($queryBuilder->expr()->in('a.id', $subQuery));

                        $queryBuilder->setParameter('keyword', '%' . $keyword . '%');
                        break;
                    case 'title':
                        $queryBuilder->andWhere('a.title LIKE :keyword');
                        break;
                    case 'author':
                        $queryBuilder->join('a.member', 'm')
                            ->andWhere('m.nickName LIKE :keyword');
                        break;
                    case 'comment':
                        $queryBuilder->join('Models\Entities\Comment', 'c', 'WITH', 'c.article = a.id')
                            ->andWhere('c.content LIKE :keyword');
                        break;
                    case 'commentAuthor':
                        $queryBuilder->join('Models\Entities\Comment', 'c', 'WITH', 'c.article = a.id')
                            ->join('Models\Entities\Member', 'm', 'WITH', 'c.member = m.id')
                            ->andWhere('m.userName LIKE :keyword');
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

        // 키워드 길이제한
        if (!empty($keyword) && strlen($keyword) > 20) {
            $errors['keyword'] = '키워드는 20자를 초과할 수 없습니다.';
        }

        // 검색 요소 유효성 검사
        $validElements = ['all', 'article-comment', 'title', 'author', 'comment', 'commentAuthor'];
        if (!empty($element) && !in_array($element, $validElements)) {
            $errors['element'] = '지원되지 않는 검색 요소입니다.';
        }

        // 기간 유효성 검사
        $validPeriods = ['all', '1day', '1week', '1month', '6months', '1year', 'custom'];
        if (!empty($period) && !in_array($period, $validPeriods)) {
            $errors['period'] = '지원되지 않는 기간입니다.';
        }

        // 날짜 유효성 검사
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
