$(document).ready(function () {

    window.addEventListener('popstate', function (event) {
        console.log('현재 상태:', event.state);
        // event.state를 사용해 페이지 콘텐츠를 업데이트하는 로직을 구현합니다.
    });

    // 사용자가 작성 중이던 form 페이지를 떠나려 할 때 표시되는 경고메시지
    var formModified = false;

    $(document).on('change', '.custom-search-input-main', function () {
        formModified = true;
    });

    $(window).on('beforeunload', function () {
        if (formModified) {
            return '변경사항이 저장되지 않을 수 있습니다.';
        }
    });

    $('.search-form-main').on('submit', function (e) {
        var keyword = $('.custom-search-input-main').val();
        if (keyword.length > 20) {
            alert("검색어는 20자를 초과할 수 없습니다.");
            e.preventDefault();
        }
    });

    $('form').submit(function () {
        $(window).off('beforeunload');
    });

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
});