<?
defined('BASEPATH') or exit('직접적인 스크립트 접근은 허용되지 않습니다.');

class ArticleEditModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('doctrine');
        $this->load->library('session');
        $this->em = $this->doctrine->em;
    }

    public function createArticle($formData)
    {
        $boardId = $formData['boardId'];
        $prefix = isset($formData['prefix']) ? $formData['prefix'] : NULL;
        $title = $formData['title'];
        $content = $formData['content'];
        $parentId = $formData['parentId'];
        $publicScope = $formData['publicScope'];

        $orderGroup = 0;
        if (!$parentId) {
            // 최상위 부모글인 경우, orderGroup을 새로 부여
            // 쿼리 빌더를 사용하여 최대 orderGroup 값을 찾음
            $queryBuilder = $this->em->createQueryBuilder();
            $queryBuilder->select($queryBuilder->expr()->max('a.orderGroup'))
                ->from('Models\Entities\Article', 'a');
            $maxOrderGroup = $queryBuilder->getQuery()->getSingleScalarResult();
            $orderGroup = $maxOrderGroup ? $maxOrderGroup + 1 : 1;
        } else {
            // 부모글이 있는 경우, 부모글의 orderGroup을 상속받음
            if ($parentArticle) {
                $orderGroup = $parentArticle->getOrderGroup();
            }
        }

        $board = $this->em->find('Models\Entities\ArticleBoard', $boardId);
        if (!$board) {
            throw new Exception('게시판을 찾을 수 없습니다.');
        }

        $validPrefixes = [
            '4' => ['PHP', 'MYSQL', 'APACHE', 'JS', 'HTML', 'CSS', '기타'],
            '5' => ['질문', '답변'],
        ];

        if (isset($validPrefixes[$boardId])) {
            $currentValidPrefixes = $validPrefixes[$boardId];
            if (!in_array($prefix, $currentValidPrefixes)) {
                throw new Exception('유효하지 않은 말머리입니다.');
            }
        } else {
            $prefix = NULL;
        }

        $memberId = $this->session->userdata('user_data')['user_id'];
        $member = $this->em->find('Models\Entities\Member', $memberId);
        if (!$member) {
            throw new Exception('작성자 정보를 찾을 수 없습니다.');
        }

        $article = new Models\Entities\Article();
        $article->setArticleBoard($board);
        $article->setMember($member);
        $article->setPrefix($prefix); // 조건에 따라 설정된 말머리 사용
        $article->setTitle($title);
        $article->setContent($content);
        $article->setPublicScope($publicScope);
        $article->setDepth($depth);
        $article->setIp($_SERVER['REMOTE_ADDR']);
        $article->setOrderGroup($orderGroup);

        if ($parentId && $parentArticle) {
            $article->setParent($parentArticle);
        }

        // 게시글 저장
        $this->em->persist($article);
        $this->em->flush();
    }
}
