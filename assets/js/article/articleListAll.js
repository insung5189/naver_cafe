$(document).ready(function () {

    $(window).on('popstate', function (e) {
        if (e.originalEvent.state) {
            fetchArticles(e.originalEvent.state);
        } else {
            // 초기 상태나 상태가 없는 경우의 처리
            fetchArticles(collectSearchConditions(1));
        }
    });
    console.log("현재 히스토리 상태 : ", history.state);

    $(document).on('click', '.article-title-link', function (e) {
        e.preventDefault();

        var state = {
            page: $('#currentPage').val(),
            boardId: $('#articleContent').data('article-board-id'),
        };

        var articleUrl = $(this).attr('href'); // 게시글 상세보기 URL

        // 특정 키와 함께 현재 상태를 저장
        history.pushState(state, '', articleUrl);

        console.log("현재 히스토리 상태 : ", history.state);

        // 게시글 상세보기 페이지로 이동
        window.location.href = articleUrl;
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
    $(document).on('click', '.article-list-all-page-btn', function (e) {
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
            url: '/article/ArticleListAllController/fetchArticles',
            type: 'GET',
            data: data,
            dataType: 'json',
            success: function (response) {
                $('#articleContent').html(response.html);
                updateDateVisibility();
                // window.history.pushState(data, "", "?page=" + data.page + "&articlesPerPage=" + data.articlesPerPage + "&keyword=" + encodeURIComponent(data.keyword));
                const queryString = $.param(data);

                // 현재 상태와 함께 history에 저장
                window.history.pushState(data, "", "?" + queryString);
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
            articlesPerPage: $('#articlesPerPage').val(),
            keyword: $('#searchForm input[name="keyword"]').val(),
            element: $('#searchForm select[name="element"]').val(),
            period: $('#searchForm select[name="period"]').val(),
            startDate: $('#start-date').val(),
            endDate: $('#end-date').val()
        };
    }

    $(document).on('click', '.show-reply', function () {
        var articleId = $(this).data('article-id');

        var isOpen = $('.replyToggle-' + articleId + '').is(':visible');
        if (isOpen) {
            $('.fa-reply-toggle-arrow-' + articleId + '').removeClass('fa-caret-up');
            $('.fa-reply-toggle-arrow-' + articleId + '').addClass('fa-caret-down');
        } else if (!isOpen) {
            $('.fa-reply-toggle-arrow-' + articleId + '').removeClass('fa-caret-down');
            $('.fa-reply-toggle-arrow-' + articleId + '').addClass('fa-caret-up');
        }
        $('.replyToggle-' + articleId + '').toggle(180);
    });

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
