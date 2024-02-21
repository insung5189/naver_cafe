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
});