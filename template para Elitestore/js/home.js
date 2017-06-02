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
        var windowst = jQuery('.slider-content-container').width;
        var indice = Math.round((jQuery('.slider-content .good').length)/2)-1;
        var intermedio;
        intermedio=2*indice*medidasDiv;
        intermedio = (windowst-intermedio-medidasDiv)/2;

        alert(-intermedio);
        jQuery('.slider-content').css('margin-left', intermedio);
        jQuery('.slider-content').children('.good').eq(Math.round((jQuery('.slider-content .good').length)/2)-1).addClass("central");


    }
};

jQuery(function($){


    HomeApp.init();

});