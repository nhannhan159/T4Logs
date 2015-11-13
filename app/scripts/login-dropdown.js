$(document).ready(function(e){
    $('input[type="submit"]').mousedown(function(){
        $(this).css('background', '#2ecc71');
    });
    $('input[type="submit"]').mouseup(function(){
        $(this).css('background', '#1abc9c');
    });
})