	
		
    if(slide_on){
        jQuery(document).ready(function($) {
			$('.iosSlider2').iosSlider({
                desktopClickDrag: true,
                touchMoveThreshold:4,
                snapToChildren: true,
                infiniteSlider: true,
				autoSlide:false,
                autoSlideTimer:6000,
        		navSlideSelector: '.sliderNavi2 .naviItem2',                
                navNextSelector: '.iosSlider2 .next',
                navPrevSelector: '.iosSlider2 .prev',
        		onSlideChange: slideContentChange2,
        		onSlideComplete: slideContentComplete,
        		onSliderLoaded: slideContentLoaded
            }); 
		});
		jQuery(document).ready(function($) {
            $('.iosSlider').iosSlider({
                desktopClickDrag: true,
                touchMoveThreshold:4,
                snapToChildren: true,
                infiniteSlider: true,
				 autoSlide:false,
                autoSlideTimer:6000,
        		navSlideSelector: '.sliderNavi .naviItem',                
                navNextSelector: '.iosSlider .next',
                navPrevSelector: '.iosSlider .prev',
        		onSlideChange: slideContentChange,
        		onSlideComplete: slideContentComplete,
        		onSliderLoaded: slideContentLoaded
            });             	
        });
		
		jQuery(document).ready(function($) {
            $('.iosSlider3').iosSlider({
                desktopClickDrag: true,
                touchMoveThreshold:4,
                snapToChildren: true,
                infiniteSlider: true,
				 autoSlide:false,
                autoSlideTimer:6000,
        		navSlideSelector: '.sliderNavi3 .naviItem3',                
                navNextSelector: '.iosSlider3 .next',
                navPrevSelector: '.iosSlider3 .prev',
        		onSlideChange: slideContentChange3,
        		onSlideComplete: slideContentComplete,
        		onSliderLoaded: slideContentLoaded
            });             	
        });
		
		jQuery(document).ready(function($) {
            $('.iosSlider4').iosSlider({
                desktopClickDrag: true,
                touchMoveThreshold:4,
                snapToChildren: true,
                infiniteSlider: true,
				 autoSlide:false,
                autoSlideTimer:6000,
        		navSlideSelector: '.sliderNavi4 .naviItem4',                
                navNextSelector: '.iosSlider4 .next',
                navPrevSelector: '.iosSlider4 .prev',
        		onSlideChange: slideContentChange4,
        		onSlideComplete: slideContentComplete,
        		onSliderLoaded: slideContentLoaded
            });             	
        });
		
		jQuery(document).ready(function($) {
			
			$('.slide').hover(function(){
				jQuery('.boton-cool').stop(false, true).fadeIn('fast');
				
			},function(){
				jQuery('.boton-cool').stop(false, true).fadeOut('fast');
				
			});

/*			
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
	