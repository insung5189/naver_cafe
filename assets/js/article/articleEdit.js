$(document).ready(function() {
    $('#board-select').change(function() {
        var board = $(this).val();
        var topicSelect = $('#topic-select');

        topicSelect.empty().append($('<option>', { value: '', text: '말머리 선택' })).prop('disabled', true);

        if (board === '지식공유') {
            var topics = ['PHP', 'MYSQL', 'APACHE', 'JS', 'HTML', 'CSS', '기타'];
            topicSelect.prop('disabled', false);
            $(topics).each(function(index, topic) {
                topicSelect.append($('<option>', { value: topic, text: topic }));
            });
        } else if (board === '질문/답변게시판') {
            var topics = ['질문', '답변'];
            topicSelect.prop('disabled', false);
            $(topics).each(function(index, topic) {
                topicSelect.append($('<option>', { value: topic, text: topic }));
            });
        }
    });
});
