$(document).ready(function () {

    saveCurrentStateInitial()

    var articleIdsStr = $('#articleIds').attr('data-articles');
    var articleIds = JSON.parse(articleIdsStr);
    localStorage.setItem('articles', JSON.stringify(articleIds));
    console.log("초기 인덱스: ", localStorage.getItem('articles'));

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
        var articleIdsStr = $('#articleIds').attr('data-articles');
        var articleIds = JSON.parse(articleIdsStr);
        $.ajax({
            url: '/article/ArticleListAllController/fetchArticles',
            type: 'GET',
            data: data,
            dataType: 'json',
            success: function (response) {
                if (response.errors) {
                    alert(response.errors);
                } else if (response.success) {
                    $('#articleContent').html(response.html);
                    var articleIdsStr = $('#articleIds').attr('data-articles');
                    var articleIds = JSON.parse(articleIdsStr);
                    localStorage.setItem('articles', JSON.stringify(articleIds));
                    console.log("ajax업데이트 후: ", localStorage.getItem('articles'));
                    updateDateVisibility();
                    saveCurrentState();
                    window.history.pushState(data, '', window.location.pathname + '?' + $.param(data));

                    console.log("ajax요청 후 업데이트 된 히스토리 상태(전체글보기) : ", history.state);
                }
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

    // 뒤로가기의 대상이 해당 페이지라면 게시글 상세페이지로 접근하기 전 세팅되었던 페이지의 세팅을 출력함.
    window.onpopstate = function (event) {
        if (event.state) {
            fetchArticles(event.state);
        }
    };

    // 게시글 목록으로 돌아가기 기능을 위해 로컬스토리지에 히스토리 상태를 저장 후 활용
    function saveCurrentState() {
        var state = {
            page: $('#currentPage').val() ? $('#currentPage').val() : 1,
            boardId: 'all',
            articlesPerPage: $('#articlesPerPage').val(),
            keyword: $('#keyword').val(),
            element: $('#element').val(),
            period: $('#select-period').val(),
            startDate: $('#start-date').val(),
            endDate: $('#end-date').val()
        };
        localStorage.setItem('articleListState', JSON.stringify(state));
    }

    function saveCurrentStateInitial() {
        var state = {
            page: '1',
            boardId: 'all',
            articlesPerPage: '15',
            keyword: '',
            element: 'article-comment',
            period: 'all',
            startDate: '',
            endDate: ''
        };
        localStorage.setItem('articleListState', JSON.stringify(state));
    }

    $(document).on('click', '.article-title-link', function () {
        saveCurrentState();
    });

    console.log("초기 페이지의 히스토리 상태 : ", localStorage.getItem('articleListState'));
});
