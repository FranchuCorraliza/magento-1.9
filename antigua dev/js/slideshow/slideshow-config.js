	
		
    if(slide_on){
		jQuery(document).ready(function($) {
			jQuery('div#iosSlider').iosSlider({
                desktopClickDrag: true,
                touchMoveThreshold:4,
                snapToChildren: true,
                infiniteSlider: true,
				autoSlide:true,
                autoSlideTimer:6000,
        		navSlideSelector: 'div#iosSlider .sliderNavi .naviItem',                
                navNextSelector: 'div#iosSlider .next',
                navPrevSelector: 'div#iosSlider .prev',
        		onSlideChange: slideContentChange,
        		onSlideComplete: slideContentComplete,
        		onSliderLoaded: slideContentLoaded
            }); 
		});
        jQuery(document).ready(function($) {
			jQuery('div#iosSlider2').iosSlider({
                desktopClickDrag: true,
                touchMoveThreshold:4,
                snapToChildren: true,
                infiniteSlider: true,
				autoSlide:false,
                autoSlideTimer:6000,
        		navSlideSelector: 'div#iosSlider2 .sliderNavi .naviItem',                
                navNextSelector: 'div#iosSlider2 .next',
                navPrevSelector: 'div#iosSlider2 .prev',
        		onSlideChange: slideContentChange2,
        		onSlideComplete: slideContentComplete,
        		onSliderLoaded: slideContentLoaded
            }); 
		});
		jQuery(document).ready(function($) {
            jQuery('div#iosSlider3').iosSlider({
                desktopClickDrag: true,
                touchMoveThreshold:4,
                snapToChildren: true,
                infiniteSlider: true,
				 autoSlide:false,
                autoSlideTimer:6000,
        		navSlideSelector: 'div#iosSlider3 .sliderNavi .naviItem',                
                navNextSelector: 'div#iosSlider3 .next',
                navPrevSelector: 'div#iosSlider3 .prev',
        		onSlideChange: slideContentChange3,
        		onSlideComplete: slideContentComplete,
        		onSliderLoaded: slideContentLoaded
            });             	
        });
		
		jQuery(document).ready(function($) {
            jQuery('div#iosSlider4').iosSlider({
                desktopClickDrag: true,
                touchMoveThreshold:4,
                snapToChildren: true,
                infiniteSlider: true,
				 autoSlide:false,
                autoSlideTimer:6000,
        		navSlideSelector: 'div#iosSlider4 .sliderNavi .naviItem',                
                navNextSelector: 'div#iosSlider4 .next',
                navPrevSelector: 'div#iosSlider4 .prev',
        		onSlideChange: slideContentChange4,
        		onSlideComplete: slideContentComplete,
        		onSliderLoaded: slideContentLoaded
            });             	
        });
		
		
		jQuery(document).ready(function($) {
/*			
			$('.slide').hover(function(){
				jQuery('.boton-cool').stop(false, true).fadeIn('fast');
				
			},function(){
				jQuery('.boton-cool').stop(false, true).fadeOut('fast');
				
			});

			
			$('.slide').mouseover(function(){
				jQuery('.boton-cool').fadeIn('fast');
				jQuery('.naviItem3').fadeIn('fast');
				jQuery('.next').fadeIn('fast');
				jQuery('.prev').fadeIn('fast');
				
			});
			$('.slide').mouseout(function(){
				jQuery('.boton-cool').fadeOut('fast');
				jQuery('.naviItem3').fadeOut('fast');
				jQuery('.next').fadeOut('fast');
				jQuery('.prev').fadeOut('fast');
				
			});
*/
			$('.boton-cool').click(function(){
				var boton = $(this).attr("id");
				var res = boton.substring(11);
				var dialog = 'div#dialog-cool-' + res;
				jQuery('.fondo-cool').fadeIn('slow');
				jQuery(dialog).fadeIn('slow');
				
			});
			$('.cerrar-cool').click(function(){
				jQuery('.fondo-cool').fadeOut('slow');
				jQuery('.dialog-cool').fadeOut('slow');
			});
			$('.fondo-cool').click(function(){
				jQuery('.fondo-cool').fadeOut('slow');
				jQuery('.dialog-cool').fadeOut('slow');
			});

			
});
    }
	