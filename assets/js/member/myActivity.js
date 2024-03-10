$(document).ready(function () {

    // 나의 활동 공통 이벤트 핸들러 및 함수
    // $('.my-activity-list-style a').click(function () {
    //     $('.my-activity-list-style a').removeClass('on underline');
    //     $(this).addClass('on underline');

    //     if ($(this).text().trim() === "작성글") {
    //         $('#myCommentsArea, #myCommentedArticlesArea, #myLikedArticlesArea, #myDeletedArticlesArea').hide();
    //         $('#myArticlesArea').show();
    //     } else if ($(this).text().trim() === "작성댓글") {
    //         $('#myArticlesArea, #myCommentedArticlesArea, #myLikedArticlesArea, #myDeletedArticlesArea').hide();
    //         $('#myCommentsArea').show();
    //     } else if ($(this).text().trim() === "댓글단 글") {
    //         $('#myArticlesArea, #myCommentsArea, #myLikedArticlesArea, #myDeletedArticlesArea').hide();
    //         $('#myCommentedArticlesArea').show();
    //     } else if ($(this).text().trim() === "좋아요한 글") {
    //         $('#myArticlesArea, #myCommentsArea, #myCommentedArticlesArea, #myDeletedArticlesArea').hide();
    //         $('#myLikedArticlesArea').show();
    //     }
    //     else if ($(this).text().trim() === "삭제한 게시글") {
    //         $('#myArticlesArea, #myCommentsArea, #myCommentedArticlesArea, #myLikedArticlesArea').hide();
    //         $('#myDeletedArticlesArea').show();
    //     }
    // });


    // 탭 처리 클릭이벤트
    $('.link_sort').click(function () {
        $('.my-activity-list-style a').removeClass('on underline');
        $(this).addClass('on underline');
        var page = 1;
        fetchPageData(page);
    });


    $('.pagination a:first').addClass('active');

    // 페이지네이션 링크 클릭 이벤트
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        var page = $(this).data('page');
        fetchPageData(page); // AJAX 요청 함수 호출
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
                        if (response.success) {
                            alert('선택한 글이 성공적으로 삭제되었습니다.');
                            location.reload();
                        } else {
                            alert('글 삭제에 실패했습니다. 다시 시도해주세요.');
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
            var commentId = $(this).attr('id').split('-')[2]; // "check-comment-1"에서 ID 부분을 가져옴.
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
                        if (response.success) {
                            alert('선택한 댓글이 성공적으로 삭제되었습니다.');
                            location.reload();
                        } else {
                            alert('댓글 삭제에 실패했습니다. 다시 시도해주세요.');
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
                    url: '/member/myactivitycontroller/myActivityArticlesSoftDelete',
                    type: 'POST',
                    dataType: 'json',
                    data: { articles: selectedArticles },
                    success: function (response) {
                        if (response.success) {
                            alert('선택한 글이 성공적으로 삭제되었습니다.');
                            location.reload();
                        } else {
                            alert('글 삭제에 실패했습니다. 다시 시도해주세요.');
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