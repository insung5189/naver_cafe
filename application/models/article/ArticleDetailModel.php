<?
defined('BASEPATH') or exit('직접적인 스크립트 접근은 허용되지 않습니다.');

class ArticleDetailModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('doctrine');
        $this->load->library('session');
        $this->em = $this->doctrine->em;
    }

    private $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

    public function getArticleById($articleId)
    {
        $article = $this->doctrine->em->find('Models\Entities\Article', $articleId);
        if (!$article) {
            return null;
        }
        return $article;
    }

    public function getAuthorByArticle($articleId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('Models\Entities\Article', 'a')
            ->where('m.article = :articleId')
            ->setParameter('articleId', $articleId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function getCommentsByArticleId($articleId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('c')
            ->from('Models\Entities\Comment', 'c')
            ->where('c.article = :articleId')
            ->setParameter('articleId', $articleId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function getFilesByArticleId($articleId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('f')
            ->from('Models\Entities\File', 'f')
            ->where('f.article = :articleId')
            ->setParameter('articleId', $articleId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function getFileFullPath($file)
    {
        if (in_array($file->getExt(), $this->imageExtensions)) {
            $basePath = "assets/file/images/articleFiles/img/";
        } else if (in_array($file->getExt(), $this->documentExtensions)) {
            $basePath = "assets/file/images/articleFiles/doc/";
        } else {
            $basePath = "assets/file/images/articleFiles/others/";
        }
        return base_url($basePath . $file->getCombinedName());
    }

    public function getLikesByArticleId($articleId)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder->select('l')
            ->from('Models\Entities\Likes', 'l')
            ->where('l.article = :articleId')
            ->setParameter('articleId', $articleId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function createComment($formData, $fileData)
    {
        $comment = new Models\Entities\Comment();
        $comment->setArticle($this->em->find('Models\Entities\Article', $formData['articleId']));
        $comment->setMember($this->em->find('Models\Entities\Member', $formData['userId']));
        $comment->setContent($formData['content']);
        $comment->setCreateDate(new DateTime('now'));

        // 첨부 파일 처리
        if ($fileData && $this->isValidImage($fileData['name'])) {
            $imagePath = $this->uploadCommentImage($fileData);
            if ($imagePath) {
                $comment->setImage($imagePath); // 이미지 파일명 처리 확인할 것
            }
        }

        $this->em->persist($comment);
        $this->em->flush();
    }

    private function isValidImage($fileName)
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), $this->imageExtensions);
    }

    private function uploadCommentImage($file)
    {
        $targetDir = "assets/upload/comments/";
        $targetFile = $targetDir . basename($file["name"]);
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $targetFile;
        }
        return null;
    }
}
