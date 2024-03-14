$(document).ready(function () {

    fetchPageData(1);

    if (location.hash === "#mycomments") {
        $('.my-activity-list-style a').removeClass('on underline');
        $('#my_activity_my_comments_area').addClass('on underline');

        // '내가 쓴 댓글' 데이터 로드
        fetchPageData(1);
    }

    if (location.hash === "#myarticles") {
        $('.my-activity-list-style a').removeClass('on underline');
        $('#my_activity_my_articles_area').addClass('on underline');

        // '내가 쓴 게시글' 데이터 로드
        fetchPageData(1);
    }

    // 나의활동요약(내가 쓴 게시글) 클릭이벤트
    $(document).on('click', '.my-wrote-articles', function () {
        $('.my-activity-list-style a').removeClass('on underline');
        $('#my_activity_my_articles_area').addClass('on underline');
        fetchPageData(1);
    });

    // 나의활동요약(내가 쓴 댓글) 클릭이벤트
    $(document).on('click', '.my-wrote-comments', function () {
        $('.my-activity-list-style a').removeClass('on underline');
        $('#my_activity_my_comments_area').addClass('on underline');
        fetchPageData(1);
    });

    // 탭 처리 클릭이벤트
    $('.link_sort').click(function () {
        $('.my-activity-list-style a').removeClass('on underline');
        $(this).addClass('on underline');
        fetchPageData(1);
    });

    $('.pagination a:first').addClass('active');

    // 페이지네이션 링크 클릭 이벤트
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        var page = $(this).data('page');
        fetchPageData(page);
    });

    function fetchPageData(page) {
        var tabId = $('.on').attr('id');
        var tabMethod;

        switch (tabId) {
            case 'my_activity_my_articles_area':
                tabMethod = 'loadMyArticles';
                break;
            case 'my_activity_my_comments_area':
                tabMethod = 'loadMyComments';
                break;
            case 'my_activity_my_commented_articles_area':
                tabMethod = 'loadMyCommentedArticles';
                break;
            case 'my_activity_my_liked_articles_area':
                tabMethod = 'loadMyLikedArticles';
                break;
            case 'my_activity_my_deleted_articles_area':
                tabMethod = 'loadMyDeletedArticles';
                break;
            default:
                console.log('잘못된 요청입니다.');
                return;
        }
        $.ajax({
            url: '/member/MyActivityController/' + tabMethod,
            type: 'GET',
            data: { page: page },
            dataType: 'json',
            success: function (response) {
                // 성공 시 페이지 콘텐츠 업데이트
                $('#tabContentArea').html(response.html);
                if (window.location.hash) {
                    history.pushState("", document.title, window.location.pathname + window.location.search);
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    $(document).on('click', '.my-articles-delete-btn', function (e) {
        var selectedArticles = [];
        $('input.input_check_article:checked').each(function () {
            var articleId = $(this).attr('id').split('-')[2]; // "check-article-1"에서 ID 부분을 가져옴.
            selectedArticles.push(articleId);
        });

        if (selectedArticles.length > 0) {
            if (confirm('선택한 글을 삭제하시겠습니까?')) {
                $.ajax({
                    url: '/member/myactivitycontroller/myActivityArticlesSoftDelete',
                    type: 'POST',
                    dataType: 'json',
                    data: { articles: selectedArticles },
                    success: function (response) {
                        // response.message를 사용하여 서버로부터 받은 메시지를 사용자에게 보여줌
                        alert(response.message);
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function () {
                        alert('서버 통신 오류가 발생했습니다. 다시 시도해주세요.');
                    }
                });
            }
        } else {
            alert('삭제할 글을 선택해주세요.');
        }
    });

    $(document).on('click', '.my-comments-delete-btn', function (e) {
        var selectedComments = [];
        $('input.input_check_comment:checked').each(function () {
            var commentId = $(this).attr('id').split('-')[2];
            selectedComments.push(commentId);
        });

        if (selectedComments.length > 0) {
            if (confirm('선택한 댓글을 삭제하시겠습니까?')) {
                $.ajax({
                    url: '/member/myactivitycontroller/myActivityCommentsSoftDelete',
                    type: 'POST',
                    dataType: 'json',
                    data: { comments: selectedComments },
                    success: function (response) {
                        alert(response.message);
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function () {
                        alert('서버 통신 오류가 발생했습니다. 다시 시도해주세요.');
                    }
                });
            }
        } else {
            alert('삭제할 댓글을 선택해주세요.');
        }
    });

    $(document).on('click', '.my-liked-articles-cancel-btn', function (e) {
        var selectedArticles = [];
        $('input.input_check_liked_article:checked').each(function () {
            var articleId = $(this).attr('id').split('-')[3]; // "check-article-1"에서 ID 부분을 가져옴.
            selectedArticles.push(articleId);
        });

        if (selectedArticles.length > 0) {
            if (confirm('선택한 글의 좋아요를 취소하시겠습니까?')) {
                $.ajax({
                    url: '/member/myactivitycontroller/myActivityArticlesLikedCancel',
                    type: 'POST',
                    dataType: 'json',
                    data: { articles: selectedArticles },
                    success: function (response) {
                        alert(response.message);
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function () {
                        alert('서버 통신 오류가 발생했습니다. 다시 시도해주세요.');
                    }
                });
            }
        } else {
            alert('좋아요를 취소할 글을 선택해주세요.');
        }
    });

    // 작성글 전체 선택/해제 기능
    $(document).on('change', '#check-article-all-this-page', function () {
        $('input.input_check_article').prop('checked', $(this).prop('checked'));
    });

    // 작성댓글 전체 선택/해제 기능
    $(document).on('change', '#check-comment-all-this-page', function () {
        $('input.input_check_comment').prop('checked', $(this).prop('checked'));
    });

    // 좋아요한글 전체 선택/해제 기능
    $(document).on('change', '#check-liked-article-all-this-page', function () {
        $('input.input_check_liked_article').prop('checked', $(this).prop('checked'));
    });
});