var tiendas;

jQuery(document).ready(function($){
	jQuery('.tienda').on( "click", function(element) {//cambiar imagenes y texto (cambiar de tienda)al hacer click en cada pestaña de las tiendas
		if(!jQuery(this).hasClass('abierto'))
		{
			jQuery(this).removeClass('cerrado');
			jQuery(this).addClass('abierto');
		}
	  jQuery('.tienda').each(function( index ) {
		  jQuery('.abierto').addClass('cerrado');
		  jQuery('.abierto').removeClass('abierto');
		});
		if(!jQuery(this).hasClass('abierto'))
		{
			jQuery(this).removeClass('cerrado');
			jQuery(this).addClass('abierto');
		}
		if(jQuery(this).hasClass('woman'))
		{
			//Ajax ourstores -------------------------------
				jQuery('#loading-mask').show();
				jQuery.ajax({
					type: "POST",
					url:MAGE_STORE_URL+"/ajaxcontrol/index/ourstores",
					data : {baseUrl: MAGE_STORE_URL, tienda:'woman'},
				})
				.done(function(msg) {
						jQuery('#loading-mask').hide();
						woman = JSON.parse(msg);
						jQuery('.tiendasuperiorizq').html(woman.foto1);
						jQuery('#video').load();
						jQuery('.tiendasuperiorder').attr('src',woman.foto2);
						jQuery('.textoOur').html(woman.texto);
						divcolumnas = woman.divisor.split(";");
						contador=1;
						lastImage=0; 
						controlador=30;
						
							jQuery(".div1").html("");
							jQuery(".div2").html("");
							jQuery(".div3").html("");
							jQuery(".div4").html("");
							
								for(o=lastImage; o<divcolumnas.length; o++){
									if(o<controlador)
									{
										jQuery(".div" + contador).append(divcolumnas[o]);
										contador++;
										
										if(contador == 5)
										{
											contador = 1;
										}
										lastImage=o;
									}
								}
					  })
				.fail(function() {
					  })
				.always(function() {
					  });
		}
		if(jQuery(this).hasClass('men'))
		{
			//Ajax ourstores -------------------------------
				jQuery('#loading-mask').show();
				jQuery.ajax({
					type: "POST",
					url:MAGE_STORE_URL+"/ajaxcontrol/index/ourstores",
					data : {baseUrl: MAGE_STORE_URL, tienda:'men'},
				})
				.done(function(msg) {
						jQuery('#loading-mask').hide();
						men = JSON.parse(msg);
						
						jQuery('.tiendasuperiorizq').html(men.foto1);
						jQuery('#video').load();
						jQuery('.tiendasuperiorder').attr('src',men.foto2);
						jQuery('.textoOur').html(men.texto);
						divcolumnas = men.divisor.split(";");
						contador=1;
						lastImage=0; 
						controlador=30;
						
							jQuery(".div1").html("");
							jQuery(".div2").html("");
							jQuery(".div3").html("");
							jQuery(".div4").html("");
								for(o=lastImage; o<divcolumnas.length; o++){
									if(o<controlador)
									{
										jQuery(".div" + contador).append(divcolumnas[o]);
										contador++;
										
										if(contador == 5)
										{
											contador = 1;
										}
										lastImage=o;
									}
								}
					  })
				.fail(function() {
					  })
				.always(function() {0
					  });
		}
		if(jQuery(this).hasClass('accessories'))
		{
			//Ajax ourstores -------------------------------
				jQuery('#loading-mask').show();
				jQuery.ajax({
					type: "POST",
					url:MAGE_STORE_URL+"/ajaxcontrol/index/ourstores",
					data : {baseUrl: MAGE_STORE_URL, tienda:'accessories'},
				})
				.done(function(msg) {
						jQuery('#loading-mask').hide();
						accessories = JSON.parse(msg);
						jQuery('.tiendasuperiorizq').html(accessories.foto1);
						jQuery('#video').load();
						jQuery('.tiendasuperiorder').attr('src',accessories.foto2);
						jQuery('.textoOur').html(accessories.texto);
						divcolumnas = accessories.divisor.split(";");
						contador=1;
						lastImage=0; 
						controlador=30;
						
							jQuery(".div1").html("");
							jQuery(".div2").html("");
							jQuery(".div3").html("");
							jQuery(".div4").html("");
							
								for(o=lastImage; o<divcolumnas.length; o++){
									if(o<controlador)
									{
										jQuery(".div" + contador).append(divcolumnas[o]);
										contador++;
										
										if(contador == 5)
										{
											contador = 1;
										}
										lastImage=o;
									}
								}
					  })
				.fail(function() {
					  })
				.always(function() {
					  });
		}
	})
	$.event.add(window, "scroll", function() {//cuando hacemos scrol en la página para que cargue más imagenes
		
		var scroll = $('.collage').height(),
		p = $(window).scrollTop()+$(window).height();

		if((p)>scroll){
			controlador = controlador+30
			for(o=lastImage; o<divcolumnas.length; o++){
					if(o<controlador)
					{
						jQuery(".div" + contador).append(divcolumnas[o]);
						contador++;
						
						if(contador == 5)
						{
							contador = 1;
						}
						lastImage=o;
					}
				}
		}

	});
});

/*
Quitado a patición de "" para que no se hagan grandes las imagenes
jQuery(document).on( "click", '.imagen--mosaic', function(element) {
		jQuery(this).clone(true).lightbox_me({
					centered: true,
					zIndex:1,
					destroyOnClose:false,
					onLoad: function() {
						
					}
				});
	});*/