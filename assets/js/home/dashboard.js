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

    // 게시글 목록으로 돌아가기 기능을 위해 로컬스토리지에 히스토리 상태를 저장 후 활용
    function saveCurrentState() {
        var state = {
            page: '',
            boardId: 'main',
            articlesPerPage: '',
            keyword: '',
            element: '',
            period: '',
            startDate: '',
            endDate: ''
        };
        localStorage.setItem('articleListState', JSON.stringify(state));
    }

    $(document).on('click', '.article-link', function () {
        saveCurrentState();
    });
});