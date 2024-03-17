<?
defined('BASEPATH') or exit('No direct script access allowed');

class MainController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('home/MainModel', 'MainModel');
    }

    public function index()
    {
        // 전체글보기 영역
        $articleListAllArticles = $this->MainModel->getArticleListAllImgs(1, 12);
        $articleListAllimgfileUrls = $this->MainModel->extractFirstImagePathsFromArticles($articleListAllArticles);
        $articleListAllarticleIds = array_map(function ($article) {
            return $article->getId();
        }, $articleListAllArticles);
        $articleListAllcommentCounts = $this->MainModel->getCommentCountForArticles($articleListAllarticleIds);

        // 자유게시판 영역
        $freeBoardArticles = $this->MainModel->getFreeBoardArticles(1, 4);
        $freeBoardArticlesimgfileUrls = $this->MainModel->extractFirstImagePathsFromArticles($freeBoardArticles);
        $freeBoardarticleIds = array_map(function ($article) {
            return $article->getId();
        }, $freeBoardArticles);
        $freeBoardArticlesCommentCounts = $this->MainModel->getCommentCountForArticles($freeBoardarticleIds);

        // 질문/답변게시판 영역
        $qnaBoardArticles = $this->MainModel->getQnaArticles(1, 13);
        $qnaBoardarticleIds = array_map(function ($article) {
            return $article->getId();
        }, $qnaBoardArticles);
        $qnaBoardArticlesCommentCounts = $this->MainModel->getCommentCountForArticles($qnaBoardarticleIds);


        $page_view_data = [
            'title' => '메인',
            'articleListAllArticles' => $articleListAllArticles,
            'articleListAllimgfileUrls' => $articleListAllimgfileUrls,
            'articleListAllcommentCounts' => $articleListAllcommentCounts,

            'freeBoardArticles' => $freeBoardArticles,
            'freeBoardArticlesimgfileUrls' => $freeBoardArticlesimgfileUrls,
            'freeBoardArticlesCommentCounts' => $freeBoardArticlesCommentCounts,

            'qnaBoardArticles' => $qnaBoardArticles,
            'qnaBoardArticlesCommentCounts' => $qnaBoardArticlesCommentCounts,
        ];
        $this->layout->view('dashboard/dashboard', $page_view_data);
    }
}
