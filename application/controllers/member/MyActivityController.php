<?
defined('BASEPATH') or exit('No direct script access allowed');
class MyActivityController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('member/MyActivityModel', 'MyActivityModel');
    }
    public function index()
    {
        $currentPage = $this->input->get('page') ?? 1;
        $articlesPerPage = $this->input->get('articlesPerPage') ? (int)$this->input->get('articlesPerPage') : 10;

        $userData = $this->session->userdata('user_data');
        $memberId = isset($userData['user_id']) ? $userData['user_id'] : null;

        if ($memberId) {
            $member = $this->em->getRepository('Models\Entities\Member')->find($memberId);
            $articles = $this->MyActivityModel->getArticlesByMemberIdByPage($memberId, $currentPage, $articlesPerPage);
            $totalArticleCount = $this->MyActivityModel->getArticleCountByMemberId($memberId);
            $totalPages = ceil($totalArticleCount / $articlesPerPage);
            $this->loadMyActivityArticlesList($member, $articles, $totalArticleCount, $currentPage, $totalPages, $articlesPerPage);
        } else {
            $page_view_data['title'] = '오류 발생';
            $this->layout->view('errors/error_page', $page_view_data);
        }
    }

    private function loadMyActivityArticlesList($member, $articles, $totalArticleCount, $currentPage, $totalPages, $articlesPerPage)
    {
        $memberPrflFileUrl = "/assets/file/images/memberImgs/";
        $commentFileUrl = "/assets/file/commentFiles/img/";

        $page_view_data = [
            'title' => '나의 활동',
            'member' => $member,
            'articles' => $articles,
            'totalArticleCountAll' => $totalArticleCount,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'articlesPerPage' => $articlesPerPage,
        ];
        $this->layout->view('member/my_activity', $page_view_data);
    }
}
