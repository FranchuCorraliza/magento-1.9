/*
 * Author: francisco corraliza
 * Description: carrusel de productos en la home
*/

var carruselHome = {
    medidasDiv: 240,
    constructor: function(){
        var windowst = jQuery('.slider-content-container').width();
        var indice = Math.round((jQuery('.slider-content .good').length)/2)-1;
        var intermedio;
        var intermediado =2*indice*this.medidasDiv;

        intermedio = (windowst-intermediado-this.medidasDiv)/2;
        jQuery('.slider-content').css('margin-left', intermedio);
        jQuery('.slider-content').children('.good').eq(indice).addClass("central");
        jQuery("#" + indice).animate({width: '300'},1000);
    },
    anterior: function(){
        var windowst = jQuery('.slider-content-container').width();
        var indice = Math.round((jQuery('.slider-content .good').length)/2);
        var intermedio;
        var actual = parseInt(jQuery('.central').attr('id'));
        var nuevo = actual-1;
        var intermediado =2*nuevo*this.medidasDiv;
        if(nuevo>=0){
            
            jQuery("#" + actual).removeClass('central');
            alert(actual);
            jQuery("#" + nuevo).addClass('central');
            alert(nuevo);
            intermedio = (windowst-intermediado-this.medidasDiv)/2;
            alert(intermedio);
            jQuery('.slider-content').animate({marginLeft: intermedio},1000);
            jQuery("#" + actual).animate({width: '240'},1000);
            jQuery("#" + nuevo).animate({width: '300'},1000);
        }
    },
    next: function(){
        var windowst = jQuery('.slider-content-container').width();
        var indice = Math.round((jQuery('.slider-content .good').length)/2);
        var intermedio;
        var actual = parseInt(jQuery('.central').attr('id'));
        var nuevo = actual+1;
        var intermediado =2*nuevo*this.medidasDiv;
        if(nuevo<jQuery('.slider-content .good').length){
            jQuery("#" + actual).removeClass('central');
            jQuery("#" + nuevo).addClass('central');
            intermedio = (windowst-intermediado-this.medidasDiv)/2;
            //jQuery('.slider-content').css('margin-left', intermedio);
            jQuery('.slider-content').animate({marginLeft: intermedio},1000);
            jQuery("#" + actual).animate({width: '240'},1000);
            jQuery("#" + nuevo).animate({width: '300'},1000);
        }
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

