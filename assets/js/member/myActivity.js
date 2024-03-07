$(document).ready(function () {

    // 나의 활동 공통 이벤트 핸들러 및 함수
    $('.my-activity-list-style a').click(function () {
        $('.my-activity-list-style a').removeClass('on underline');
        $(this).addClass('on underline');

        if ($(this).text().trim() === "작성글") {
            $('#myCommentsArea, #myCommentedArticlesArea, #myLikedArticlesArea, #myDeletedArticlesArea').hide();
            $('#myArticlesArea').show();
        } else if ($(this).text().trim() === "작성댓글") {
            $('#myArticlesArea, #myCommentedArticlesArea, #myLikedArticlesArea, #myDeletedArticlesArea').hide();
            $('#myCommentsArea').show();
        } else if ($(this).text().trim() === "댓글단 글") {
            $('#myArticlesArea, #myCommentsArea, #myLikedArticlesArea, #myDeletedArticlesArea').hide();
            $('#myCommentedArticlesArea').show();
        } else if ($(this).text().trim() === "좋아요한 글") {
            $('#myArticlesArea, #myCommentsArea, #myCommentedArticlesArea, #myDeletedArticlesArea').hide();
            $('#myLikedArticlesArea').show();
        }
        else if ($(this).text().trim() === "삭제한 게시글") {
            $('#myArticlesArea, #myCommentsArea, #myCommentedArticlesArea, #myLikedArticlesArea').hide();
            $('#myDeletedArticlesArea').show();
        }
    });

    $('.my-articles-delete-btn').on('click', function () {
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

    $('.my-comments-delete-btn').on('click', function () {
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

    $('.link-to-commented-article, .link-to-commented-article-contents').on('click', function () {
        var articleId = $(this).data('comment-article-id');
        var commentId = $(this).data('comment-id');

        // 게시글 상세 페이지로 이동하며, URL에 댓글의 고유 ID를 해시로 추가
        window.location.href = '/article/articleDetailcontroller/index/' + articleId + '#comment-' + commentId;
    });

    // 작성글 전체 선택/해제 기능
    $('#check-article-all-this-page').change(function () {
        $('input.input_check_article').prop('checked', $(this).prop('checked'));
    });

    // 작성댓글 전체 선택/해제 기능
    $('#check-comment-all-this-page').change(function () {
        $('input.input_check_comment').prop('checked', $(this).prop('checked'));
    });
});