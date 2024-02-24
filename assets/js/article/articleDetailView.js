$(document).ready(function () {
    $('#articleLink').click(function () {
        var textToCopy = window.location.href;

        var tempElement = $('<textarea>').val(textToCopy).appendTo('body').select();
        document.execCommand('copy');
        tempElement.remove();

        alert('게시글 주소가 클립보드에 복사되었습니다: \n' + textToCopy);
    });

    $('.file-array-toggle').click(function () {
        $('.article-file-list').slideToggle(100);
    });

    // 댓글 text-area 가변적 높이조절
    $('.comment-text-area').each(function () {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;');
    }).on('input', function () {
        this.style.height = '17px';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // 댓글 텍스트 갯수 제한
    $('#commentTextArea').on('input', function () {
        var currentTextLength = $(this).val().length;
        if (currentTextLength > 0) {
            $('.text-caculate').show().text(currentTextLength + ' / 3000');
        } else {
            $('.text-caculate').hide();
        }

        if (currentTextLength > 2999) {
            alert("텍스트는 최대 3000자까지 입력 가능합니다.");
            $(this).val($(this).val().substring(0, 3000));
            $('.text-caculate').text('3000 / 3000');
        }
    });

    // 댓글 파일등록 미리보기
    $('#commentImage').change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var imgHtml = '<a href="javascript:void(0);" class="img-preview-wrap"><img src="' + e.target.result + '" style="max-width: 54px; max-height: 54px;"></a>';
                $('#imgPreview').html(imgHtml);
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // 댓글 첨부이미지 리셋
    $('#imgPreview').on('click', '.img-preview-wrap', function () {
        $(this).remove();
        $('#commentImage').val('');
    });
});