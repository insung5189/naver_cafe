<?
defined('BASEPATH') or exit('No direct script access allowed');
class ArticleListAllController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article/ArticleListAllModel', 'ArticleListAllModel');
    }

    // 기존 로직
    public function index()
    {
        $childArticles = [];
        $currentPage = $this->input->get('page', TRUE) ?? 1;
        $articlesPerPage = $this->input->get('articlesPerPage', TRUE) ? (int)$this->input->get('articlesPerPage') : 15;
        $keyword = $this->input->get('keyword', TRUE) ?? '';
        $element = $this->input->get('element', TRUE) ?? '';
        $period = $this->input->get('period', TRUE) ?? '';
        $startDate = $this->input->get('startDate', TRUE) ?? '';
        $endDate = $this->input->get('endDate', TRUE) ?? '';

        if (!empty($keyword) && !empty($period) || !empty($startDate) || !empty($endDate)) {

            $result = $this->ArticleListAllModel->searchArticles($keyword, $element, $period, $startDate, $endDate, $currentPage, $articlesPerPage);

            if (isset($result['errors'])) {
                // 검색결과에 오류 발생 시
                $errors = $result['errors'];
                $articles = $this->ArticleListAllModel->getArticlesByPage($currentPage, $articlesPerPage); // 기존 게시글은 depth가 0인것, isActive가 1인것만 불러옴.
                $childArticles = $this->ArticleListAllModel->getChildArticles(); // 자식글은 부모글의 id값을 key값으로 하여 배열에 저장하고 페이지에서 부모글 밑에 조건부로 foreach문으로 반복나열함.
                $totalArticleCount = $this->ArticleListAllModel->getTotalArticleCount();
                $totalArticleCountForPaginaion = $this->ArticleListAllModel->getTotalArticleCountForPagination();
                $articleIndex = NULL;
            } else {
                $errors = NULL;
                // 검색결과에 오류 없을 때
                $articles = $result; // 검색키워드가 있고, 오류가 없을 땐 검색결과 전부를 페이징 해서 불러옴.
                // $totalArticleCount = $this->ArticleListAllModel->getTotalArticleCountWithSearch($keyword, $element, $period, $startDate, $endDate);
                $childArticles = $this->ArticleListAllModel->getChildArticles(); // 자식글은 부모글의 id값을 key값으로 하여 배열에 저장하고 페이지에서 부모글 밑에 조건부로 foreach문으로 반복나열함.
                // $childArticles = NULL; // 자식글은 부모글의 id값을 key값으로 하여 배열에 저장하고 페이지에서 부모글 밑에 조건부로 foreach문으로 반복나열함.
                // $totalArticleCountForPaginaion = $this->ArticleListAllModel->getTotalArticleCountForPagination();
                $totalArticleCountForPaginaion = $this->ArticleListAllModel->getTotalArticleCountWithSearchForPagination($keyword, $element, $period, $startDate, $endDate);
                // $totalArticleCountForPaginaion = count($articles);
                $totalArticleCount = $totalArticleCountForPaginaion;
                $articleIndex = $this->ArticleListAllModel->searchArticles($keyword, $element, $period, $startDate, $endDate, NULL, NULL);
            }
        } else {
            // 검색하지 않았을 때
            $errors = NULL;
            $articles = $this->ArticleListAllModel->getArticlesByPage($currentPage, $articlesPerPage); // 기존 게시글은 depth가 0인것, isActive가 1인것만 불러옴.
            $childArticles = $this->ArticleListAllModel->getChildArticles(); // 자식글은 부모글의 id값을 key값으로 하여 배열에 저장하고 페이지에서 부모글 밑에 조건부로 foreach문으로 반복나열함.
            $totalArticleCount = $this->ArticleListAllModel->getTotalArticleCount();
            $totalArticleCountForPaginaion = $this->ArticleListAllModel->getTotalArticleCountForPagination();
            $articleIndex = $this->ArticleListAllModel->getArticlesByPage(NULL, NULL);
        }

        $totalPages = ceil($totalArticleCountForPaginaion / $articlesPerPage);

        $parentArticlesExistAllArticles = $this->ArticleListAllModel->checkChildArticlesParentExistForSearch($articles);
        $parentArticlesExist = $this->ArticleListAllModel->checkChildArticlesParentExist($childArticles);

        // 게시글 ID 배열 생성
        $articleIds = array_map(function ($article) {
            return $article->getId();
        }, $articles);

        if (isset($articleIndex)) {
            $articleIndexIds = array_map(function ($article) {
                return $article->getId();
            }, $articleIndex);
        }

        // 게시글별 댓글 개수 조회
        $commentCounts = $this->ArticleListAllModel->getCommentCountForArticles($articleIds);

        $page_view_data = [
            'title' => !empty($keyword) ? '전체글보기 검색 결과' : '전체글보기',
            'articles' => $articles,
            'articleIndexIds' => $articleIndexIds,
            'commentCounts' => $commentCounts,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'articlesPerPage' => $articlesPerPage,
            'totalArticleCountAll' => $totalArticleCount,
            'parentArticlesExist' => $parentArticlesExist,
            'parentArticlesExistAllArticles' => $parentArticlesExistAllArticles,
            'childArticles' => $childArticles,
            'keyword' => $keyword,
            'element' => $element,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'errors' => $errors ?? []
        ];

        // 데이터와 함께 뷰 로드
        $this->layout->view('article/article_list_all', $page_view_data);
    }

    public function fetchArticles()
    {
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {
                $childArticles = [];
                $currentPage = $this->input->get('page', TRUE) ?? 1;
                $articlesPerPage = $this->input->get('articlesPerPage', TRUE) ? (int)$this->input->get('articlesPerPage') : 15;
                $keyword = $this->input->get('keyword') ?? '';
                $element = $this->input->get('element', TRUE) ?? '';
                $period = $this->input->get('period', TRUE) ?? '';
                $startDate = $this->input->get('startDate', TRUE) ?? '';
                $endDate = $this->input->get('endDate', TRUE) ?? '';

                if (!empty($keyword) && !empty($period) || !empty($startDate) || !empty($endDate)) {

                    $result = $this->ArticleListAllModel->searchArticles($keyword, $element, $period, $startDate, $endDate, $currentPage, $articlesPerPage);

                    if (isset($result['errors'])) {
                        // 검색결과에 오류 발생 시
                        $errors = $result['errors'];
                        $articles = $this->ArticleListAllModel->getArticlesByPage($currentPage, $articlesPerPage); // 기존 게시글은 depth가 0인것, isActive가 1인것만 불러옴.
                        $childArticles = $this->ArticleListAllModel->getChildArticles(); // 자식글은 부모글의 id값을 key값으로 하여 배열에 저장하고 페이지에서 부모글 밑에 조건부로 foreach문으로 반복나열함.
                        $totalArticleCount = $this->ArticleListAllModel->getTotalArticleCount();
                        $totalArticleCountForPaginaion = $this->ArticleListAllModel->getTotalArticleCountForPagination();
                        $articleIndex = $this->ArticleListAllModel->getArticlesByPage(NULL, NULL);
                    } else {
                        $errors = NULL;
                        // 검색결과에 오류 없을 때
                        $articles = $result; // 검색키워드가 있고, 오류가 없을 땐 검색결과 전부를 페이징 해서 불러옴.
                        // $totalArticleCount = $this->ArticleListAllModel->getTotalArticleCountWithSearch($keyword, $element, $period, $startDate, $endDate);
                        $childArticles = $this->ArticleListAllModel->getChildArticles(); // 자식글은 부모글의 id값을 key값으로 하여 배열에 저장하고 페이지에서 부모글 밑에 조건부로 foreach문으로 반복나열함.
                        // $childArticles = NULL; // 자식글은 부모글의 id값을 key값으로 하여 배열에 저장하고 페이지에서 부모글 밑에 조건부로 foreach문으로 반복나열함.
                        // $totalArticleCountForPaginaion = $this->ArticleListAllModel->getTotalArticleCountForPagination();
                        $totalArticleCountForPaginaion = $this->ArticleListAllModel->getTotalArticleCountWithSearchForPagination($keyword, $element, $period, $startDate, $endDate);
                        // $totalArticleCountForPaginaion = count($articles);
                        $totalArticleCount = $totalArticleCountForPaginaion;
                        $articleIndex = $this->ArticleListAllModel->searchArticles($keyword, $element, $period, $startDate, $endDate, NULL, NULL);
                    }
                } else {
                    $errors = NULL;
                    // 검색하지 않았을 때
                    $articles = $this->ArticleListAllModel->getArticlesByPage($currentPage, $articlesPerPage); // 기존 게시글은 depth가 0인것, isActive가 1인것만 불러옴.
                    $childArticles = $this->ArticleListAllModel->getChildArticles(); // 자식글은 부모글의 id값을 key값으로 하여 배열에 저장하고 페이지에서 부모글 밑에 조건부로 foreach문으로 반복나열함.
                    $totalArticleCount = $this->ArticleListAllModel->getTotalArticleCount();
                    $totalArticleCountForPaginaion = $this->ArticleListAllModel->getTotalArticleCountForPagination();
                    $articleIndex = $this->ArticleListAllModel->getArticlesByPage(NULL, NULL);
                }

                $totalPages = ceil($totalArticleCountForPaginaion / $articlesPerPage);

                $parentArticlesExistAllArticles = $this->ArticleListAllModel->checkChildArticlesParentExistForSearch($articles);
                $parentArticlesExist = $this->ArticleListAllModel->checkChildArticlesParentExist($childArticles);
                
                // 게시글 ID 배열 생성
                $articleIds = array_map(function ($article) {
                    return $article->getId();
                }, $articles);

                if (isset($articleIndex)) {
                    $articleIndexIds = array_map(function ($article) {
                        return $article->getId();
                    }, $articleIndex);
                }

                // 게시글별 댓글 개수 조회
                $commentCounts = $this->ArticleListAllModel->getCommentCountForArticles($articleIds);

                $page_view_data = [
                    'title' => !empty($keyword) ? '전체글보기 검색 결과' : '전체글보기',
                    'articles' => $articles,
                    'articleIndexIds' => $articleIndexIds ?? NULL,
                    'commentCounts' => $commentCounts,
                    'currentPage' => $currentPage,
                    'totalPages' => $totalPages,
                    'articlesPerPage' => $articlesPerPage,
                    'totalArticleCountAll' => $totalArticleCount,
                    'parentArticlesExist' => $parentArticlesExist,
                    'parentArticlesExistAllArticles' => $parentArticlesExistAllArticles,
                    'childArticles' => $childArticles,
                    'keyword' => $keyword,
                    'element' => $element,
                    'period' => $period,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'errors' => $errors ?? []
                ];

                $html = $this->load->view('article/article_list_all_content', $page_view_data, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html, 'errors' => $errors]);
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
