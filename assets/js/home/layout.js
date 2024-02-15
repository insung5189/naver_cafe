$(document).ready(function () {
    // 공통 기능을 처리하는 함수
    function switchTab(activeTabClass, inactiveTabClass, showSelector, hideSelector) {
        // 비활성 탭 스타일 초기화
        $(inactiveTabClass + ' button').css({
            'color': '',
            'font-weight': ''
        });
        // 활성 탭 스타일 적용
        $(activeTabClass + ' button').css({
            'color': '#000',
            'font-weight': 'bold'
        });
        // 관련 섹션 표시 및 숨김 처리
        $(showSelector).show();
        $(hideSelector).hide();
    }

    $('.cafe-info-tab').click(function () {
        switchTab('.cafe-info-tab', '.user-activity-tab', '.cafe-details', '.user-activity');
    });

    $('.user-activity-tab').click(function () {
        switchTab('.user-activity-tab', '.cafe-info-tab', '.user-activity', '.cafe-details');
    });

    switchTab('.cafe-info-tab', '.user-activity-tab', '.cafe-details', '.user-activity');
});