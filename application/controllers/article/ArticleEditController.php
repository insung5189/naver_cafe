<?
defined('BASEPATH') or exit('No direct script access allowed');
require_once 'library/HTMLPurifier.auto.php';
class ArticleEditController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article/ArticleEditModel', 'ArticleEditModel');
    }

    public function index()
    {
        if (!$this->session->userdata('user_data')) {
            show_error('로그인이 필요한 기능입니다.');
            return;
        }

        $parentId = $this->input->get('parentId');
        $boardId = $this->input->get('boardId');
        $prefix = $this->input->get('prefix');

        $rqMethod = empty($parentId) ? 'createArticle' : 'createReplyArticle';

        // 게시판 데이터 조회
        $boardRepo = $this->em->getRepository('Models\Entities\ArticleBoard');
        $boards = $boardRepo->findBy(['isDeleted' => 0], ['createDate' => 'DESC']);

        // ID가 6인 게시판 제외
        $boards = array_filter($boards, function ($board) {
            return $board->getId() !== "6";
        });

        $parentArticle = null;

        if ($parentId) {
            $parentArticle = $this->ArticleEditModel->getArticleById($parentId);
        }

        $page_view_data = [
            'title' => !empty($parentId) ? '답글 달기' : '카페 글쓰기',
            'boards' => $boards,
            'parentArticle' => $parentArticle,
        ];

        $this->layout->view('article/article_form', $page_view_data);
    }

    public function createArticle()
    {
        if (!$this->session->userdata('user_data')) {
            show_error('로그인이 필요한 기능입니다.');
            return;
        }

        $content = $this->input->post('content', TRUE);
        $allowedTags = '<iframe><figure><oembed><p><img>';
        $content = strip_tags($content, $allowedTags);

        $formData = [
            'boardId' => $this->input->post('board', TRUE),
            'prefix' => $this->input->post('prefix', TRUE),
            'title' => $this->input->post('title', TRUE),
            'content' => $content,
            'parentId' => $this->input->post('parentId', TRUE),
            'publicScope' => $this->input->post('publicScope', TRUE),
            'memberId' => $this->session->userdata('user_data')['user_id'],
        ];

        if (empty($formData['parentId'])) {
            $result = $this->ArticleEditModel->processCreateNewArticle($formData);
        } else {
            $result = $this->ArticleEditModel->processCreateReplyArticle($formData);
        }

        if ($result['success']) {
            redirect('/article/articledetailcontroller/index/' . $result['articleId']);
        } else {
            $this->session->set_flashdata('error_messages', $result['errors']);
            redirect('/article/articleeditcontroller');
        }
    }

    public function uploadFile()
    {
        $relativePath = 'assets/file/articleFiles/';

        $imgExtensions = ['gif', 'jpg', 'png', 'jpeg', 'webp'];
        $docExtensions = ['doc', 'pdf', 'docx', 'xlsx', 'ppt', 'pptx', 'xls'];

        $extension = strtolower(pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION));

        // Determine folder path based on file extension
        if (in_array($extension, $imgExtensions)) {
            $uploadSubfolder = 'img/';
        } elseif (in_array($extension, $docExtensions)) {
            $uploadSubfolder = 'doc/';
        } else {
            $uploadSubfolder = 'others/';
        }

        $uploadPath = FCPATH . $relativePath . $uploadSubfolder;

        // 디렉토리 존재 확인 및 디렉토리가 없다면 생성
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // 업로드 설정
        $config['upload_path'] = $uploadPath; // 확장자에 따른 파일업로드 경로
        $config['allowed_types'] = '*';
        $config['max_size'] = 20000; // 20MB로 제한

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('upload')) {
            $error = ['uploaded' => 0, 'error' => ['message' => strip_tags($this->upload->display_errors())]];
            echo json_encode($error);
        } else {
            $data = $this->upload->data();
            $fileUrl = base_url($relativePath . $uploadSubfolder . $data['file_name']);

            $this->ArticleEditModel->saveFileEntity($data, $fileUrl);

            echo json_encode(['uploaded' => 1, 'fileName' => $data['file_name'], 'url' => $fileUrl]);
        }
    }
}
