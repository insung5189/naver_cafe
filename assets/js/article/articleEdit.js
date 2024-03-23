$(document).ready(function () {

    // 사용자가 작성 중이던 form 페이지를 떠나려 할 때 표시되는 경고메시지
    var formModified = false;

    $('body').on('keyup', '#board-select, #title, #fileInput, input[type="radio"]', function () {
        formModified = true;
    });

    $('body').on('click', 'input[type="radio"]', function () {
        formModified = true;
    });

    $(window).on('beforeunload', function () {
        if (formModified) {
            return '변경사항이 저장되지 않을 수 있습니다.';
        }
    });

    $('form').submit(function () {
        $(window).off('beforeunload');
    });

    // CKEDITOR.
    ClassicEditor
        .create(document.querySelector('.article-content-area'), {
            // CK에디터 custom-build할때 markdown기능 추가하면 DB에 저장될 때 markdown으로 저장되어서 상세보기 페이지에서 이미지 및 미디어 확인불가
            ckfinder: {
                uploadUrl: '/article/articleEditController/uploadImgFile'
            },
            mediaEmbed: {
                previewsInData: true,
            },
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            },
            placeholder: '내용을 입력해주세요.(에디터의 도움말을 보시려면 Alt+0 키를 눌러주세요.)',
            fontFamily: {
                options: [
                    'default',
                    '궁서체',
                    '바탕',
                    '돋움',
                    '굴림',
                    '고딕',
                    '명조',
                    '나눔고딕',
                    '맑은고딕',
                    '굴림체',
                    '명조체',
                    '바탕체',
                    '돋움체',
                    'Helvetica',
                    'D2Coding',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',

                ],
                supportAllValues: true
            },
            fontSize: {
                options: [10, 12, 14, 'default', 18, 20, 22],
                supportAllValues: true
            },
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    },
                    {
                        name: 'a',
                        attributes: {
                            target: true,
                            download: true
                        }
                    }
                ]
            },
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            removePlugins: [
                'ExportPdf',
                'ExportWord',
                'CKBox',
                'CKFinder',
                'EasyImage',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                'MathType',
                'Table'
            ]
        })
        // .then부분은 에디터가 성공적으로 초기화된 후 CK에디터 인스턴스에 직접 접근할 수 있는 부분.
        .then(editor => {
            window.editor = editor;
            // 페이지 로드 후 hidden 필드에서 기존 게시글 내용을 읽어와 CKEditor에 세팅
            var existingContent = $('#existingArticleContent').val();

            var isEdit = $('#isEdit').val();
            if (isEdit) {
                editor.setData(existingContent);
            }
            editor.model.document.on('change:data', () => {
                formModified = true;
            });

            // 파일 업로드 버튼 클릭 이벤트 리스너 등록
            $(document).on('change', '#fileInput', function (e) {
                e.preventDefault();
                if (this.files.length > 0) {
                    var fileInput = $('#fileInput')[0];
                    var file = fileInput.files[0]; // 첫 번째 선택된 파일
                    var formData = new FormData();

                    formData.append('file', file); // 단일 파일을 'file' 키에 추가

                    $.ajax({
                        url: '/article/articleEditController/uploadFile',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            // 서버로부터 받은 응답을 JSON으로 파싱
                            var data = JSON.parse(response);
                            if (data && data.uploaded && data.uploaded === 1) {
                                // 성공적으로 파일이 업로드되면, CKEditor에 테이블 형태로 파일 링크를 삽입
                                const fileLinkHtml =
                                    `<p>&nbsp;</p>
                                    <figure class="table">
                                    <table style="border-collapse: collapse; width: 80%;">
                                <thead>
                                    <tr>
                                        <th><span style="font-size:13px;">${data.fileName}</span></th>
                                        <th><span style="font-size:13px;">${data.fileSize}KB</span></th>
                                        <th><a href="${data.url}"><span style="font-size:28px;">💾</span></a></th>
                                    </tr>
                                </thead>
                            </table>
                            </figure>
                            <br>
                            <p>&nbsp;</p>`;

                                if (window.editor) {
                                    window.editor.model.change(writer => {
                                        const viewFragment = window.editor.data.processor.toView(fileLinkHtml);
                                        const modelFragment = window.editor.data.toModel(viewFragment);
                                        window.editor.model.insertContent(modelFragment, window.editor.model.document.selection);
                                    });
                                    $('#fileInput').val('');
                                }
                            } else {
                                // 파일 업로드 실패 처리
                                $('#fileInput').val('');
                                alert('파일 업로드 실패: ' + (data.error ? data.error.message : '알 수 없는 오류 발생.'));
                                console.error('파일 업로드 실패:', data.error ? data.error.message : '알 수 없는 오류 발생.');
                            }
                        },
                        error: function () {
                            alert('파일 업로드에 실패했습니다.');
                            console.error('파일 업로드에 실패했습니다.');
                        }
                    });
                }
            });
        })
        .catch(error => {
            console.error('CKEditor가 초기화되지 못했습니다.: ', error);
            alert('CKEditor가 초기화되지 못했습니다.: ', error);
        });

    var parentBoardId = $('#parentBoardId').val();

    // 부모 게시판 정보에 따른 게시판 선택 상자 설정
    $('#board-select').val(parentBoardId).prop('disabled', !!parentBoardId);

    // 게시판 선택 시 말머리 업데이트
    $('#board-select').change(function () {
        updatePrefixSelect();
    });

    // 말머리 업데이트 및 선택 처리
    function updatePrefixSelect() {
        var boardId = $('#board-select').val();
        var prefixSelect = $('#prefix-select');
        prefixSelect.empty().append($('<option>', { value: '', text: '말머리 선택' }));

        // 게시판 ID 별 말머리 정의
        var prefixes = {
            '4': ['PHP', 'MYSQL', 'APACHE', 'JS', 'HTML', 'CSS', '기타'],
            '5': ['질문', '답변']
        };

        // 해당 게시판의 말머리가 존재하면 추가
        if (prefixes[boardId]) {
            $.each(prefixes[boardId], function (index, prefix) {
                var option = $('<option>', {
                    value: prefix,
                    text: prefix
                }).appendTo(prefixSelect);

                // 선택 이벤트 핸들러 추가
                if (prefix === parentPrefix) {
                    option.prop('selected', true);
                }
            });

            prefixSelect.prop('disabled', false);
        } else {
            prefixSelect.prop('disabled', true);
        }
    }

    // 말머리 선택 상자 변경 이벤트 핸들러 업데이트
    $(document).on('change', '#prefix-select', function () {
        // 선택된 말머리 업데이트
        parentPrefix = $(this).val();
    });

    // 페이지 로드 시 초기 말머리 설정
    updatePrefixSelect();

    // 수정 모드인지 확인
    if ($('#isEdit').val() === "1") {
        // 게시판과 말머리 선택 상태 설정
        var currentPrefix = $('#currentPrefix').val();
        var currentBoardId = $('#currentBoardId').val();

        $('#board-select').val(currentBoardId).trigger('change');

        // 게시판 셀렉트박스 업데이트
        if (currentBoardId === '4') {
            var prefixes = ['PHP', 'MySQL', 'Apache', 'JavaScript', 'HTML', 'CSS', '기타'];
            prefixes.forEach(function (prefix) {
                var selected = currentPrefix === prefix ? ' selected' : '';
                $('#prefix-select').append('<option value="' + prefix + '"' + selected + '>' + prefix + '</option>');
            });
        } else if (currentBoardId === '5') {
            var prefixes = ['질문', '답변'];
            prefixes.forEach(function (prefix) {
                var selected = currentPrefix === prefix ? ' selected' : '';
                $('#prefix-select').append('<option value="' + prefix + '"' + selected + '>' + prefix + '</option>');
            });
        }
    }

    $(document).on('input', '#title', function () {
        var title = $(this).val();
        if (title.length > 100) {
            alert('제목은 100자 이하로 입력해주세요.');
            $(this).val(title.substring(0, 100));
        }
    });

    $(document).on('submit', '#articleForm', function (e) {
        e.preventDefault();

        var currentURL = window.location.href;
        var urlSegments = currentURL.split('/');
        var formData = new FormData(this);
        formData.append('content', editor.getData());
        formData.append('currentURL', currentURL);

        var depth = parseInt(formData.get('depth'));
        var memberId = parseInt(formData.get('memberId'), 10);
        var parentId = formData.get('parentId');
        var boardId = formData.get('parentBoardId');
        var articleId = formData.get('articleId');
        var articleIdFromURL = urlSegments[urlSegments.length - 1];

        // memberId가 존재하지 않거나 음수인 경우
        if (!memberId || memberId < 0 || !Number.isInteger(memberId) || isNaN(memberId)) {
            alert('회원 정보가 올바르지 않습니다. 자동으로 로그아웃됩니다.');
            location.href = '/member/logincontroller/processLogout';
            return;
        }

        // 기본 게시글 작성인 경우.
        if (currentURL.endsWith('/article/articleeditcontroller')) {
            if (depth !== 0) {
                alert('기본 게시글 작성 시 depth는 0이어야 합니다.');
                return;
            }
        } else if (currentURL.includes('?parentId=')) { // 답글 작성인 경우
            if (depth < 1) {
                alert('답글 작성 시 depth는 1 이상이어야 합니다.');
                return;
            }
            if (!parentId) {
                alert('답글 작성 시 부모글의 정보는 반드시 존재해야 합니다.');
                return;
            } else if (!boardId) {
                alert('답글 작성 시 게시판정보는 반드시 존재해야 합니다.');
                return;
            }
        }

        var title = $('#title').val();
        if (title.length > 100) {
            alert('제목은 100자 이하로 입력해주세요.');
            return false;
        }

        $.ajax({
            url: '/article/articleeditcontroller/createArticle',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert('게시글이 성공적으로 등록되었습니다.');
                    window.location.href = response.redirectUrl;
                } else if (response.loginRequired) {
                    alert(response.message);
                    window.location.href = response.loginUrl;
                } else if (response.invalidBoardSelection) {
                    alert('오류발생: ' + response.message);
                    const boardSelect = $('#board-select');
                    boardSelect.empty(); // 기존의 option들을 모두 제거
                    boardSelect.append($('<option>', {
                        value: '',
                        text: '게시판에 올바른 값을 넣어주세요.'
                    }));
                    boardSelect.append($('<option>', { value: '1', text: '자유게시판' }));
                    boardSelect.append($('<option>', { value: '2', text: '건의게시판' }));
                    boardSelect.append($('<option>', { value: '3', text: '아무말게시판' }));
                    boardSelect.append($('<option>', { value: '4', text: '지식공유' }));
                    boardSelect.append($('<option>', { value: '5', text: '질문/답변게시판' }));
                } else if (response.invalidPrefixSelection) {
                    // 말머리 선택이 유효하지 않을 때 처리
                    alert('오류발생: ' + response.message);
                    const prefixSelect = $('#prefix-select');
                    prefixSelect.empty();
                    prefixSelect.append($('<option>', {
                        value: '',
                        text: '말머리를 선택해주세요.'
                    }));
                    prefixSelect.append($('<option>', {
                        value: '',
                        text: '말머리 선택 안함.'
                    }));
                    if (response.boardId === "4") {
                        prefixSelect.append($('<option>', { text: 'PHP', value: 'PHP' }));
                        prefixSelect.append($('<option>', { text: 'MYSQL', value: 'MYSQL' }));
                        prefixSelect.append($('<option>', { text: 'APACHE', value: 'APACHE' }));
                        prefixSelect.append($('<option>', { text: 'JS', value: 'JS' }));
                        prefixSelect.append($('<option>', { text: 'HTML', value: 'HTML' }));
                        prefixSelect.append($('<option>', { text: 'CSS', value: 'CSS' }));
                        prefixSelect.append($('<option>', { text: '기타', value: '기타' }));
                    } else if (response.boardId === "5") {
                        prefixSelect.append($('<option>', { text: '질문', value: '질문' }));
                        prefixSelect.append($('<option>', { text: '답변', value: '답변' }));
                    }
                } else {
                    alert('오류발생: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('게시글 등록 중 문제가 발생했습니다. 다시 시도해주세요.' + error);
            }
        });
    });
});