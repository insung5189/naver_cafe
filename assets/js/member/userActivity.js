$(document).ready(function () {

    fetchPageData(1);

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
        var memberId = $('.my_activity_sort_area-left').data('member-id');
        var tabMethod;

        switch (tabId) {
            case 'user_activity_user_articles_area':
                tabMethod = 'loadUserArticles';
                break;
            case 'user_activity_commented_articles_area':
                tabMethod = 'loadUserCommentedArticles';
                break;
            default:
                console.log('잘못된 요청입니다.');
                return;
        }
        $.ajax({
            url: '/member/UserActivityController/' + tabMethod,
            type: 'GET',
            data: {
                page: page,
                memberId: memberId
            },
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
});