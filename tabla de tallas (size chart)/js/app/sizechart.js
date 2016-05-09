jQuery(document).ready(function($) {
    jQuery("td").hover(
        function(){
            jQuery(this).parent('tr').addClass('selected');
        }, function(){
            jQuery(this).parent('tr').removeClass('selected');
        }
    );
	
	jQuery('#link-sizechart').on("click",function(event){
		event.preventDefault();
        event.stopPropagation();
		jQuery('.sizechart-background').show();
		$(window).resize();
	});
	
	jQuery('.sizechart .border .content .close').on("click",function(){
		jQuery('.sizechart-background').hide();
		
	});
	
	jQuery('.sizechart-background').on("click",function(){
		
	});
 
    jQuery(window).resize(function(){
     
              // aquí le pasamos la clase o id de nuestro div a centrar (en este caso "caja")
			  console.log((jQuery(window).width() - jQuery('.sizechart').outerWidth())/2);
			  console.log((jQuery(window).height() - jQuery('.sizechart').outerHeight())/2);
              jQuery('.sizechart').css({
                   position:'absolute',
                   left: (jQuery(window).width() - jQuery('.sizechart').outerWidth())/2,
                   top: (jQuery(window).height() - jQuery('.sizechart').outerHeight())/2
              });
            
        });
     
    // Ejecutamos la función
    
});
//sizechart-background