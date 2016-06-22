/*
 * Author: francisco corraliza
 * Description: carrusel de productos en la home
*/

var carruselHome = {
    medidasDiv: 240,
    constructor: function(){
        var windowst = jQuery('.slider-content-container').width();
        var indice = Math.round((jQuery('.slider-content .good').length)/2)-2;
        var intermedio;
        var intermediado =2*indice*medidasDiv;
        intermedio = (windowst-intermediado-medidasDiv)/2;

        jQuery('.slider-content').css('margin-left', intermedio);
        jQuery('.slider-content').children('.good').eq(Math.round((jQuery('.slider-content .good').length)/2)-1).addClass("central");
        alert(parseInt(jQuery('.central').attr('id')));
    },
    anterior: function(){
        var windowst = jQuery('.slider-content-container').width();
        var indice = Math.round((jQuery('.slider-content .good').length)/2);
        var intermedio;
        var actual = parseInt(jQuery('.central').attr('id'));
        var nuevo = actual-1;
        var intermediado =2*nuevo*medidasDiv;
        
        jQuery("#" + actual).removeClass('central');
        alert(actual);
        jQuery("#" + nuevo).addClass('central');
        intermedio = (windowst-intermediado-medidasDiv)/2;

        jQuery('.slider-content').css('margin-left', -intermedio);
    },
    next: function(){
        var windowst = jQuery('.slider-content-container').width();
        var indice = Math.round((jQuery('.slider-content .good').length)/2);
        var intermedio;
        var actual = parseInt(jQuery('.central').attr('id'));
        var nuevo = actual+1;
        var intermediado =2*nuevo*medidasDiv;
        
        jQuery("#" + actual).removeClass('central');
        alert(nuevo);
        jQuery("#" + nuevo).addClass('central');
        intermedio = (windowst-intermediado-medidasDiv)/2;
        alert(intermedio);
        jQuery('.slider-content').css('margin-left', intermedio);
    }
}
jQuery(function($){
    
carruselHome.constructor();
    $( ".prev" ).on( "click", function(event) {
      event.preventDefault();
      carruselHome.anterior();
    });
    $( ".next" ).on( "click", function(event) {
      event.preventDefault();
      carruselHome.next();
    });
});

