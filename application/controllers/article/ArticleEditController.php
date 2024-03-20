<?
defined('BASEPATH') or exit('No direct script access allowed');

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
            $this->setRedirectCookie(current_url());
            redirect('/member/logincontroller');
            return;
        }

        $parentId = $this->input->get('parentId');
        $boardRepo = $this->em->getRepository('Models\Entities\ArticleBoard');
        $boards = $boardRepo->findBy(['isDeleted' => 0], ['createDate' => 'DESC']);

        // ID가 6인 게시판 제외
        $boards = array_filter($boards, function ($board) {
            return $board->getId() !== "6";
        });

        $parentArticle = null;
        $existingArticleContent = null;

        if ($parentId) {
            $parentArticle = $this->ArticleEditModel->getArticleById($parentId);
        }

        $page_view_data = [
            'title' => !empty($parentId) ? '답글 달기' : '카페 글쓰기',
            'boards' => $boards,
            'parentArticle' => $parentArticle,
            'isEdit' => false,
            'existingArticleContent' => $existingArticleContent,
        ];

        $this->layout->view('article/article_form', $page_view_data);
    }

    public function editForm($articleId)
    {
        if (!$this->session->userdata('user_data')) {
            $this->setRedirectCookie(current_url());
            redirect('/member/logincontroller');
            return;
        }

        $article = $this->ArticleEditModel->getArticleById($articleId);

        $parentArticle = $this->ArticleEditModel->getParentArticleById($articleId);

        if (!$article) {
            show_404();
            return;
        }

        $currentUserId = $this->session->userdata('user_data')['user_id'];

        if ($article->getMember()->getId() != $currentUserId) {
            $this->session->set_flashdata('error_message', '접근 권한이 없습니다.');
            redirect('/');
            return;
        }

        $boards = $this->em->getRepository('Models\Entities\ArticleBoard')->findBy(['isDeleted' => 0], ['createDate' => 'DESC']);

        $boards = array_filter($boards, function ($board) {
            return $board->getId() !== "6";
        });

        // 게시글의 현재 게시판 ID
        $currentBoardId = $article->getArticleBoard()->getId();
        $currentPrefix = $article->getPrefix();
        $existingArticleContent = $article->getContent();

        $prefixesMap = [
            '4' => ['PHP', 'MySQL', 'Apache', 'JavaScript', 'HTML', 'CSS', '기타'],
            '5' => ['질문', '답변']
        ];

        // 현재 게시글의 게시판에 해당하는 말머리 목록
        $prefixes = isset($prefixesMap[$currentBoardId]) ? $prefixesMap[$currentBoardId] : [];

        $data = [
            'article' => $article,
            'boards' => $boards,
            'isEdit' => true,
            'parentArticle' => isset($parentArticle) ? $parentArticle : null,
            'existingArticleContent' => $existingArticleContent,
            'currentBoardId' => $currentBoardId,
            'currentPrefix' => $currentPrefix,
            'prefixes' => $prefixes,
        ];

        $this->layout->view('article/article_form', $data);
    }

    public function createArticle()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('/');
            return;
        }

        if (!$this->session->userdata('user_data')) {
            $this->setRedirectCookie('/article/articleeditcontroller');
            $loginUrl = site_url('/member/logincontroller');
            echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.', 'loginRequired' => true, 'loginUrl' => $loginUrl]);
            return;
        }

        try {
            $isEdit = $this->input->post('isEdit', TRUE) == true;
            $articleId = $this->input->post('articleId', TRUE);

            $validPrefixes = [
                '4' => ['PHP', 'MYSQL', 'APACHE', 'JS', 'HTML', 'CSS', '기타', ''],
                '5' => ['질문', '답변', '']
            ];

            $prefix = $this->input->post('prefix', TRUE);
            $boardId = $this->input->post('board', TRUE) ? $this->input->post('board', TRUE) : $this->input->post('parentBoardId', TRUE);
            $title = $this->input->post('title', TRUE);
            $content = $this->purifyHtmlContent($this->input->post('content'));
            $publicScope = $this->input->post('publicScope', TRUE);

            $errors = [];
            if (empty($boardId)) {
                $errors[] = '게시판을 선택해주세요.';
                echo json_encode(['success' => false, 'message' => $errors]);
                return;
            }

            if ((int)$boardId < 1 || (int)$boardId > 5 || !ctype_digit($boardId)) {
                $errors[] = '유효하지 않은 게시판입니다. 올바른 게시판을 선택해 주세요.';
                echo json_encode(['success' => false, 'message' => $errors, 'invalidBoardSelection' => true]);
                return;
            }

            if (array_key_exists($boardId, $validPrefixes) && !in_array($prefix, $validPrefixes[$boardId])) {
                echo json_encode([
                    'success' => false,
                    'message' => '유효하지 않은 말머리입니다. 올바른 말머리를 선택해 주세요.',
                    'invalidPrefixSelection' => true,
                    'boardId' => $boardId
                ]);
                return;
            }

            if (empty($title)) {
                $errors[] = '제목을 입력해 주세요.';
            }

            if (empty($content)) {
                $errors[] = '내용을 입력해 주세요.';
            }

            $validPublicScopes = ['public', 'members', 'admins'];
            if (empty($publicScope) || !in_array($publicScope, $validPublicScopes)) {
                $errors[] = '공개 범위가 잘못되었습니다. 올바른 값을 선택해 주세요.';
            }

            // 컨트롤러의 실패처리결과 표시
            if (!empty($errors)) {
                echo json_encode(['success' => false, 'message' => $errors]);
                return;
            }

            // 에디터 내부에 삽입한 이미지파일 임시폴더에서 정규폴더로 이동
            $editorImgfileValid = $this->updateImagePathsAndMoveFiles($content);
            // 에디터와 별도로 첨부한 파일 임시폴더에서 정규폴더로 이동
            $attachedFileValid = $this->moveFilesAndUpdatePaths($editorImgfileValid);
            // 파일정보 담은 테이블 내용 스타일 추가 및 살균처리
            $finalContent = $this->validateAndUpdateFileContent($attachedFileValid);
            // 정규폴더로 모두 이동시키고 살균처리까지 완료된 파일정보를 추출해서 변수에 담고 모델로 전달
            $fileUrls = $this->extractFileUrlsFromContent($finalContent);

            $formData = [
                'boardId' => $boardId,
                'prefix' => $prefix,
                'title' => $title,
                'content' => $finalContent, // 게시글 상세보기에 표시하기 위한 모든 정보가 담겨있음.
                'fileUrls' => $fileUrls, // 게시글과 연관된 파일정보만 추출해서담겨있음.(모델영역에서 게시글 등록 후 파일 처리예정)
                'parentId' => $this->input->post('parentId', TRUE),
                'publicScope' => $publicScope,
                'memberId' => $this->session->userdata('user_data')['user_id'],
            ];

            if ($isEdit) {
                $result = $this->ArticleEditModel->processModifyArticle($formData, $articleId);
            } else {
                if (empty($formData['parentId'])) {
                    $result = $this->ArticleEditModel->processCreateNewArticle($formData);
                } else {
                    $result = $this->ArticleEditModel->processCreateReplyArticle($formData);
                }
            }

            if ($result['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => '게시글이 성공적으로 등록되었습니다.',
                    'redirectUrl' => '/article/articledetailcontroller/index/' . $result['articleId'] // 클라이언트에 전달할 리다이렉트 URL
                ]);
            } else {
                // 모델의 실패처리결과 표시
                echo json_encode(['success' => false, 'message' => $result['message'], 'errors' => $result['errors']]);
            }
        } catch (\Exception $e) {
            // 컨트롤러에서 예외가 발생한 경우
            echo json_encode(['success' => false, 'message' => $e->getMessage(), 'errors' => ['message' => $e->getMessage()]]);
            return;
        }
    }

    protected function purifyHtmlContent($htmlContent)
    {
        require_once 'vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';
        $config = HTMLPurifier_Config::createDefault();

        $def = $config->getHTMLDefinition(true);
        $def->addElement('iframe', 'Block', 'Flow', 'Common', [
            'src' => 'URI',
            'width' => 'Length',
            'height' => 'Length',
            'frameborder' => 'Text',
            'allow' => 'Text',
            'style' => 'Text',
            'allowfullscreen' => 'Bool',
        ]);

        $def->addElement('figure', 'Block', 'Flow', 'Common', []);
        $def->addElement('div', 'Block', 'Flow', 'Common', [
            'style' => 'Text',
            'data-oembed-url' => 'URI',
        ]);

        $purifier = new HTMLPurifier($config);
        return $purifier->purify($htmlContent);
    }

    // form이 제출되면 에디터에 이미지파일을 임시폴더에서 정규폴더로 이동하는 로직
    protected function updateImagePathsAndMoveFiles($content)
    {
        $updatedContent = preg_replace_callback(
            '/<img src="\/(assets\/file\/temporary\/img\/[^"]+)"/i',
            function ($matches) {
                $tempUrl = urldecode($matches[1]);
                $newSrc = str_replace('temporary/', 'articleFiles/', $tempUrl);

                // 파일 시스템에서의 이동
                $oldFilePath = FCPATH . $tempUrl;
                $newFilePath = FCPATH . $newSrc;
                if (file_exists($oldFilePath)) {
                    if (!is_dir(dirname($newFilePath))) {
                        mkdir(dirname($newFilePath), 0755, true);
                    }
                    rename($oldFilePath, $newFilePath);
                }

                return '<img src="/' . $newSrc . '"';
            },
            $content
        );

        return $updatedContent;
    }

    // form이 제출되면 첨부된 파일을 임시폴더에서 정규폴더로 이동하는 로직
    protected function moveFilesAndUpdatePaths($htmlContent)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            if ($link->hasAttribute('href')) {
                $encodedUrl = $link->getAttribute('href');
                $href = urldecode($encodedUrl);
                if (strpos($href, '/assets/file/temporary/') !== false) {
                    $extension = strtolower(pathinfo($href, PATHINFO_EXTENSION));
                    $imgExtensions = ['gif', 'jpg', 'png', 'jpeg', 'webp', 'bmp'];
                    $docExtensions = ['doc', 'pdf', 'docx', 'xlsx', 'ppt', 'pptx', 'xls', 'txt'];

                    if (in_array($extension, $imgExtensions)) {
                        $newBasePath = 'assets/file/articleFiles/img/';
                    } elseif (in_array($extension, $docExtensions)) {
                        $newBasePath = 'assets/file/articleFiles/doc/';
                    } else {
                        $newBasePath = 'assets/file/articleFiles/others/';
                    }

                    $oldPath = FCPATH . trim(parse_url($href, PHP_URL_PATH), '/');
                    $newPath = FCPATH . $newBasePath . basename($oldPath);

                    if (file_exists($oldPath)) {
                        if (!is_dir(dirname($newPath))) {
                            mkdir(dirname($newPath), 0755, true);
                        }
                        rename($oldPath, $newPath);
                        $newUrl = base_url($newBasePath . basename($newPath));
                        $link->setAttribute('href', $newUrl);
                    }
                }
            }
        }

        return $dom->saveHTML();
    }

    protected function validateAndUpdateFileContent($htmlContent)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $figures = $dom->getElementsByTagName('figure');
        $figuresToMove = [];

        foreach ($figures as $figure) {
            if ($figure->parentNode->nodeName === 'figure') {
                $figuresToMove[] = $figure;
            }
        }

        foreach ($figuresToMove as $figure) {
            $parentFigure = $figure->parentNode;
            $newSibling = $parentFigure->nextSibling;
            $parentOfParent = $parentFigure->parentNode;

            if ($newSibling) {
                $parentOfParent->insertBefore($figure, $newSibling);
            } else {
                $parentOfParent->appendChild($figure);
            }
        }

        $tables = $dom->getElementsByTagName('table');

        foreach ($tables as $table) {
            $table->setAttribute('style', 'border: 1px double #b3b3b3; border-collapse: collapse; border-spacing: 0; height: 47px; overflow: hidden;');
        }

        $ths = $dom->getElementsByTagName('th');
        foreach ($ths as $th) {
            $th->setAttribute('style', 'background: rgba(0, 0, 0, .05); font-weight: 700; text-align: left; border: 1px solid #bfbfbf; width:fit-content; padding: 0.4em; overflow-wrap: break-word;');
        }

        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            if ($link->hasAttribute('href') && strpos($link->getAttribute('href'), '/assets/file/') !== false) {
                $fileUrl = $link->getAttribute('href');
                $actualFileInfo = $this->getActualFileInfo($fileUrl);

                if ($actualFileInfo) {
                    // 파일 이름을 담고 있는 첫 번째 th 태그
                    $fileNameTh = $link->parentNode->parentNode->firstChild;
                    if ($fileNameTh) {
                        $fileNameTh->nodeValue = '';
                        $fileNameSpan = $dom->createElement('span', $actualFileInfo['name']);
                        $fileNameSpan->setAttribute('style', 'font-size:13px;');
                        $fileNameTh->appendChild($fileNameSpan);
                    }

                    // 파일 크기를 담고 있는 두 번째 th 태그
                    $fileSizeTh = $fileNameTh->nextSibling;
                    if ($fileSizeTh) {
                        $fileSizeTh->nodeValue = '';
                        $fileSizeSpan = $dom->createElement('span', $actualFileInfo['size'] . 'KB');
                        $fileSizeSpan->setAttribute('style', 'font-size:13px;');
                        $fileSizeTh->appendChild($fileSizeSpan);
                    }

                    // 다운로드링크(이모티콘)를 담고있는 세 번째 th태그
                    $downloadLinkTh = $fileSizeTh->nextSibling;
                    while ($downloadLinkTh && $downloadLinkTh->nodeName !== 'th') {
                        $downloadLinkTh = $downloadLinkTh->nextSibling;
                    }
                    if ($downloadLinkTh) {
                        // 기존 내용을 지우고 새로운 a태그와 span태그 생성
                        $downloadLinkTh->nodeValue = '';
                        $downloadLinkA = $dom->createElement('a');
                        $downloadLinkA->setAttribute('href', $actualFileInfo['url']);
                        $downloadLinkA->setAttribute('download', $actualFileInfo['name']);
                        $downloadLinkA->setAttribute('target', '_blank');

                        $downloadIconSpan = $dom->createElement('span', '💾'); // 이모티콘과 함께 span 태그 생성
                        $downloadIconSpan->setAttribute('style', 'font-size:28px;'); // 스타일 적용

                        $downloadLinkA->appendChild($downloadIconSpan); // a 태그에 span 태그 추가
                        $downloadLinkTh->appendChild($downloadLinkA); // th 태그에 a 태그 추가
                    }
                }
            }
            $tables = iterator_to_array($dom->getElementsByTagName('table'));
            foreach ($tables as $table) {
                $parent = $table->parentNode;
                if ($parent->nodeName !== 'figure' || $parent->getAttribute('class') !== 'table') {
                    $newFigure = $dom->createElement('figure');
                    $newFigure->setAttribute('class', 'table');
                    if ($parent instanceof DOMElement) {
                        $parent->replaceChild($newFigure, $table);
                    } else {
                        $dom->appendChild($newFigure);
                    }
                    $newFigure->appendChild($table);
                }
            }
        }

        return $dom->saveHTML();
    }

    // 파일정보를 담은 테이블 내용 살균처리에서 파일정보(이름, 사이즈, url)를 추출하기 위한 로직
    protected function getActualFileInfo($fileUrl)
    {
        $decodedUrl = urldecode($fileUrl);
        // URL에서 호스트 부분을 제거하여 상대 경로를 추출
        $parsedUrl = parse_url($decodedUrl);
        $relativePath = ltrim($parsedUrl['path'], '/');

        // FCPATH와 상대 경로를 조합하여 전체 파일 시스템 경로를 생성
        $filePath = FCPATH . $relativePath;

        if (file_exists($filePath)) {
            // 파일의 실제 이름과 크기, 접근 가능한 URL 반환
            return [
                'name' => basename($filePath),
                'size' => round(filesize($filePath) / 1024, 2),
                'url' => base_url($relativePath)
            ];
        }
        return false;
    }

    public function uploadImgFile()
    {
        $tempPath = 'assets/file/temporary/';

        $imgExtensions = ['gif', 'jpg', 'png', 'jpeg', 'webp', 'bmp'];
        $allowedMimeTypes = ['image/gif', 'image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/bmp'];

        $file = $_FILES['upload'];
        $extension = strtolower(pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION));
        $filename = pathinfo($file['name'], PATHINFO_FILENAME);
        $mimeType = mime_content_type($file['tmp_name']);

        if (!in_array($extension, $imgExtensions) || !in_array($mimeType, $allowedMimeTypes) || $file['size'] > 20000 * 1024) {
            echo json_encode(['uploaded' => 0, 'error' => ['message' => '유효하지 않은 파일입니다.']]);
            return;
        }

        $date = date('Ymd');
        $uuid = uniqid();
        $newName = "{$filename}-{$date}-{$uuid}.{$extension}";
        $uploadSubfolder = 'img/';
        $uploadPath = FCPATH . $tempPath . $uploadSubfolder;

        // 디렉토리 존재 확인 및 디렉토리가 없다면 생성
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // 업로드 설정
        $config['upload_path'] = $uploadPath;
        $config['allowed_types'] = implode('|', $imgExtensions);
        $config['max_size'] = 20000; // 20MB로 제한
        $config['file_name'] = $newName;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('upload')) {
            $error = ['uploaded' => 0, 'error' => ['message' => strip_tags($this->upload->display_errors())]];
            echo json_encode($error);
        } else {
            $data = $this->upload->data();
            $fileUrl = base_url($tempPath . $uploadSubfolder . $data['file_name']);
            echo json_encode(['uploaded' => 1, 'fileSize' => $data['file_size'], 'fileName' => $data['file_name'], 'url' => $fileUrl]);
        }
    }

    public function uploadFile()
    {
        $tempPath = 'assets/file/temporary/';

        $allowedMimeTypes = [
            'image/gif', 'image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/bmp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'application/zip',
            'application/x-hwp'
        ];

        $file = $_FILES['file'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = pathinfo($file['name'], PATHINFO_FILENAME);
        $mimeType = mime_content_type($file['tmp_name']);
        $imgExtensions = ['gif', 'jpg', 'png', 'jpeg', 'webp', 'bmp'];
        $docExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'hwp'];
        $allowedExtensions = array_merge($imgExtensions, $docExtensions);

        if (!in_array($mimeType, $allowedMimeTypes)) {
            echo json_encode(['uploaded' => 0, 'error' => ['message' => '유효하지 않은 파일입니다.']]);
            return;
        }

        if ($file['size'] > 20000 * 1024) {
            echo json_encode(['uploaded' => 0, 'error' => ['message' => '파일 크기가 너무 큽니다. 최대 허용 크기: 20MB']]);
            return;
        }

        $date = date('Ymd');
        $uuid = uniqid();
        $newName = "{$filename}-{$date}-{$uuid}.{$extension}";

        if (in_array($extension, $imgExtensions)) {
            $uploadSubfolder = 'img/';
        } elseif (in_array($extension, $docExtensions)) {
            $uploadSubfolder = 'doc/';
        } else {
            $uploadSubfolder = 'others/';
        }

        $uploadPath = FCPATH . $tempPath . $uploadSubfolder;

        // 디렉토리가 없으면 생성
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $config['upload_path'] = $uploadPath;
        $config['allowed_types'] = implode('|', $allowedExtensions);
        $config['max_size'] = 20000; // 20MB로 제한
        $config['file_name'] = $newName;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file')) {
            $error = ['uploaded' => 0, 'error' => ['message' => strip_tags($this->upload->display_errors())]];
            echo json_encode($error);
        } else {
            $data = $this->upload->data();
            $fileUrl = base_url($tempPath . $uploadSubfolder . $data['file_name']);
            echo json_encode(['uploaded' => 1, 'fileSize' => $data['file_size'], 'fileName' => $data['file_name'], 'url' => $fileUrl]);
        }
    }

    // content내부에 첨부파일로 분류되는 img태그와 a태그를 분석해서 게시글과 연관된 첨부파일 구분하는 메서드
    protected function extractFileUrlsFromContent($content)
    {
        $urls = [];
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

        // img 태그 처리
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            $decodedSrc = urldecode($src);
            if (!empty($decodedSrc) && strpos($decodedSrc, '/assets/file/') === 0) {
                $urls[] = $decodedSrc;
            }
        }

        // a 태그 처리
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            $encodedUrl = $link->getAttribute('href');
            $decodedUrl = urldecode($encodedUrl);
            if (!empty($decodedUrl) && strpos($decodedUrl, '/assets/file/') === 0) {
                $urls[] = $decodedUrl;
            }
        }

        return $urls;
    }
}
