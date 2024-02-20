$(document).ready(function () {
    $('#articlesPerPage').change(function () {
        $('#articlesPerPageForm').submit();
    });

    $('#select-period').change(function () {
        var selectedPeriod = $(this).val();
        if (selectedPeriod === 'custom') {
            $('.select-date').show();
        } else {
            $('.select-date').hide();
        }
    }).trigger('change');

    $('.search-form').submit(function (e) {
        var selectedPeriod = $('select[name="period"]').val();
        if (selectedPeriod === 'custom' && (!$('#start-date').val() || !$('#end-date').val())) {
            alert('사용자 지정 기간을 선택한 경우 시작 날짜와 종료 날짜를 입력해야 합니다.');
            e.preventDefault();
        }
    });
});