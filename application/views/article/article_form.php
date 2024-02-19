<?
$GLOBALS['pageResources'] = [
    'css' => [
        '/assets/css/article/articleEdit.css',
    ],
    'js' => [
        '/assets/js/article/articleEdit.js',
    ]
];
?>
<section class="section-container">
    <div class="container">
        <form action="/article/articleeditcontroller/processWrite" method="POST">
            <div class="writingHeader">
                <h1 class="title">카페 글쓰기</h1>
                <input class="form-btn-box btn-box submit-btn" type="submit" value="등록">
            </div>
            <div class="editer-box">
                <div class="select-box">
                    <div class="board-select-box">
                        <select id="board-select" name="board" class="custom-input">
                            <option value="">게시판을 선택해 주세요.</option>
                            <option value="자유게시판">자유게시판</option>
                            <option value="건의게시판">건의게시판</option>
                            <option value="아무말게시판">아무말게시판</option>
                            <option value="지식공유">지식공유</option>
                            <option value="질문/답변게시판">질문/답변게시판</option>
                        </select>
                    </div>
                    <div class="topic-select-box">
                        <select id="topic-select" name="topic" class="custom-input" disabled>
                            <option value="">말머리 선택</option>
                        </select>
                    </div>
                </div>
                <div class="title-box">
                    <input class="custom-input" id="title" placeholder="제목을 입력해 주세요" required name="title" type="text">
                </div>
                <!-- 부모 게시글 ID를 위한 숨겨진 필드 (답글일 경우) -->
                <input type="hidden" name="parent_id" id="parent_id">
            </div>
        </form>
    </div>
</section>