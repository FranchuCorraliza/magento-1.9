// Transformación Layer Navigation Catálogo
jQuery(document).ready(function($) {

	jQuery(document).on('click', ".filter--button", function(){
			$(".col-left-mobile").slideDown('slow', function(){
				$(".filter--button").addClass('open');
				$(".filter--button").removeClass('closed');
			});
	});

    jQuery(document).on('click', ".back__container--buttons", function(){
            $(".col-left-mobile").slideUp('slow', function(){
                $(".filter--button").removeClass('open');
                $(".filter--button").addClass('closed');
            });
    });

	jQuery(document).on('click', ".apply__container--buttons", function(){
			$(".col-left-mobile").slideUp('slow', function(){
				$(".filter--button").removeClass('open');
				$(".filter--button").addClass('closed');
			});
	});
	//abrir y cerrar el acordeon de categorias
	$("#filter-category-title").on("click", function(){
		if($(this).hasClass('filter-title-expanded'))
		{
			$("#filter-category-content").slideUp("slow");
			$(this).removeClass('filter-title-expanded');
			$(this).addClass('filter-title-collapsed');
		}
		else{
			$("#filter-category-content").slideDown("slow");
			$(this).removeClass('filter-title-collapsed');
			$(this).addClass('filter-title-expanded');
		}
			
	});

	//abrir y cerrar el acordeon de genero
	$("#filter-gender-title").on("click", function(){
		if($(this).hasClass('filter-title-expanded'))
		{
			$("#filter-gender-content").slideUp("slow");
			$(this).removeClass('filter-title-expanded');
			$(this).addClass('filter-title-collapsed');
		}
		else{
			$("#filter-gender-content").slideDown("slow");
			$(this).removeClass('filter-title-collapsed');
			$(this).addClass('filter-title-expanded');
		}
			
	});

	//abrir y cerrar el acordeon de A-Z
	$("#filter-alfabeto-title").on("click", function(){
		if($(this).hasClass('filter-title-expanded'))
		{
			$("#filter-alfabeto-content").slideUp("slow");
			$(this).removeClass('filter-title-expanded');
			$(this).addClass('filter-title-collapsed');
		}
		else{
			$("#filter-alfabeto-content").slideDown("slow");
			$(this).removeClass('filter-title-collapsed');
			$(this).addClass('filter-title-expanded');
		}
			
	});
	//aplicamos el filtro de genero
	$('.filters').on("click", function(){
		var genero="", categoria="", letra="", url;
		if(!$(this).hasClass('selected'))
		{
			$(this).parent().find('.selected').each(function(index, element){
				$(element).removeClass('selected');
			});
			$(this).addClass('selected');
		}
		else
		{
			$(this).removeClass('selected');
		}
		$('#filter-gender-content').find(".selected").each(function(index, element){
			genero = $(element).attr('gender');
		});
		$('#filter-category-content').find(".selected").each(function(index, element){
			categoria = $(element).attr('category');
		});
		$('#filter-alfabeto-content').find(".selected").each(function(index, element){
			letra = $(element).html();
		});
		url=MAGE_STORE_URL + "manufacturer/index/list/?gender="+genero+"&category"+categoria+"&letter="+letra;
		$('#loading-mask').show();
		$.ajax({
			type:'GET',
			url:url,
		})

		.done(function(msg){
			$('.col-main').html(msg);
			$('#loading-mask').hide();
		});
	});
	
	$(window).resize(function(){
		$('.image-logo').css({
               position:'relative',
               //left: ($(".imagen-manufacturer--logotipo").width() - $('.image-logo').outerWidth())/2,
               top: ($(".imagen-manufacturer--logotipo").height() - $('.image-logo').outerHeight())/2
          });
		
	});
	$(window).resize();
 
	
});
/*
jQuery(window).load(function() {
        $(window).resize(function(){
		$('.image-logo').css({
               position:'relative',
               //left: ($(".imagen-manufacturer--logotipo").width() - $('.image-logo').outerWidth())/2,
               top: ($(".imagen-manufacturer--logotipo").height() - $('.image-logo').outerHeight())/2
          });
		
	});
	$(window).resize();
});
*/	