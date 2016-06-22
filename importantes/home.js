/*
 * Author: francisco corraliza
 * Description: carrusel de productos en la home
 */
var medidasDiv = 240;
var HomeApp = {
    init: function() {
                    HomeApp.showIntro();
    },

    showIntro : function() {
        var windowst = jQuery('.slider-content-container').width();
        var indice = Math.round((jQuery('.slider-content .good').length)/2);
        var intermedio;
        var intermediado =2*indice*medidasDiv;
        intermedio = (windowst-intermediado-medidasDiv)/2;

        jQuery('.slider-content').css('margin-left', intermedio);
        jQuery('.slider-content').children('.good').eq(Math.round((jQuery('.slider-content .good').length)/2)-1).addClass("central");
    },
    Negativo : function() {
        var windowst = jQuery('.slider-content-container').width();
        var indice = Math.round((jQuery('.slider-content .good').length)/2);
        var intermedio;
        var actual = jQuery('.central').attr('id');
        var nuevo = actual-1;
        var intermediado =2*nuevo*medidasDiv;
        
        jQuery("#" + medidasDiv).removeClass('central');
        alert(actual);
        jQuery("#" + medidasDiv).addClass('central');
        intermedio = (windowst-intermediado-medidasDiv)/2;

        jQuery('.slider-content').css('margin-left', -intermedio);
    }
};

jQuery(function($){
    $( ".prev" ).on( "click", function() {
      HomeApp.Negativo();
    });

    HomeApp.init();

});

