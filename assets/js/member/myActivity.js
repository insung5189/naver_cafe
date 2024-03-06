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
});