<?
defined('BASEPATH') or exit('No direct script access allowed');

class MainModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getArticleListAllImgs($currentPage, $articlesPerPage)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.isActive = 1')
            ->andWhere('a.depth = 0')
            ->andWhere('a.content LIKE :contentPattern')
            ->setParameter('contentPattern', '%<figure class="image"><img src="/assets/file/articleFiles/img/%')
            ->orderBy('a.orderGroup', 'DESC')
            ->setFirstResult(($currentPage - 1) * $articlesPerPage)
            ->setMaxResults($articlesPerPage);

        $articles = $queryBuilder->getQuery()->getResult();

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

    public function getFreeBoardArticles($currentPage, $articlesPerPage)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.isActive = 1')
            ->andWhere('a.depth = 0')
            ->andWhere('a.articleBoard = 1')
            ->orderBy('a.orderGroup', 'DESC')
            ->setFirstResult(($currentPage - 1) * $articlesPerPage)
            ->setMaxResults($articlesPerPage);

        $articles = $queryBuilder->getQuery()->getResult();

        return $articles;
    }

    public function getQnaArticles($currentPage, $articlesPerPage)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('a.articleBoard = 5')
            ->andWhere('a.isActive = 1')
            ->orderBy('a.orderGroup', 'DESC')
            ->setFirstResult(($currentPage - 1) * $articlesPerPage)
            ->setMaxResults($articlesPerPage);

        return $queryBuilder->getQuery()->getResult();
    }

    public function extractFirstImagePathsFromArticles($articles)
    {
        $firstImagePaths = [];

        foreach ($articles as $article) {
            $content = $article->getContent();
            $dom = new DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new DOMXPath($dom);
            $images = $xpath->query('//figure[contains(@class, "image")]/img');

            if ($images->length > 0) {
                $firstImgSrc = $images->item(0)->getAttribute('src');
                // 게시글의 ID를 키로 사용하여 이미지 URL을 저장합니다.
                $firstImagePaths[$article->getId()] = $firstImgSrc;
            }
        }

        return $firstImagePaths;
    }
}
