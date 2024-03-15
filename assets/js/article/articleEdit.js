$(document).ready(function () {

    // 사용자가 작성 중이던 form 페이지를 떠나려 할 때 표시되는 경고메시지
    var formModified = false;

    $(window).on('beforeunload', function () {
        if (formModified) {
            return '변경사항이 저장되지 않을 수 있습니다.';
        }
    });

    $('form').submit(function () {
        $(window).off('beforeunload');
    });





    CKEDITOR.
        ClassicEditor.create(document.querySelector('.article-content-area'), {
            language: 'ko',
            ckfinder: {
                uploadUrl: '/article/articleEditController/uploadFile'
            },
            mediaEmbed: {
                previewsInData: true,
                // 기본 제공되는 미디어 임베드 기능을 사용합니다.
            },
            toolbar: {
                items: [
                    //'exportPDF', 'exportWord', '|',
                    'findAndReplace', 'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',
                    'undo', 'redo', '|',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                    'alignment', '|',
                    'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                    'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                    'textPartLanguage', '|',
                    'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            },
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
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
            placeholder: '내용을 입력해주세요.',
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
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
                    }
                ]
            },
            htmlEmbed: {
                showPreviews: true
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
            mention: {
                feeds: [
                    {
                        marker: '@',
                        feed: [
                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                            '@sugar', '@sweet', '@topping', '@wafer'
                        ],
                        minimumCharacters: 1
                    }
                ]
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
                'MathType'
            ]
        })
        .then(editor => {
            window.editor = editor;
            editor.model.document.on('change:data', () => {
                formModified = true;
            });
        })
        .catch(error => {
            console.error('CKEditor initialization failed: ', error);
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
});