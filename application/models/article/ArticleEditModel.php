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
        $prefix = $formData['prefix'];
        $title = $formData['title'];
        $content = $formData['content'];
        $parentId = $formData['parentId'];
        $publicScope = $formData['publicScope'];

        $depth = 1;
        if ($parentId) {
            $parentArticle = $this->em->getRepository('Models\Entities\Article')->find($parentId);
            if ($parentArticle) {
                $depth = $parentArticle->getDepth() + 1;
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

        $currentValidPrefixes = isset($validPrefixes[$boardId]) ? $validPrefixes[$boardId] : [];

        if (!in_array($prefix, $currentValidPrefixes)) {
            throw new Exception('유효하지 않은 말머리입니다.');
        }

        $userId = $this->session->userdata('user_data')['user_id'];
        $member = $this->em->find('Models\Entities\Member', $userId);
        if (!$member) {
            throw new Exception('작성자 정보를 찾을 수 없습니다.');
        }

        $article = new Models\Entities\Article();
        $article->setArticleBoard($board);
        $article->setMember($member);
        $article->setPrefix($prefix);
        $article->setTitle($title);
        $article->setContent($content);
        $article->setPublicScope($publicScope);
        $article->setDepth($depth);
        $article->setIp($_SERVER['REMOTE_ADDR']);

        if ($parentId && $parentArticle) {
            $article->setParent($parentArticle);
        }

        // 게시글 저장
        $this->em->persist($article);
        $this->em->flush();
    }
}
