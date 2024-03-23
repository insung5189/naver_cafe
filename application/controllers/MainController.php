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

    public function cafeInfo()
    {
        $page_view_data['title'] = '카페소개';
        $this->layout->view('cafeinfo/cafeinfo', $page_view_data);
    }

    // 전체검색 로직
    public function mainSearch()
    {
        $childArticles = [];
        $currentPage = $this->input->get('page', TRUE) ?? 1;
        $articlesPerPage = $this->input->get('articlesPerPage', TRUE) ? (int)$this->input->get('articlesPerPage') : 15;
        $keyword = $this->input->get('keyword') ?? '';
        $element = $this->input->get('element', TRUE) ?? '';
        $period = $this->input->get('period', TRUE) ?? '';
        $startDate = $this->input->get('startDate', TRUE) ?? '';
        $endDate = $this->input->get('endDate', TRUE) ?? '';

        if (!empty($keyword) && !empty($period) || !empty($startDate) || !empty($endDate)) {

            $result = $this->MainModel->searchArticles($keyword, $element, $period, $startDate, $endDate, $currentPage, $articlesPerPage);

            if (isset($result['errors'])) {
                // 검색결과에 오류 발생 시
                $errors = $result['errors'];
                $articles = $this->MainModel->getArticlesByPage($currentPage, $articlesPerPage); // 기존 게시글은 depth가 0인것, isActive가 1인것만 불러옴.
                $childArticles = $this->MainModel->getChildArticles(); // 자식글은 부모글의 id값을 key값으로 하여 배열에 저장하고 페이지에서 부모글 밑에 조건부로 foreach문으로 반복나열함.
                $totalArticleCount = $this->MainModel->getTotalArticleCount();
                $totalArticleCountForPaginaion = $this->MainModel->getTotalArticleCountForPagination();
            } else {
                // 검색결과에 오류 없을 때
                $articles = $result; // 검색키워드가 있고, 오류가 없을 땐 검색결과 전부를 페이징 해서 불러옴.
                $totalArticleCount = $this->MainModel->getTotalArticleCountWithSearch($keyword, $element, $period, $startDate, $endDate);
                $childArticles = $this->MainModel->getChildArticles(); // 자식글은 부모글의 id값을 key값으로 하여 배열에 저장하고 페이지에서 부모글 밑에 조건부로 foreach문으로 반복나열함.
                $totalArticleCountForPaginaion = $this->MainModel->getTotalArticleCountWithSearch($keyword, $element, $period, $startDate, $endDate, $currentPage, $articlesPerPage);
            }
        } else {
            // 검색하지 않았을 때
            $articles = $this->MainModel->getArticlesByPage($currentPage, $articlesPerPage); // 기존 게시글은 depth가 0인것, isActive가 1인것만 불러옴.
            $childArticles = $this->MainModel->getChildArticles(); // 자식글은 부모글의 id값을 key값으로 하여 배열에 저장하고 페이지에서 부모글 밑에 조건부로 foreach문으로 반복나열함.
            $totalArticleCount = $this->MainModel->getTotalArticleCount();
            $totalArticleCountForPaginaion = $this->MainModel->getTotalArticleCountForPagination();
        }

        $totalPages = ceil($totalArticleCountForPaginaion / $articlesPerPage);

        $parentArticlesExist = $this->MainModel->checkChildArticlesParentExist($childArticles);

        // 게시글 ID 배열 생성
        $articleIds = array_map(function ($article) {
            return $article->getId();
        }, $articles);

        // 게시글별 댓글 개수 조회
        $commentCounts = $this->MainModel->getCommentCountForArticles($articleIds);

        $page_view_data = [
            'title' => '카페 통합검색 결과',
            'articles' => $articles,
            'commentCounts' => $commentCounts,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'articlesPerPage' => $articlesPerPage,
            'totalArticleCountAll' => $totalArticleCount,
            'parentArticlesExist' => $parentArticlesExist,
            'childArticles' => $childArticles,
            'keyword' => $keyword,
            'element' => $element,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'errors' => $errors ?? []
        ];

        // 데이터와 함께 뷰 로드
        $this->layout->view('dashboard/main_search', $page_view_data);
    }

    public function fetchArticles()
    {
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {
                $childArticles = [];
                $currentPage = $this->input->get('page', TRUE) ?? 1;
                $articlesPerPage = $this->input->get('articlesPerPage', TRUE) ? (int)$this->input->get('articlesPerPage') : 15;
                $keyword = $this->input->get('keyword', TRUE) ?? '';
                $element = $this->input->get('element', TRUE) ?? '';
                $period = $this->input->get('period', TRUE) ?? '';
                $startDate = $this->input->get('startDate', TRUE) ?? '';
                $endDate = $this->input->get('endDate', TRUE) ?? '';

                if (!empty($keyword) && !empty($period) || !empty($startDate) || !empty($endDate)) {

                    $result = $this->MainModel->searchArticles($keyword, $element, $period, $startDate, $endDate, $currentPage, $articlesPerPage);

                    if (isset($result['errors'])) {
                        // 검색결과에 오류 발생 시
                        $errors = $result['errors'];
                        $articles = $this->MainModel->getArticlesByPage($currentPage, $articlesPerPage); // 기존 게시글은 depth가 0인것, isActive가 1인것만 불러옴.
                        $childArticles = $this->MainModel->getChildArticles(); // 자식글은 부모글의 id값을 key값으로 하여 배열에 저장하고 페이지에서 부모글 밑에 조건부로 foreach문으로 반복나열함.
                        $totalArticleCount = $this->MainModel->getTotalArticleCount();
                        $totalArticleCountForPaginaion = $this->MainModel->getTotalArticleCountForPagination();
                    } else {
                        // 검색결과에 오류 없을 때
                        $articles = $result; // 검색키워드가 있고, 오류가 없을 땐 검색결과 전부를 페이징 해서 불러옴.
                        $totalArticleCount = $this->MainModel->getTotalArticleCountWithSearch($keyword, $element, $period, $startDate, $endDate);
                        $childArticles = $this->MainModel->getChildArticles(); // 자식글은 부모글의 id값을 key값으로 하여 배열에 저장하고 페이지에서 부모글 밑에 조건부로 foreach문으로 반복나열함.
                        $totalArticleCountForPaginaion = $this->MainModel->getTotalArticleCountWithSearch($keyword, $element, $period, $startDate, $endDate, $currentPage, $articlesPerPage);
                    }
                } else {
                    // 검색하지 않았을 때
                    $articles = $this->MainModel->getArticlesByPage($currentPage, $articlesPerPage); // 기존 게시글은 depth가 0인것, isActive가 1인것만 불러옴.
                    $childArticles = $this->MainModel->getChildArticles(); // 자식글은 부모글의 id값을 key값으로 하여 배열에 저장하고 페이지에서 부모글 밑에 조건부로 foreach문으로 반복나열함.
                    $totalArticleCount = $this->MainModel->getTotalArticleCount();
                    $totalArticleCountForPaginaion = $this->MainModel->getTotalArticleCountForPagination();
                }

                $totalPages = ceil($totalArticleCountForPaginaion / $articlesPerPage);

                $parentArticlesExist = $this->MainModel->checkChildArticlesParentExist($childArticles);

                // 게시글 ID 배열 생성
                $articleIds = array_map(function ($article) {
                    return $article->getId();
                }, $articles);

                // 게시글별 댓글 개수 조회
                $commentCounts = $this->MainModel->getCommentCountForArticles($articleIds);

                $page_view_data = [
                    'title' => '카페 통합검색 결과',
                    'articles' => $articles,
                    'commentCounts' => $commentCounts,
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                    'articlesPerPage' => $articlesPerPage,
                    'totalArticleCountAll' => $totalArticleCount,
                    'parentArticlesExist' => $parentArticlesExist,
                    'childArticles' => $childArticles,
                    'keyword' => $keyword,
                    'element' => $element,
                    'period' => $period,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'errors' => $errors ?? []
                ];

                $html = $this->load->view('dashboard/main_search_content', $page_view_data, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '데이터를 불러오는 데 실패했습니다.']);
            }
        } else {
            $this->loadErrorView();
        }
    }

    public function loadErrorView()
    {
        $page_view_data = [
            'title' => '잘못된 접근입니다.',
            'message' => '정상적인 경로로 접근해주세요.',
        ];
        $this->layout->view('errors/error_page', $page_view_data);
    }
}
