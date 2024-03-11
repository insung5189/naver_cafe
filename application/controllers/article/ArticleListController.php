<?
defined('BASEPATH') or exit('No direct script access allowed');
class ArticleListController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $articleBoard = '게시판에 대한 객체정보 들어갈 자리';

        $page_view_data = [
            'title' => $articleBoard, // $articleBoard->getBoardName(); DB를 참조해서 게시판의 이름이 들어갈 것
            'errors' => $errors ?? [] // 에러처리
        ];
        // 데이터와 함께 뷰 로드
        $this->layout->view('article/article_list_by_board', $page_view_data);
    }

    public function loadFreeBoard()
    {
        // 자유게시판의 목록 로드
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {

                $freeBoardListData = [
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                ];

                $html = $this->load->view('article/article_list', $freeBoardListData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '자유게시판 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }

    public function loadSuggestedBoard()
    {
        // 건의게시판의 목록 로드
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {

                $suggestedBoardListData = [
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                ];

                $html = $this->load->view('article/article_list', $suggestedBoardListData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '건의게시판 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }

    public function loadWordVomitBoard()
    {
        // 아무말게시판의 목록 로드
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {

                $wordVomitBoardListData = [
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                ];

                $html = $this->load->view('article/article_list', $wordVomitBoardListData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '아무말게시판 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }

    public function loadKnowledgeSharingBoard()
    {
        // 지식공유게시판의 목록 로드
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {

                $knowledgeSharingBoardListData = [
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                ];

                $html = $this->load->view('article/article_list', $knowledgeSharingBoardListData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '지식공유게시판 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }

    public function loadQnaBoard()
    {
        // 질문/답변게시판의 목록 로드
        // AJAX 요청 인지 확인
        if ($this->input->is_ajax_request()) {
            try {

                $qnaBoardListData = [
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                    'articlesByPage' => 'ㅎㅎ',
                ];

                $html = $this->load->view('article/article_list', $qnaBoardListData, TRUE);

                // 데이터를 JSON 형태로 반환
                echo json_encode(['success' => true, 'html' => $html]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => '질문/답변게시판 목록을 불러오는 데 실패했습니다.']);
            }
        } else {
            show_404();
        }
    }
}
