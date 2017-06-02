jQuery( document ).ready(function($) {
    $('.customercredit--class').on("click", function(e){
        if ($('.customercredit--class').hasClass('desplegado'))
        {
            $('.customercredit--class').removeClass('desplegado');
            $('.customercredit--class').addClass('plegado');
            $('.customer').slideUp();
        }
        else{
            $('.customercredit--class').addClass('desplegado');
            $('.customercredit--class').removeClass('plegado');
            $('.customer').slideDown();
        }
        
    });
    jQuery(document).on('click', ".filter--button", function(){
            $(".col-left-mobile").slideDown('slow', function(){
                $(".filter--button").addClass('open');
                $(".filter--button").removeClass('closed');
            });
    });

    jQuery(document).on('click', ".apply__container--buttons", function(){
            $(".col-left-mobile").slideUp('slow', function(){
                $(".filter--button").removeClass('open');
                $(".filter--button").addClass('closed');
            });
    });
    if((screen.width<1024)){
        $('footer.desktop').before($('.col-main'));
    }
});