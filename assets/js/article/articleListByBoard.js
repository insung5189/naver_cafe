$(document).ready(function () {

    $('#articlesPerPage').change(function () {
        $('#articlesPerPageForm').submit();
    });

    $('#select-period').change(function () {
        var selectedPeriod = $(this).val();
        if (selectedPeriod === 'custom') {
            $('.select-date').show();
        } else {
            $('.select-date').hide();
        }
    }).trigger('click');

    /**
        function fetchArticleBoard(page, boardId) {
            var loadBoardMethod;
    
            switch (boardId) {
                case 1:
                    loadBoardMethod = 'loadFreeBoard';
                    break;
                case 2:
                    loadBoardMethod = 'loadSuggestedBoard';
                    break;
                case 3:
                    loadBoardMethod = 'loadWordVomitBoard';
                    break;
                case 4:
                    loadBoardMethod = 'loadKnowledgeSharingBoard';
                    break;
                case 5:
                    loadBoardMethod = 'loadQnaBoard';
                    break;
                default:
                    console.log('잘못된 요청입니다.');
                    return;
            }
            $.ajax({
                url: '/article/ArticleListController/' + loadBoardMethod,
                type: 'GET',
                data:
                {
                    page: page,
                    boardId: boardId
                },
                dataType: 'json',
                success: function (response) {
                    // 성공 시 페이지 콘텐츠 업데이트
                    $('#dynamicContent').html(response.html);
                },
                error: function (xhr, status, error) {
                    // 요청에 실패했을 때 실행되는 함수
                    console.error("Error: " + error);
                    console.log(error);
                    alert("게시판 컨텐츠를 불러오는데 실패했습니다.");
                }
            });
        }
     */
});