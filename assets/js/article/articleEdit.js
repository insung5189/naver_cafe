$(document).ready(function () {
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