

jQuery(document).ready(function($){
	
// Transformacion Descripción y Videos de Catálogo
	var parrafo=[];
	jQuery(".products-grid .video").each(function(){
		var codigo;
		codigo = "<video style=\"max-width:100%; height:390px\" autoplay loop><source src=\"" + this.getAttribute('data-video') + ".mp4\" type=\"video/mp4\"><source src=\"" + this.getAttribute('data-video') + ".webm\" type=\"video/webm\"></video>";
 		this.innerHTML = codigo;
	});
	$(document).on("click", ".category--readmore", function(){
		if($(this).hasClass('closed'))
		{
			$(this).removeClass('closed');
			$(this).addClass('opened');
			$('.text--js').css("height", "auto");
            $(this).html($(this).attr('data-less'));
		}
		else
		{
			$(this).removeClass('opened');
			$(this).addClass('closed');
			$('.text--js').css("height", "3em");
			$(this).html($(this).attr('data-more'));
		}
	});

    $(".product-image").hover(function(){
    	if($(this).find('img').length>2)
		{
            $(this).find('.imagen--principal').css({'display':'none'});
            $(this).find('.imagen--back').css({'display':'block'});
		}
    }, function(){
        if($(this).find('img').length>2) {
            $(this).find('.imagen--principal').css({'display': 'block'});
            $(this).find('.imagen--back').css({'display': 'none'});
        }
    });

// Transformación Layer Navigation Catálogo

$(".col-left").stick_in_parent();

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
	jQuery(document).on('click', "#catalog--block--title--back", function(){
			$(".col-left-mobile").slideUp('slow', function(){
				$(".filter--button").removeClass('open');
				$(".filter--button").addClass('closed');
			});
	});
	
});

// Control desplegables del toolbar
function showSortBy(){
   	jQuery("#toolbar-dropdown1").show();
  }
 
function hideSortBy(){  
    jQuery("#toolbar-dropdown1").hide();
}


//Resaltar Elementos Wishlist

function resaltarRecuadro(id){
		
		if (jQuery("#product-"+id).hasClass('padre-deseado')){
			jQuery("#product-"+id).removeClass('padre-deseado');
		}else{
			jQuery("#product-"+id).addClass("padre-deseado");
		}
}
