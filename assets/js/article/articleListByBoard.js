$(document).ready(function () {
    // var boardId = new URLSearchParams(window.location.search).get('boardId'); // URL에서 boardId 가져오기
    var boardId = $('#articleContent').data('article-board-id');
        // 초기 페이지 로드
    fetchArticles({
        page: 1,
        articlesPerPage: 15,
        boardId: boardId
    });

    $(document).on('change', '#select-period', function () {
        if ($('#select-period').val() === "custom") {
            $('.select-date').show(100);
        } else {
            $('.select-date').hide(100);
            $('#start-date').val('');
            $('#end-date').val('');
        }
    });
    
    // 페이지네이션 페이지 변경 처리
    $(document).on('click', '.article-board-list-page-btn', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        fetchArticles(collectSearchConditions(page));
    });

    // 게시글 표시 개수 변경 처리
    $(document).on('change', '#articlesPerPage', function () {
        fetchArticles(collectSearchConditions(1)); // 개수 변경 시 1페이지로 리셋
    });

    // 검색 폼 제출 처리
    $(document).on('submit', '#searchForm', function (e) {
        e.preventDefault();
        fetchArticles(collectSearchConditions(1)); // 검색 실행 시 1페이지로 리셋
    });

    // AJAX 요청 함수
    function fetchArticles(data) {
        $.ajax({
            url: '/article/ArticleListController/loadBoard',
            type: 'GET',
            data: data,
            dataType: 'json',
            success: function (response) {
                $('#articleContent').html(response.html);
                updateDateVisibility();
            },
            error: function (xhr, status, error) {
                console.error("Error: ", error);
            }
        });
    }

    // 검색 조건 수집 함수
    function collectSearchConditions(page) {
        return {
            page: page,
            boardId: boardId,
            articlesPerPage: $('#articlesPerPage').val(),
            keyword: $('#searchForm input[name="keyword"]').val(),
            element: $('#searchForm select[name="element"]').val(),
            period: $('#searchForm select[name="period"]').val(),
            startDate: $('#start-date').val(),
            endDate: $('#end-date').val()
        };
    }

    function updateDateVisibility() {
        if ($('#select-period').val() === 'custom' || ($('#start-date').val() && $('#end-date').val())) {
            $('.select-date').show();
        } else {
            $('.select-date').hide();
        }
    }

    // 페이지 로드 시 .select-date 요소의 초기 상태 업데이트
    updateDateVisibility();
});
