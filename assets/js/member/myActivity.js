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
        $('input.input_check:checked').each(function () {
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

    // 전체 선택/해제 기능
    $('#check-article-all-this-page').change(function () {
        $('input.input_check').prop('checked', $(this).prop('checked'));
    });
});