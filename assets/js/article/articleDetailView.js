$(document).ready(function () {
    $('#articleLink').click(function () {
        var textToCopy = window.location.href;

        // 텍스트를 임시 textarea에 넣기
        var tempElement = $('<textarea>').val(textToCopy).appendTo('body').select();
        document.execCommand('copy');
        tempElement.remove();

        // 사용자에게 알림 표시
        alert('게시글 주소가 클립보드에 복사되었습니다: \n' + textToCopy);
    });

    $('.file-array-toggle').click(function () {
        $('.article-file-list').slideToggle(100);
    });
});