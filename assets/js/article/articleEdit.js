$(document).ready(function() {
    $('#board-select').change(function() {
        var board = $(this).val();
        var prefixSelect = $('#prefix-select');

        prefixSelect.empty().append($('<option>', { value: '', text: '말머리 선택' })).prop('disabled', true);

        if (board === '4') {
            var prefixs = ['PHP', 'MYSQL', 'APACHE', 'JS', 'HTML', 'CSS', '기타'];
            prefixSelect.prop('disabled', false);
            $(prefixs).each(function(index, prefix) {
                prefixSelect.append($('<option>', { value: prefix, text: prefix }));
            });
        } else if (board === '5') {
            var prefixs = ['질문', '답변'];
            prefixSelect.prop('disabled', false);
            $(prefixs).each(function(index, prefix) {
                prefixSelect.append($('<option>', { value: prefix, text: prefix }));
            });
        }
    });
});