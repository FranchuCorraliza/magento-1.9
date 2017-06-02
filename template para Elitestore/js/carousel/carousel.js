/*
 * Author: francisco corraliza
 * Description: carrusel de productos en la home
*/

var carruselHome = {
    medidasDiv: 312,
    medidasDivGrande: 280,
    constructor: function(){
        var windowst = jQuery('.slider-content-container').width();
        var indice = Math.round((jQuery('.slider-content .good').length)/2)-1;
        var diferencia = 60;
        var intermedio;
        var intermediado =2*indice*this.medidasDiv+diferencia;

        intermedio = (windowst-intermediado-this.medidasDiv)/2;
        jQuery('.slider-content').css('margin-left', intermedio);
        jQuery('.slider-content').children('.good').eq(indice).addClass("central");
        jQuery("#" + indice).animate({width: '280'},1000);
        jQuery("#" + indice +" .good-name").show("slow");
    },
    anterior: function(){
        var windowst = jQuery('.slider-content-container').width();
        var indice = Math.round((jQuery('.slider-content .good').length)/2);
        var diferencia = 60;
        var intermedio;
        var actual = parseInt(jQuery('.central').attr('id'));
        var nuevo = actual-1;
        var intermediado =2*nuevo*this.medidasDiv+diferencia;
        if(nuevo>=0){
            if(nuevo<=3)
            {
                if(nuevo==3){
                    jQuery(".good:eq(4)").removeClass('central');
                    jQuery(".good:eq(3)").addClass('central');
                    intermedio = (windowst-intermediado-this.medidasDiv)/2;
                    jQuery('.slider-content').animate({marginLeft: intermedio},1000);
                    jQuery(".good:eq(4) .good-name").hide();
                    jQuery(".good:eq(4)").animate({width: '240'},1000);
                    jQuery(".good:eq(3)").animate({width: '280'},1000, function(){
                        jQuery(".good:eq(3) .good-name").show("slow", function(){
                            jQuery(".prev").removeClass('inaction');
                           });
                    });
                }
                if(nuevo==2){
                    jQuery(".good:eq(3)").removeClass('central');
                    jQuery(".good:eq(2)").addClass('central');
                    intermedio = (windowst-intermediado-this.medidasDiv)/2;
                    jQuery('.slider-content').animate({marginLeft: intermedio},1000);
                    jQuery(".good:eq(3) .good-name").hide();
                    jQuery(".good:eq(3)").animate({width: '240'},1000);
                    jQuery(".good:eq(2)").animate({width: '280'},1000, function(){
                        jQuery(".good:eq(2) .good-name").show("slow", function(){
                            jQuery(".prev").removeClass('inaction');
                           });
                    });
                }
                if(nuevo==1){
                    jQuery(".good:eq(2)").removeClass('central');
                    jQuery(".good:eq(1)").addClass('central');
                    intermedio = (windowst-intermediado-this.medidasDiv)/2;
                    jQuery('.slider-content').animate({marginLeft: intermedio},1000);
                    jQuery(".good:eq(2) .good-name").hide();
                    jQuery(".good:eq(2)").animate({width: '240'},1000);
                    jQuery(".good:eq(1)").animate({width: '280'},1000, function(){
                        jQuery(".good:eq(1) .good-name").show("slow", function(){
                            jQuery(".prev").removeClass('inaction');
                           });
                    });
                }
                if(nuevo==0){
                    jQuery(".good:eq(1)").removeClass('central');
                    jQuery(".good:eq(0)").addClass('central');
                    intermedio = (windowst-intermediado-this.medidasDiv)/2;
                    jQuery('.slider-content').animate({marginLeft: intermedio},1000);
                    jQuery(".good:eq(1) .good-name").hide();
                    jQuery(".good:eq(1)").animate({width: '240'},1000);
                    jQuery(".good:eq(0)").animate({width: '280'},1000, function(){
                        jQuery(".good:eq(0) .good-name").show("slow", function(){
                            jQuery(".prev").removeClass('inaction');
                           });
                    });
                }

            }
            else{
                jQuery("#" + actual).removeClass('central');
                jQuery("#" + nuevo).addClass('central');
                intermedio = (windowst-intermediado-this.medidasDiv)/2;
                jQuery('.slider-content').animate({marginLeft: intermedio},1000);
                jQuery("#" + actual +" .good-name").hide();
                jQuery("#" + actual).animate({width: '240'},1000);
                jQuery("#" + nuevo).animate({width: '280'},1000, function(){
                   jQuery("#" + nuevo +" .good-name").show("slow", function(){
                    jQuery(".prev").removeClass('inaction');
                   });
                });
            }
        }
    },
    next: function(){
        var windowst = jQuery('.slider-content-container').width();
        var indice = Math.round((jQuery('.slider-content .good').length)/2);
        var diferencia = 60;
        var intermedio;
        var actual = parseInt(jQuery('.central').attr('id'));
        var nuevo = actual+1;
        var intermediado =2*nuevo*this.medidasDiv+diferencia;
        if(nuevo<jQuery('.slider-content .good').length){
            if(nuevo<=3)
            {
                if(nuevo==3){
                    jQuery(".good:eq(2)").removeClass('central');
                    jQuery(".good:eq(3)").addClass('central');
                    intermedio = (windowst-intermediado-this.medidasDiv)/2;
                    jQuery('.slider-content').animate({marginLeft: intermedio},1000);
                    jQuery(".good:eq(2) .good-name").hide();
                    jQuery(".good:eq(2)").animate({width: '240'},1000);
                    jQuery(".good:eq(3)").animate({width: '280'},1000, function(){
                        jQuery(".good:eq(3) .good-name").show("slow", function(){
                            jQuery(".next").removeClass('inaction');
                           });
                    });
                }
                if(nuevo==2){
                    jQuery(".good:eq(1)").removeClass('central');
                    jQuery(".good:eq(2)").addClass('central');
                    intermedio = (windowst-intermediado-this.medidasDiv)/2;
                    jQuery('.slider-content').animate({marginLeft: intermedio},1000);
                    jQuery(".good:eq(1) .good-name").hide();
                    jQuery(".good:eq(1)").animate({width: '240'},1000);
                    jQuery(".good:eq(2)").animate({width: '280'},1000, function(){
                        jQuery(".good:eq(2) .good-name").show("slow", function(){
                            jQuery(".next").removeClass('inaction');
                           });
                    });
                }
                if(nuevo==1){
                    jQuery(".good:eq(0)").removeClass('central');
                    jQuery(".good:eq(1)").addClass('central');
                    intermedio = (windowst-intermediado-this.medidasDiv)/2;
                    jQuery('.slider-content').animate({marginLeft: intermedio},1000);
                    jQuery(".good:eq(0) .good-name").hide();
                    jQuery(".good:eq(0)").animate({width: '240'},1000);
                    jQuery(".good:eq(1)").animate({width: '280'},1000, function(){
                        jQuery(".good:eq(1) .good-name").show("slow", function(){
                            jQuery(".next").removeClass('inaction');
                           });
                    });
                }
                if(nuevo==0){
                    alert("cero");
                    jQuery(".good:eq(0)").removeClass('central');
                    jQuery(".good:eq(1)").addClass('central');
                    intermedio = (windowst-intermediado-this.medidasDiv)/2;
                    jQuery('.slider-content').animate({marginLeft: intermedio},1000);
                    jQuery(".good:eq(0) .good-name").hide();
                    jQuery(".good:eq(0)").animate({width: '240'},1000);
                    jQuery(".good:eq(1)").animate({width: '280'},1000, function(){
                        jQuery(".good:eq(1) .good-name").show("slow", function(){
                            jQuery(".next").removeClass('inaction');
                           });
                    });
                }

            }
            else{
                jQuery("#" + actual).removeClass('central');
                jQuery("#" + nuevo).addClass('central');
                intermedio = (windowst-intermediado-this.medidasDiv)/2;
                //jQuery('.slider-content').css('margin-left', intermedio);
                jQuery('.slider-content').animate({marginLeft: intermedio},1000);
                jQuery("#" + actual +" .good-name").hide();
                jQuery("#" + actual).animate({width: '240'},1000);
                jQuery("#" + nuevo).animate({width: '280'},1000, function(){
                    jQuery("#" + nuevo +" .good-name").show("slow", function(){
                            jQuery(".next").removeClass('inaction');
                           });
                });
            }
        }
    }
}
jQuery(function($){
    
carruselHome.constructor();
    $( ".prev" ).on( "click", function(event) {
      event.preventDefault();
      if($( ".prev" ).hasClass('inaction'))
      {

      }
      else{
        carruselHome.anterior();
        var contador = parseInt(jQuery('.central').attr('id'));

        if(contador>0){
            $( ".prev" ).addClass('inaction');
        }
      }
      
    });
    $( ".next" ).on( "click", function(event) {
      event.preventDefault();
      if($( ".next" ).hasClass('inaction'))
      {

      }
      else{
        carruselHome.next();
        var contador = parseInt(jQuery('.central').attr('id'));
        if(contador<$(".slider-content > div").length-1){
            $( ".next" ).addClass('inaction');
        }
      }
    });
});

