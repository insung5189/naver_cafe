$(document).ready(function() {
    
    $('.toggle-daemoon').text('▲ 대문접기');

    $('.toggle-daemoon').click(function() {
        $('.daemoon-img').toggle();

        if($('.daemoon-img').is(':visible')) {
            $(this).text('▲ 대문접기');
        } else {
            $(this).text('▼ 대문보기');
        }
    });
});