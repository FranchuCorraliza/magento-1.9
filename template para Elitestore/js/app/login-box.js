
(function($) {
	
	//Disparamos LightBox Login en cabecera y menu

	//fin disparador.
		
	$(document).ready(function() {
		// Opens a modal window when clicking the "My Account" link
		// If #login-modal exists, then open the lightbox
		// Refer to lightbox_me jQuery Plugin (http://buckwilson.me/lightboxme/) for settings and documentation
		$(document).on('click', '.login', function(e) {
			if($('#login-modal').length) {
					$('#signup-box').hide();
					$('#login-box').show();
					$('#forgot-box').hide();
					$('.sabana-login-modal').show();
					$('#login-modal').css('width', '305');
					$('#login-modal').show();
					
				/*	
				codigo antiguo para hacer un popup 
				$('#login-modal').lightbox_me({
					centered: true,
					zIndex:1,
					onLoad: function() {
						
						initLoginBox();
					}
				});*/
 
				e.preventDefault();
			}
		});
		//mostra el registro al pulsar sobre el link del menu
		$(document).on('click', '.register', function(e) {
			if($('#login-modal').length) {
					$('#signup-box').show();
					$('#login-box').hide();
					$('#forgot-box').hide();
					$('.sabana-login-modal').show();
						$('#login-modal').css('width', '716');
						$('#login-modal').show();
				
				/*
				$('#login-modal').lightbox_me({
					centered: true,
					zIndex:1,
					onLoad: function() {
						
						initLoginBox();
					}
				});*/
 
				e.preventDefault();
			}
		});
		
		//cerrar las ventanas al hacer click en close
		$(document).on('click', '#close_x', function(e) {
					if($('#login-modal').length) {
							$('#signup-box').hide();
							$('#login-box').hide();
							$('#forgot-box').hide();
							$('.sabana-login-modal').hide();
							$('#login-modal').hide();
								
						e.preventDefault();
					}
				});
/*
		$(document).on('click', '.bloquecuarto', function(e) {
			if($('#login-modal').length) {
				$('#login-modal').lightbox_me({
					centered: true,
					zIndex:1,
					onLoad: function() {
						
						initLoginBox();
					}
				});
 
				e.preventDefault();
			}
		});
		*/
	});
 
 	// Activates events on the login form & handles AJAX form posts with JSON data return
	//function initLoginBox() {
		$('#already-registered-link').click(function(e) {
			$('#signup-box').hide();
			$('#login-box').show();
			$('#forgot-box').hide();
			$('#login-modal').css('width', '305');
			e.preventDefault();
		});
 
		$('#need-account-link').click(function(e) {
			$('#signup-box').show();
			$('#login-box').hide();
			$('#forgot-box').hide();
			$('#login-modal').css('width', '716');
			e.preventDefault();
		});
		
		$('#already-registered-link').click(function(e) {
			$('#signup-box').hide();
			$('#login-box').show();
			$('#forgot-box').hide();
			$('#login-modal').css('width', '305');
			e.preventDefault();
		});
		
		$('#forgot-your-password').on('click', function() {
			/*$('#signup-box').hide();
			$('#login-box').hide();
			$('#forgot-box').show();
			$('#login-modal').css('width', '305');*/
			alert('hola');
		});
		
		$('#back-to-login').click(function(e) {
			$('#signup-box').hide();
			$('#login-box').show();
			$('#forgot-box').hide();
			e.preventDefault();
		});
		
		
		
		
 
		$('#signup-form').unbind().submit(function() {
			$('#error-mensaje').html("");
			showProgressAnimation();
			$.post($(this).attr('action'), $(this).serialize(), function(data) {
				
				if(!data.exceptions) {
					$('#login-modal').trigger('close');
					alert($('#login-form').attr('urllogin'));
					updateLogin($('#login-form').attr('urllogin'));
					wishlist_panel = new Wishlist_Panel();
					ajaxcartproshow($('#login-form').attr('urlcart'));
					hideProgressAnimation();
				} else {
					hideProgressAnimation();
					for(var i = 0; i < data.exceptions.length; i++) {
						$('#error-mensaje').html($('#error-mensaje').html()+'<br/>'+data.exceptions[i]);
					}
				}
			}, 'json');
		});
 
		$('#login-form').unbind().submit(function() {
			$('#error-mensaje').html("");
			showProgressAnimation();
			$.post($(this).attr('action'), $(this).serialize(), function(data) {
				if(!data.exceptions) {
					$('#login-modal').trigger('close');
					updateLogin($('#login-form').attr('urllogin'));
					wishlist_panel = new Wishlist_Panel();
					ajaxcartproshow($('#login-form').attr('urlcart'));

					hideProgressAnimation();
				} else {
					hideProgressAnimation();
					for(var i = 0; i < data.exceptions.length; i++) {
						$('#error-mensaje').html($('#error-mensaje').html()+'<br/>'+data.exceptions[i]);
					}
				}
			}, 'json');
		});
		
		
		$('#forgot-form').unbind().submit(function() {
			$('#error-mensaje').html("");
			showProgressAnimation();
			$.post($(this).attr('action'), $(this).serialize(), function(data) {
				if(!data.exceptions) {
					hideProgressAnimation();
					$('#error-mensaje').html(data.success);
					
				} else {
					hideProgressAnimation();
					for(var i = 0; i < data.exceptions.length; i++) {
						$('#error-mensaje').html($('#error-mensaje').html()+'<br/>'+data.exceptions[i]);
					}
				}
			}, 'json');
		});
	//}
})(jQuery);

function updateLogin($url){
	jQuery.ajax({
        type: "POST",
        url:$url,
		data : {baseUrl: MAGE_STORE_URL},
    })
          .done(function(msg) {
            jQuery("#header-login-trigger-cabecera").html(msg);
          })
          .fail(function() {
          })
          .always(function() {
          });
}