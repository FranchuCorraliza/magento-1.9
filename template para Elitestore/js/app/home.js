var hijo=0;

jQuery(document).ready(function($){
//Transformador de cabecera -------------------------------

	var div = $('.header--content--right');
	var logo = $('.logo');
	var start = $(div).offset().top;
	
	var interval = setInterval(increment,3000);  

	$.event.add(window, "scroll", function() {
		var p = $(window).scrollTop();
		$('.nav-container').css('background',((p)>start) ? 'rgb(255,255,255)' : 'rgba(255,255,255,0)');
		$('#nav li a').css('color',((p)>start) ? 'rgb(0,0,0)' : 'rgb(255,255,255)');
		$('.sheet ul li a').css('color',((p)>start) ? 'rgb(255,255,255)' : 'rgb(255,255,255)');
		
	});

});

function increment(){
	if(hijo==0)
	{
		jQuery(".texto--banner ul").find("#0").fadeIn("slow");
		hijo=hijo+1;
	}
	else if(hijo>2)
	{
		jQuery(".texto--banner ul").find("#2").fadeOut("slow");
		jQuery(".texto--banner ul").find("#0").fadeIn("slow");
		hijo=1;
	}
	else
	{
		hijo=hijo-1;
		jQuery(".texto--banner ul").find("#" + hijo).fadeOut("slow");
		hijo=hijo+1;
		jQuery(".texto--banner ul").find("#" + hijo).fadeIn("slow");
		hijo=hijo+1;
		
	}	
         
}