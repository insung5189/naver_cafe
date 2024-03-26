$(document).ready(function () {



    $('.toggle-daemoon').text('▲ 대문접기');

    $('.toggle-daemoon').click(function () {
        $('.daemoon-img').toggle();

        if ($('.daemoon-img').is(':visible')) {
            $(this).text('▲ 대문접기');
        } else {
            $(this).text('▼ 대문보기');
        }
    });

    $(document).on('click', '.freeboard-article-link', function () {
        // var articleIdsStr = $('#articleIds').attr('data-articles');
        var articleIds = JSON.parse(articleIdsStr);
        localStorage.setItem('articles', JSON.stringify(articleIds));
        console.log("초기 인덱스: ", localStorage.getItem('articles'));
        
        boardId = 'main';
        saveCurrentState(boardId);
    });

    $(document).on('click', '.qna-article-link', function () {
        // var articleIdsStr = $('#articleIds').attr('data-articles');
        var articleIds = JSON.parse(articleIdsStr);
        localStorage.setItem('articles', JSON.stringify(articleIds));
        console.log("초기 인덱스: ", localStorage.getItem('articles'));
        
        boardId = 'main';
        saveCurrentState(boardId);
    });

    $(document).on('click', '.all-article-link', function () {
        // var articleIdsStr = $('#articleIds').attr('data-articles');
        var articleIds = JSON.parse(articleIdsStr);
        localStorage.setItem('articles', JSON.stringify(articleIds));
        console.log("초기 인덱스: ", localStorage.getItem('articles'));

        boardId = 'all';
        saveCurrentState(boardId);
    });

    // 게시글 목록으로 돌아가기 기능을 위해 로컬스토리지에 히스토리 상태를 저장 후 활용
    function saveCurrentState(boardId) {
        var state = {
            page: '1',
            boardId: boardId,
            articlesPerPage: '15',
            keyword: '',
            element: '',
            period: 'all',
            startDate: '',
            endDate: ''
        };
        localStorage.setItem('articleListState', JSON.stringify(state));
    }


});