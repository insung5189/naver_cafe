$(document).ready(function () {

    var articleIdsStr = $('#articleIds').attr('data-articles');
    var articleIds = JSON.parse(articleIdsStr);
    localStorage.setItem('articles', JSON.stringify(articleIds));
    console.log("초기 인덱스: ", localStorage.getItem('articles'));

    $(document).on('click', '#boardBookMarkBtn', function () {
        var boardId = $(this).data('article-board');
        var memberId = $(this).data('member-id');
        var isBookmarked = $(this).data('member-bookmarking-this-board');

        $.ajax({
            url: '/article/articlelistcontroller/processBookMark',
            type: 'POST',
            dataType: 'json',
            data: {
                boardId: boardId,
                memberId: memberId,
                isBookmarked: isBookmarked
            },
            success: function (response) {
                if (response.success) {
                    if (response.isBookmarked) {
                        $('#boardBookMarkBtn').find('.fa-star').removeClass('fa-regular').addClass('fa-solid');
                        $('#favoriteBoardLayout').html(response.html);
                        $('#favoriteBoardLayout').show();
                        alert('게시판이 즐겨찾기에 추가되었습니다.');
                    } else {
                        $('#boardBookMarkBtn').find('.fa-star').removeClass('fa-solid').addClass('fa-regular');
                        $('#favoriteBoardLayout').html(response.html);
                        $('#favoriteBoardLayout').show();
                        alert('즐겨찾기가 해제되었습니다.');
                    }
                } else {
                    alert(response.message || '오류가 발생했습니다.');
                }
            },
            error: function (xhr, status, error) {
                alert('오류가 발생했습니다.');
            }
        });
    });

    var boardId = $('#articleContent').data('article-board-id');

    saveCurrentStateInitial()

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
                    console.log("ajax요청 후 업데이트 된 히스토리 상태(게시판별 보기) : ", history.state);
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
            boardId: boardId,
            articlesPerPage: $('#articlesPerPage').val(),
            keyword: $('#keyword').val(),
            element: $('#element').val(),
            period: $('#select-period').val(),
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

    // 뒤로가기의 대상이 해당 페이지라면 게시글 상세페이지로 접근하기 전 세팅되었던 페이지의 세팅을 출력함.
    window.onpopstate = function (event) {
        if (event.state) {
            fetchArticles(event.state);
        }
    };

    console.log("초기 페이지의 히스토리 상태 : ", localStorage.getItem('articleListState'));


    // 게시글 목록으로 돌아가기 기능을 위해 로컬스토리지에 히스토리 상태를 저장 후 활용
    function saveCurrentState() {
        var state = {
            page: $('#currentPage').val(),
            boardId: boardId,
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
            boardId: boardId,
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

});
