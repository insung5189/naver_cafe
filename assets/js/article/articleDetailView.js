$(document).ready(function () {
    var articleId = $('#article').data('article-id');
    var viewedArticles = localStorage.getItem('viewedArticles');
    viewedArticles = viewedArticles ? JSON.parse(viewedArticles) : {};

    if (!viewedArticles[articleId]) {
        console.log("처음 조회하는 게시물입니다. 조회수를 증가시킬 수 있습니다.");

        $.post('/article/articledetailcontroller/increaseHitCount', { articleId: articleId }, function (data) {
            if (data.success) {
                $('#hitCount').text(data.newHitCount);
            } else {
                console.error(data.message);
            }
        }, 'json');

        // 최초조회한 후 조회정보를 로컬스토리지에 저장.
        viewedArticles[articleId] = true;
        localStorage.setItem('viewedArticles', JSON.stringify(viewedArticles));
    } else {
        console.log("이미 조회한 게시물입니다.");
    }

    // 링크복사 클릭 이벤트
    $('#articleLink').click(function () {
        var textToCopy = window.location.href;

        var tempElement = $('<textarea>').val(textToCopy).appendTo('body').select();
        document.execCommand('copy');
        tempElement.remove();

        alert('게시글 주소가 클립보드에 복사되었습니다: \n' + textToCopy);
    });

    // 첨부파일 토글 버튼 클릭 이벤트
    $('.file-array-toggle').click(function () {
        var session = $(this).data('session');
        if (session) {
            $('.article-file-list').slideToggle(100);
        } else if (confirm('첨부파일은 회원만 조회할 수 있습니다.\n로그인페이지로 이동하시겠습니까?')) {
            document.cookie = "redirect_url=" + window.location.href + ";path=/";
            window.location.href = '/member/logincontroller';
        }
    });

    // 댓글 관련 js

    // 댓글 등록 후 해당 위치로 스크롤
    if (window.location.hash) {
        var commentElement = $(window.location.hash);
        if (commentElement.length) {
            $('html, body').animate({
                scrollTop: commentElement.offset().top
            }, 1000, function () {
                // 스크롤 이동 완료 후 주소창에서 해시값 제거
                var cleanUrl = window.location.href.split('#')[0];
                window.history.replaceState({}, document.title, cleanUrl);
            });
        }
    }

    // 댓글 정렬 옵션(ASC, DESC)
    $('.comment-sort-option').click(function (e) {
        e.preventDefault();
        var articleId = $(this).data('articleId');
        var sortOption = $(this).data('sort');

        $.ajax({
            url: '/article/ArticleDetailController/commentSortAction',
            type: 'GET',
            data: {
                articleId: articleId,
                sortOption: sortOption
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('.comment-foreach-box > ul').html(response.html);

                    if (sortOption == 'ASC') {
                        $('.create-date-asc-btn').css({
                            'color': '#000',
                            'font-weight': '700'
                        });
                        $('.create-date-desc-btn').css({
                            'color': '#b7b7b7',
                            'font-weight': '400'
                        });
                    }
                    else {
                        $('.create-date-asc-btn').css({
                            'color': '#b7b7b7',
                            'font-weight': '400'
                        });
                        $('.create-date-desc-btn').css({
                            'color': '#000',
                            'font-weight': '700'
                        });
                    }
                } else {
                    alert(response.error || '댓글 목록을 불러오는 데 실패했습니다.');
                }
            },
            error: function (xhr, status, error) {
                alert('댓글 목록을 불러오는 데 실패했습니다.');
            }
        });
    });

    // 댓글 수정/삭제 토글 버튼 클릭 이벤트
    $('body').on('click', '.comment-edit-delete-toggle', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var commentId = $(this).data('comment-id');

        var $toggleBox = $('#comment-' + commentId).find('.comment-edit-and-delete-btn-box');

        $toggleBox.slideToggle(100);

        $('.comment-edit-and-delete-btn-box').not($toggleBox).hide();
    });

    // 댓글 수정/삭제 닫기 클릭이벤트(문서 아무곳이나)
    $(document).on('click', function (e) {
        $('.comment-edit-and-delete-btn-box').slideUp(100);
    });

    // 댓글 text-area 가변적 높이조절
    $('.comment-text-area').each(function () {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;');
    }).on('input', function () {
        this.style.height = '17px';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // 댓글 텍스트 갯수 제한 및 카운팅
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

    // 댓글 첨부이미지 리셋 V
    $('#imgPreview').on('click', '.img-preview-wrap', function () {
        $(this).remove();
        $('#commentImage').val('');
    });


    // 답글 관련 js

    var commentReplyId = $(this).data('comment-reply-id');

    // 답글 작성 토글 버튼 클릭 이벤트
    $('body').on('click', '.create-comment-reply-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var commentReplyId = $(this).data('comment-reply-id');

        var $commentRelpytoggleBox = $('#comment-reply-' + commentReplyId).find('#reply-comment');

        $commentRelpytoggleBox.slideToggle(100);

    });

    // 답글 작성 닫기 버튼 클릭 이벤트
    $('body').on('click', '.cancel-comment-reply-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var commentReplyId = $(this).data('comment-reply-id');

        var $commentRelpytoggleBox = $('#comment-reply-' + commentReplyId).find('#reply-comment');

        $commentRelpytoggleBox.slideUp(100);
    });

    // 답글 text-area 가변적 높이조절 
    $('body').on('input', '.comment-text-area-reply', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // 답글 텍스트 갯수 제한 및 카운팅
    $('body').on('input', '.comment-text-area-reply', function () {
        var commentReplyId = $(this).data('comment-reply-id');
        var currentLength = $(this).val().length;
        $('[data-text-calculate-reply-id="' + commentReplyId + '"]').text(currentLength + ' / 3000').show();

        if (currentLength > 2999) {
            alert("텍스트는 최대 3000자까지 입력 가능합니다.");
            $(this).val($(this).val().substr(0, 3000));
        }
    });

    // 답글 파일등록 미리보기
    $('body').on('change', '[data-comment-image-reply-id]', function () {
        var commentReplyId = $(this).data('comment-image-reply-id');
        if (this.files && this.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var previewHtml = '<a href="javascript:void(0);" class="img-preview-wrap-reply"><img src="' + e.target.result + '" style="max-width: 54px; max-height: 54px;"></a>';
                $('[data-img-preview-reply-id="' + commentReplyId + '"]').html(previewHtml);
            };

            if (this.files[0]) {
                reader.readAsDataURL(this.files[0]);
            }
        }
    });

    // 답글 첨부이미지 리셋
    $('body').on('click', '[data-img-preview-reply-id]', function () {
        var commentReplyId = $(this).data('img-preview-reply-id');
        $(this).empty();
        $('[data-comment-image-reply-id="' + commentReplyId + '"]').val('');
    });

});