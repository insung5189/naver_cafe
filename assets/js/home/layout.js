$(document).ready(function () {
    // 카페정보/나의활동 탭 토글
    function switchTab(activeTabClass, inactiveTabClass, showSelector, hideSelector) {
        $(inactiveTabClass + ' button').css({
            'color': '',
            'font-weight': ''
        });
        $(activeTabClass + ' button').css({
            'color': '#000',
            'font-weight': 'bold'
        });
        $(showSelector).show();
        $(hideSelector).hide();
    }

    $('.cafe-info-tab').click(function () {
        switchTab('.cafe-info-tab', '.user-activity-tab', '.cafe-details', '.user-activity');
    });
    $('.user-activity-tab').click(function () {
        var isLoggedIn = $('#userStatus').data('logged-in');
        if (!isLoggedIn) {
            if (confirm('로그인이 필요합니다.\n확인 버튼을 누르시면 로그인 페이지로 이동합니다.')) {
                window.location.href = '/member/logincontroller';
            }
        } else {
            switchTab('.user-activity-tab', '.cafe-info-tab', '.user-activity', '.cafe-details');
        }
    });

    // 즐겨찾기 게시판 버튼 토글
    $('.toggle-favorite-board').click(function () {
        $('.board-instructions').toggle();

        var isOpen = $('.board-instructions').is(':visible');
        if (isOpen) {
            $('.up-and-down-btn').css('background-image', "url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"15\" height=\"12\" viewBox=\"-4 -4 15 12\" x=\"68\" y=\"48\"><path fill=\"%23777\" fill-rule=\"evenodd\" d=\"M3.5 0L7 4H0z\"/></svg>')");
        } else {
            $('.up-and-down-btn').css('background-image', "url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"15\" height=\"12\" viewBox=\"-4 -4 15 12\" x=\"68\" y=\"48\"><path fill=\"%23777\" fill-rule=\"evenodd\" d=\"M3.5 4L7 0H0z\"/></svg>')");
        }
    });

    $('#inviteLink').click(function () {
        var textToCopy = "http://211.238.132.177/";

        // 텍스트를 임시 textarea에 넣기
        var tempElement = $('<textarea>').val(textToCopy).appendTo('body').select();
        document.execCommand('copy');
        tempElement.remove();

        // 사용자에게 알림 표시
        alert('주소가 클립보드에 복사되었습니다: ' + textToCopy);
    });

    // 게시판 버튼에 클릭 이벤트 바인딩
    // $(document).on('click', '.board-url', function () {
    //     // 클릭된 게시판의 data-board-id 속성 값 가져오기
    //     var boardId = $(this).data("board-id");
    //     fetchArticleBoard(1, boardId);
    // });

    function fetchArticleBoard(page, boardId) {
        var loadBoardMethod = '';

        switch (boardId) {
            case 1:
                loadBoardMethod = 'loadFreeBoard';
                break;
            case 2:
                loadBoardMethod = 'loadSuggestedBoard';
                break;
            case 3:
                loadBoardMethod = 'loadWordVomitBoard';
                break;
            case 4:
                loadBoardMethod = 'loadKnowledgeSharingBoard';
                break;
            case 5:
                loadBoardMethod = 'loadQnaBoard';
                break;
            default:
                console.log('잘못된 요청입니다.');
                return;
        }
        $.ajax({
            url: '/article/ArticleListController/' + loadBoardMethod,
            type: 'GET',
            data:
            {
                page: page,
                boardId: boardId
            },
            dataType: 'json',
            success: function (response) {
                // 성공 시 페이지 콘텐츠 업데이트
                $('#dynamicContent').html(response.html);
            },
            error: function (xhr, status, error) {
                // 요청에 실패했을 때 실행되는 함수
                console.error("Error: " + error);
                console.log(error);
                alert("게시판 컨텐츠를 불러오는데 실패했습니다.");
            }
        });
    }

});