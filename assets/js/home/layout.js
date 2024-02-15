$(document).ready(function() {
    $('.cafe-info-tab').click(function() {
        $('.cafe-details').show();
        $('.user-activity').hide();
    });

    $('.user-activity-tab').click(function() {
        $('.user-activity').show();
        $('.cafe-details').hide();
    });

    $('.cafe-details').show();
    $('.user-activity').hide();
});