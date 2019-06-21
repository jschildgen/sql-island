$(document).ready(function() {


    //----------------- menu ----------------

    //menu button and box
    $('.menu-content').hide();
    $('.menu-bg').hide();
    $('.menu-button').click(function(e){
        e.preventDefault();

        if(!$('.menu-content').is(':hidden')) {
            $('.menu-bg').hide();
        }
        else {
            $('.menu-bg').show();
        }
        $('.menu-content').slideToggle('slow');
    });

    $('.menu-bg').click(function(e){
        e.preventDefault();

        if(!$('.menu-content').is(':hidden')){
            $('.menu-content').slideToggle('slow');
            $('.menu-bg').hide();
        }

    });


});
