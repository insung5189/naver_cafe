$(document).ready(function () {

    // 사용자가 작성 중이던 form 페이지를 떠나려 할 때 표시되는 경고메시지
    var formModified = false;

    $('body').on('keyup change', 'form input[type="file"], form textarea, form select', function () {
        formModified = true;
    });

    $(window).on('beforeunload', function () {
        if (formModified) {
            return '변경사항이 저장되지 않을 수 있습니다.';
        }
    });

    $('body').on('submit', 'form', function () {
        $(window).off('beforeunload');
    });

    var articleId = $('#article').data('article-id');
    var relatedArticleId = $('#relatedArticleItemLi').data('related-article-id');
    var relatedArticleTargetPage = $('#relatedArticleTargetPage').data('target-page');
    // 관련게시글 영역 로드
    relatedArticles(relatedArticleTargetPage, articleId);

    // 페이지네이션 페이지 변경 처리
    $(document).on('click', '.related-article-board-list-page-btn', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        relatedArticles(page, articleId);
    });

    // AJAX 요청 함수
    function relatedArticles(page, articleId) {
        var boardId = $('#relatedArticles').data('article-board-id');
        $.ajax({
            url: '/article/ArticleDetailController/relatedArticles',
            type: 'GET',
            data: {
                page: page,
                boardId: boardId,
                articleId: articleId
            },
            dataType: 'json',
            success: function (response) {
                $('#relatedArticles').html(response.html);
                $('#relatedArticles').find('.related-article-list-item').each(function () {
                    var relatedArticleId = $(this).data('related-article-id');
                    if (articleId === relatedArticleId) {
                        $(this).addClass('related-article-selected');
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error("Error: ", error);
            }
        });
    }


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
    $(document).on('click', '#articleLink', function () {
        var textToCopy = window.location.href;

        var tempElement = $('<textarea>').val(textToCopy).appendTo('body').select();
        document.execCommand('copy');
        tempElement.remove();

        alert('게시글 주소가 클립보드에 복사되었습니다: \n' + textToCopy);
    });

    // 첨부파일 토글 버튼 클릭 이벤트
    $(document).on('click', '.file-array-toggle', function () {
        var session = $(this).data('session');
        if (session) {
            $('.article-file-list').slideToggle(100);
        } else if (confirm('첨부파일은 회원만 조회할 수 있습니다.\n로그인페이지로 이동하시겠습니까?')) {
            document.cookie = "redirect_url=" + window.location.href + ";path=/";
            window.location.href = '/member/logincontroller';
        }
    });

    $(document).on('click', '.article-delete-btn', function () {
        var articleId = $(this).data('delete-article-id');

        if (confirm('이 글을 삭제하시겠습니까?')) {
            $.ajax({
                url: '/article/articledetailcontroller/deleteArticle',
                type: 'POST',
                dataType: 'json',
                data: { articleId: articleId },
                success: function (response) {
                    if (response.success) {
                        alert('글이 성공적으로 삭제되었습니다.');
                        window.location.href = '/article/articlelistcontroller/index/' + response.articleboardId;
                    } else if (response.loginRequired) {
                        alert(response.message);
                        window.location.href = response.loginUrl;
                    } else {
                        alert('글 삭제에 실패했습니다. 다시 시도해주세요.');
                    }
                },
                error: function () {
                    alert('서버 통신 오류가 발생했습니다. 다시 시도해주세요.');
                }
            });
        } else {
            alert('글 삭제를 취소했습니다.');
        }
    });

    $(document).on('click', '#articleLikeBtn', function () {
        var articleId = $('#article').data('article-id');

        $.ajax({
            url: '/article/articleDetailController/articleLike',
            type: 'POST',
            dataType: 'json',
            data: { articleId: articleId },
            success: function (response) {
                if (response.success) {
                    // 좋아요 추가 또는 삭제에 따라 UI 업데이트
                    if (response.action === 'added') {
                        $('.fa-heart').addClass('fa-solid').removeClass('fa-regular');
                    } else if (response.action === 'removed') {
                        $('.fa-heart').addClass('fa-regular').removeClass('fa-solid');
                    } else {
                        alert(response.message);
                    }
                    $('.like-count-num').text(response.likeCount); // 업데이트된 좋아요 수 반영
                } else if (response.loginRequired) {
                    if (confirm('로그인이 필요한 기능입니다.\n로그인페이지로 이동하시겠습니까?')) {
                        document.cookie = "redirect_url=" + window.location.href + ";path=/";
                        window.location.href = '/member/logincontroller';
                    }
                } else {
                    $('.like-count-num').text(response.likeCount); // 업데이트된 좋아요 수 반영
                    alert(response.message);
                }
            },
            error: function () {
                alert('서버 통신 오류가 발생했습니다. 다시 시도해주세요.');
            }
        });
    });

    // 댓글 관련 js

    // 등록순 버튼 클릭 이벤트
    $(document).on('click', '#sort-asc-btn', function (e) {
        e.preventDefault();
        var depthOption = $('#depthOptionCheckbox').is(':checked') ? 'ASC' : '';
        var treeOption = $('#treeOptionCheckbox').is(':checked') ? 'enabled' : 'disabled';
        updateSortDepthAndTreeOptions('ASC', depthOption, treeOption);
    });

    // 최신순 버튼 클릭 이벤트
    $(document).on('click', '#sort-desc-btn', function (e) {
        e.preventDefault();
        var depthOption = $('#depthOptionCheckbox').is(':checked') ? 'ASC' : '';
        var treeOption = $('#treeOptionCheckbox').is(':checked') ? 'enabled' : 'disabled';
        updateSortDepthAndTreeOptions('DESC', depthOption, treeOption);
    });

    // Depth 옵션 체크박스 상태 변경 이벤트
    $(document).on('change', '#depthOptionCheckbox', function () {
        // 현재 활성화된 정렬 옵션 확인
        var sortOption = $('#sort-asc-btn').hasClass('sort-btn-active') ? 'ASC' : 'DESC';
        var depthOption = $('#depthOptionCheckbox').is(':checked') ? 'ASC' : '';
        var treeOption = $('#treeOptionCheckbox').is(':checked') ? 'enabled' : 'disabled';
        updateSortDepthAndTreeOptions(sortOption, depthOption, treeOption);
    });

    // Tree 옵션 체크박스 상태 변경 이벤트
    $(document).on('change', '#treeOptionCheckbox', function () {
        // 현재 활성화된 정렬 옵션 확인 및 depthOption 상태 확인
        var sortOption = $('#sort-asc-btn').hasClass('sort-btn-active') ? 'ASC' : 'DESC';
        var depthOption = $('#depthOptionCheckbox').is(':checked') ? 'ASC' : '';
        var treeOption = $('#treeOptionCheckbox').is(':checked') ? 'enabled' : 'disabled';
        updateSortDepthAndTreeOptions(sortOption, depthOption, treeOption);
    });

    function updateSortDepthAndTreeOptions(sortOption, depthOption, treeOption) {
        var articleId = $('#sort-asc-btn').data('articleId');

        $.ajax({
            url: '/article/ArticleDetailController/commentSortAction',
            type: 'GET',
            data: {
                articleId: articleId,
                sortOption: sortOption,
                depthOption: depthOption,
                treeOption: treeOption
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('.comment-foreach-box > ul').html(response.html);

                    // 정렬 버튼 UI 업데이트
                    if (sortOption == 'ASC') {
                        $('#sort-asc-btn').removeClass('sort-btn-deactivate').addClass('sort-btn-active');
                        $('#sort-desc-btn').removeClass('sort-btn-active').addClass('sort-btn-deactivate');
                    } else { // DESC
                        $('#sort-desc-btn').removeClass('sort-btn-deactivate').addClass('sort-btn-active');
                        $('#sort-asc-btn').removeClass('sort-btn-active').addClass('sort-btn-deactivate');
                    }
                } else {
                    alert(response.error || '댓글 목록을 불러오는 데 실패했습니다.');
                }
            },
            error: function (xhr, status, error) {
                alert('댓글 목록을 불러오는 데 실패했습니다.');
            }
        });
    }

    // 댓글 수정/삭제 토글 버튼 클릭 이벤트
    $(document).on('click', '.comment-edit-delete-toggle', function (e) {
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
    $(document).on('input', '.comment-text-area', function () {
        var currentLength = $(this).val().length;
        const textMaxLength = 3000;
        if (currentLength > 0) {
            $('.text-caculate').text(currentLength + ' / ' + textMaxLength).show();
        } else {
            $('.text-caculate').hide();
        }
        if (currentLength > textMaxLength - 1) {
            alert("텍스트는 최대 " + textMaxLength + "자까지 입력 가능합니다.");
            $(this).val($(this).val().substr(0, textMaxLength));
        }
    });

    // 댓글 파일등록 미리보기
    $('#commentImage').change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var imgHtml = '<a href="javascript:void(0);" class="img-preview-wrap"><i class="fa-solid fa-circle-xmark fa-xl x-btn-position"></i><img src="' + e.target.result + '" style="max-width: 54px; max-height: 54px;"></a>';
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


    // 답글 관련 js

    // 답글 작성 토글 버튼 클릭 이벤트
    $(document).on('click', '.create-comment-reply-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var commentReplyId = $(this).data('comment-reply-id');

        var $commentRelpytoggleBox = $('#comment-reply-' + commentReplyId).find('#reply-comment');

        $commentRelpytoggleBox.slideToggle(100);

    });

    // 답글 작성 닫기 버튼 클릭 이벤트
    $(document).on('click', '.cancel-comment-reply-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var commentReplyId = $(this).data('comment-reply-id');

        var $commentRelpytoggleBox = $('#comment-reply-' + commentReplyId).find('#reply-comment');

        $commentRelpytoggleBox.slideUp(100);
    });

    // 답글 text-area 가변적 높이조절 
    $(document).on('input', '.comment-text-area-reply', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // 답글 텍스트 갯수 제한 및 카운팅
    $(document).on('input', '.comment-text-area-reply', function () {
        var commentReplyId = $(this).data('comment-reply-id');
        var currentLength = $(this).val().length;
        const textMaxLength = 3000;
        if (currentLength > 0) {
            $('[data-text-calculate-reply-id="' + commentReplyId + '"]').text(currentLength + ' / ' + textMaxLength).show();
        } else {
            $('[data-text-calculate-reply-id="' + commentReplyId + '"]').hide();
        }
        if (currentLength > textMaxLength - 1) {
            alert("텍스트는 최대 " + textMaxLength + "자까지 입력 가능합니다.");
            $(this).val($(this).val().substr(0, textMaxLength));
        }
    });

    // 답글 파일등록 미리보기
    $(document).on('change', '[data-comment-image-reply-id]', function () {
        var commentReplyId = $(this).data('comment-image-reply-id');
        if (this.files && this.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var previewHtml = '<a href="javascript:void(0);" class="img-preview-wrap-reply"><i class="fa-solid fa-circle-xmark fa-xl x-btn-position"></i><img src="' + e.target.result + '" style="max-width: 54px; max-height: 54px;"></a>';
                $('[data-img-preview-reply-id="' + commentReplyId + '"]').html(previewHtml);
            };

            if (this.files[0]) {
                reader.readAsDataURL(this.files[0]);
            }
        }
    });

    // 답글 첨부이미지 리셋
    $(document).on('click', '[data-img-preview-reply-id]', function () {
        var commentReplyId = $(this).data('img-preview-reply-id');
        $(this).empty();
        $('[data-comment-image-reply-id="' + commentReplyId + '"]').val('');
    });


    // 댓/답글 수정 js

    // 댓/답글 수정 폼 토글
    $(document).on('click', '[data-edited-comment-id]', function () {
        var commentId = $(this).data('edited-comment-id');
        var existingImageUrl = $(this).data('comment-image-url');

        if (!existingImageUrl || existingImageUrl === "/assets/file/commentFiles/img/") {
            $('[data-img-preview-edit-id="' + commentId + '"]').empty();
        } else {
            var previewHtml = '<a href="javascript:void(0);" class="img-preview-wrap"><i class="fa-solid fa-circle-xmark fa-xl x-btn-position"></i><img src="' + existingImageUrl + '" style="max-width: 54px; max-height: 54px;"></a>';
            $('[data-img-preview-edit-id="' + commentId + '"]').html(previewHtml);
        }

        $('#comment-' + commentId + ' .comment-author-action-box').hide();
        $('#comment-' + commentId + ' .comment-content-area').hide();
        $('#comment-' + commentId + ' .comment-edited-form-box').show();

        // 특정 댓글/답글의 수정 폼의 높이를 조절
        $('#comment-' + commentId + ' .comment-text-area-edit').each(function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });

    $(document).on('click', '[data-comment-edited-cancel-id]', function () {
        var commentId = $(this).data('comment-edited-cancel-id');
        $('#comment-' + commentId + ' .comment-author-action-box').show();
        $('#comment-' + commentId + ' .comment-content-area').show();
        $('#comment-' + commentId + ' .comment-edited-form-box').hide();
    });

    // 댓글/답글 수정
    $(document).on('submit', '.comment-edited-form-box form', function (e) {
        e.preventDefault();

        var form = $(this);
        var formData = new FormData(this);
        var commentId = form.data('update-comment-id');

        $.ajax({
            url: `/article/articledetailcontroller/editComment/` + commentId,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                var data = JSON.parse(response);
                if (data.success) {
                    // 댓글 내용 업데이트
                    $('#comment-content-' + commentId).text(data.content || form.find('[name="commentEditContent"]').val());

                    // 첨부 이미지가 있다면 이미지 업데이트
                    if (data.commentFileName) {
                        var imagePath = '/assets/file/commentFiles/img/' + data.commentFileName;
                        var previewHtml = '<img id="uploadedImage-' + commentId + '" src="' + imagePath + '" alt="댓글 첨부사진">';
                        $('#comment-content-img-' + commentId).html(previewHtml);
                        $('#comment-' + commentId + ' .comment-edit-btn').data('comment-image-url', imagePath);
                    } else {
                        $('#comment-' + commentId + ' .comment-edit-btn').data('comment-image-url', '');
                        $('#uploadedImage-' + commentId).remove();
                        $('[data-img-preview-edit-id="' + commentId + '"] a').remove();
                    }

                    // 수정 폼 숨기기 및 기타 UI 요소 표시
                    $('#comment-' + commentId + ' .comment-edited-form-box').hide();
                    $('#comment-' + commentId + ' .comment-author-action-box').show();
                    $('#comment-' + commentId + ' .comment-content-area').show();

                    alert(data.message);
                } else {
                    alert(data.message);
                }
            },
            error: function () {
                alert('댓글 수정에 실패했습니다.');
            }
        });
    });

    // 이미지 첨부 input 변경 시 미리보기 생성
    $(document).on('change', '[data-comment-image-edit-id]', function () {
        var reader = new FileReader();
        var commentEditId = $(this).data('comment-image-edit-id');
        var previewContainer = $('[data-img-preview-edit-id="' + commentEditId + '"]');

        if (this.files && this.files[0]) {
            reader.onload = function (e) {
                var previewHtml = '<a href="javascript:void(0);" class="img-preview-wrap"><i class="fa-solid fa-circle-xmark fa-xl x-btn-position"></i><img src="' + e.target.result + '" style="max-width: 54px; max-height: 54px;"></a>';
                previewContainer.html(previewHtml);
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            // 파일이 선택되지 않았을 경우 미리보기 컨테이너를 비웁니다.
            previewContainer.empty();
        }
    });

    // 수정할 댓/답글 첨부이미지 리셋
    $(document).on('click', '[data-img-preview-edit-id]', function () {
        var commentEditId = $(this).data('img-preview-edit-id');
        // 첨부파일 input 선택자 수정
        var fileInput = $('[data-comment-image-edit-id="' + commentEditId + '"]');
        fileInput.replaceWith(fileInput.val('').clone(true));
        $('.img-preview-wrap').remove(); // 미리보기 이미지 삭제
    });

    // 수정할 댓/답글 text-area 가변적 높이조절 
    $(document).on('input', '.comment-text-area-edit', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });



    // 수정할 댓/답글 텍스트 갯수 제한 및 카운팅
    $(document).on('input', '.comment-text-area-edit', function () {
        var commentEditId = $(this).data('comment-edit-id');
        var currentLength = $(this).val().length;
        const textMaxLength = 3000;
        if (currentLength > 0) {
            $('[data-text-calculate-edit-id="' + commentEditId + '"]').text(currentLength + ' / ' + textMaxLength).show();
        } else {
            $('[data-text-calculate-edit-id="' + commentEditId + '"]').hide();
        }
        if (currentLength > textMaxLength - 1) {
            alert("텍스트는 최대 " + textMaxLength + "자까지 입력 가능합니다.");
            $(this).val($(this).val().substr(0, textMaxLength));
        }
    });

    // 댓글/답글 삭제
    $(document).on('click', '[data-delete-comment-id]', function () {
        var deleteCommentId = $(this).data('delete-comment-id');
        var articleId = $('#article').data('article-id');
        if (confirm('댓글을 삭제하시겠습니까?')) {
            $.ajax({
                url: '/article/articledetailcontroller/deleteComment/' + deleteCommentId,
                type: 'POST',
                data: {
                    deleteCommentId: deleteCommentId,
                    articleId: articleId
                },
                success: function (response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        alert(data.message);
                        $('#comment-' + deleteCommentId).remove();
                    } else if (data.loginRequired) {
                        alert(data.message);
                        window.location.href = data.loginUrl;
                    } else {
                        alert(data.message);
                    }
                }, error: function () {
                    alert('댓글 삭제 중 오류 발생');
                }
            });
        }
    });

    $(document).on('click', '#scrollTopBtn', function () {
        $('html, body').animate({ scrollTop: 0 }, 0);
        return false;
    });

    $(document).on('click', '.article-edit-btn', function () {
        var articleId = $(this).data('article-id');
        window.location.href = '/article/articleeditcontroller/editForm/' + articleId;
    });

});