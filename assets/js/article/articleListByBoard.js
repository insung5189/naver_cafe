$(document).ready(function () {

    if (location.hash === "#freeBoard") {
        // 데이터 로드
        fetchArticleBoard(1);
    }
    if (location.hash === "#suggestedBoard") {
        // 데이터 로드
        fetchArticleBoard(1);
    }
    if (location.hash === "#wordVomitBoard") {
        // 데이터 로드
        fetchArticleBoard(1);
    }
    if (location.hash === "#knowledgeSharingBoard") {
        // 데이터 로드
        fetchArticleBoard(1);
    }
    if (location.hash === "#qnaBoard") {
        // 데이터 로드
        fetchArticleBoard(1);
    }

    // 게시판 버튼에 클릭 이벤트 바인딩
    $(".board-url").click(function () {
        // 클릭된 게시판의 data-board-id 속성 값 가져오기
        var boardId = $(this).data("board-id");

        // AJAX 요청: 게시판 ID를 서버에 전달
        $.ajax({
            url: "/path/to/board/content/handler", // 실제 게시판 컨텐츠를 처리하는 서버의 URL로 변경해야 합니다.
            type: "GET", // 또는 "POST", 서버 구현에 따라 선택
            data: {
                boardId: boardId // 서버로 전달할 데이터
            },
            dataType: "json", // 서버로부터 기대하는 응답의 타입 (예: "json", "html")
            success: function (response) {
                // 서버로부터 응답을 성공적으로 받았을 때 실행되는 함수
                // 예: 응답을 페이지에 표시
                $("#content").html(response); // 응답을 특정 요소에 표시, "content"는 적절한 대상 요소의 ID로 변경해야 합니다.
            },
            error: function (xhr, status, error) {
                // 요청에 실패했을 때 실행되는 함수
                console.error("Error: " + error);
                alert("게시판 컨텐츠를 불러오는데 실패했습니다.");
            }
        });
    });

    function fetchArticleBoard(page) {
        var boardId = $('.board-url').data("board-id");
        var loadBoardMethod;

        switch (boardId) {
            case '1':
                loadBoardMethod = 'loadFreeBoard';
                break;
            case '2':
                loadBoardMethod = 'loadSuggestedBoard';
                break;
            case '3':
                loadBoardMethod = 'loadWordVomitBoard';
                break;
            case '4':
                loadBoardMethod = 'loadKnowledgeSharingBoard';
                break;
            case '5':
                loadBoardMethod = 'loadQnaBoard';
                break;
            default:
                console.log('잘못된 요청입니다.');
                return;
        }
        $.ajax({
            url: '/article/ArticleListController/' + tabMethod,
            type: 'GET',
            data: { page: page },
            dataType: 'json',
            success: function (response) {
                // 성공 시 페이지 콘텐츠 업데이트
                $('#dynamicContent').html(response.html);

                if (window.location.hash) {
                    history.pushState("", document.title, window.location.pathname + window.location.search);
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

});