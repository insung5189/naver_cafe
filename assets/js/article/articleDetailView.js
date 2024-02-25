$(document).ready(function () {
    // 댓글 등록 후 해당 위치로 스크롤
    if (window.location.hash) {
        var commentElement = $(window.location.hash);
        if (commentElement.length) {
            $('html, body').animate({
                scrollTop: commentElement.offset().top
            }, 1000, function () {
                // 스크롤 완료 후 URL의 해시(#) 제거
                var cleanUrl = window.location.href.split('#')[0];
                window.history.replaceState({}, document.title, cleanUrl);
            });
        }
    }

    $('.comment-sort-option').click(function (e) {
        e.preventDefault();
        var articleId = $(this).data('article-id');
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
                var commentsContainer = $('.comment-foreach-box > ul');
                commentsContainer.empty();

                $.each(response.comments, function (index, comment) {
                    var backgroundColor = comment.isRecent ? 'style="background-color: #ffffe0;"' : ''; // 최근 댓글 확인
                    var commentHtml = '<li id="comment-' + comment.id + '" ' + backgroundColor + '>' +
                        '<div class="comment-author-action-box">' +
                        '<div class="comment-author-box">' +
                        '<a href="/작성자의_활동내역_링크">' +
                        '<img class="prfl-img-thumb" src="' + comment.profileImageUrl + '" alt="' + comment.authorName + ' 프로필이미지">' +
                        '</a>' +
                        '<div class="comment-content-each">' +
                        '<div class="comment-author">' + comment.authorName + '</div>' +
                        (comment.isArticleAuthor ? '<div class="is-article-author">작성자</div>' : '') +
                        '</div>' +
                        '</div>' +
                        (comment.isCommentAuthor ? '<div class="comment-edit-delete-btn">' +
                            '<i class="fa-solid fa-xl fa-ellipsis-vertical"></i>' +
                            '</div>' : '') +
                        '</div>' +
                        '<div class="comment-content-area">' +
                        '<p><span>' + comment.content + '</span></p>' +
                        (comment.commentImageUrl ? '<img src="' + comment.commentImageUrl + '" alt="댓글 첨부사진">' : '') +
                        '<div class="comment-info-box">' +
                        '<span>' + comment.date + '</span>' +
                        '<a href="/해당댓글_답글쓰기 링크">답글쓰기</a>' +
                        '</div>' +
                        '</div>' +
                        '<hr class="comment-hr-line">' +
                        '</li>';
                    commentsContainer.append(commentHtml);
                });
            },
            error: function (xhr, status, error) {
                alert('댓글 목록을 불러오는 데 실패했습니다.');
            }
        });
    });

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