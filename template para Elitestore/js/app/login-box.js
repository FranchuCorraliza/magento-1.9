(function($) {
	
	//Disparamos LightBox Login en cabecera y menu

	//fin disparador.
		
	$(document).ready(function() {
        //cargamos el bloque con ajax
        jQuery.ajax({
                url:   MAGE_STORE_URL+"ajaxcontrol/index/quicklogin",
                type:  'post',
                success:  function (response) {
                        jQuery('body').append(response);
                    }
            });
        //cuando hacemos click en el boton ver mi wishlist una vez logeado
        $(document).on('click', '.wislist--logged', function(e) {
            var url=MAGE_STORE_URL+"wishlistpanel/";
            location.href = url;            
        });
		// Opens a modal window when clicking the "My Account" link
		// If #login-modal exists, then open the lightbox
		// Refer to lightbox_me jQuery Plugin (http://buckwilson.me/lightboxme/) for settings and documentation
		//mostrar al hacer click en sign up
		//en version desktop
		$(document).on('click', '.login', function(e) {
			if($('#login-modal').length) {
					$('#error-mensaje').hide();
					$('#error-mensaje').html("");
					$('#signup-box').hide();
					//cambiar 

					if(!$('#login-modal').hasClass("claseSignin")){
						$('#login-modal').addClass("claseSignin");
						if($('#login-modal').hasClass('registrarse'))
						{
							$('#login-modal').removeClass('registrarse');
						}
					}

					//cambiar clase para responsibe
					$('#login-box').show();
					$('#forgot-box').hide();
					$('#login-modal-content').css('float', '');
					$('#login-modal-content').css('background', '');
					$('#login-modal-content').css('padding', '');
					$('#login-modal').css('width', '');
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
		//cuando hacemos click en el boton del wislist panel

		$(document).on('click', '.wishlist__footer--buttom button.register-top-title', function(e) {
			if($('#login-modal').length) {
					$('#error-mensaje').hide();
					$('#error-mensaje').html("");
					$('#signup-box').hide();
                    //cambiar 

                    if(!$('#login-modal').hasClass("claseSignin")){
                        $('#login-modal').addClass("claseSignin");
                        if($('#login-modal').hasClass('registrarse'))
                        {
                            $('#login-modal').removeClass('registrarse');
                        }
                    }

                    //cambiar clase para responsibee();

					$('#login-box').show();
					$('#forgot-box').hide();
					$('#login-modal-content').css('float', '');
					$('#login-modal-content').css('background', '');
					$('#login-modal-content').css('padding', '');
					$('#login-modal').css('width', '');
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
		//movil
		$('#signinmele').on('click', function(e){
			if($('#login-modal').length) {
					$('#error-mensaje').hide();
					$('#error-mensaje').html("");
					$('#signup-box').hide();
					$('#login-box').show();
					$('#forgot-box').hide();
					$('#login-modal-content').css('float', '');
					$('#login-modal-content').css('background', '');
					$('#login-modal-content').css('padding', '');
					$('#login-modal').css('width', '');
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
		
		$('.login-mobile-link').on('click', function(e){
			if($('#login-modal').length) {
					$('#error-mensaje').hide();
					$('#error-mensaje').html("");
					$('#signup-box').hide();
					$('#login-box').show();
					$('#forgot-box').hide();
					$('#login-modal-content').css('float', '');
					$('#login-modal-content').css('background', '');
					$('#login-modal-content').css('padding', '');
					$('#login-modal').css('width', '');
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
		
		/*
		$(document).on('click', '.register-top-title', function(e) {
			alert("register top tilte");
			if($('#login-modal').length) {
					$('#error-mensaje').hide();
					$('#error-mensaje').html("");
					$('#signup-box').hide();
					$('#login-box').show();
					$('#forgot-box').hide();
					$('#login-modal-content').css('float', '');
					$('#login-modal-content').css('background', '');
					$('#login-modal-content').css('padding', '');
					$('#login-modal').css('width', '');
				$('#login-modal').lightbox_me({
					centered: true,
					zIndex:1,
					onLoad: function() {
						
						initLoginBox();
					}
				});
 
				e.preventDefault();
			}
		});*/
		
		
		
		//mostra el registro al pulsar sobre el link del menu
		$(document).on('click', '.register', function(e) {
			if($('#login-modal').length) {
					$('#error-mensaje').hide();
					$('#error-mensaje').html("");
					//cambiar 

					if(!$('#login-modal').hasClass("registrarse")){
						$('#login-modal').addClass("registrarse");
						if($('#login-modal').hasClass('claseSignin'))
						{
							$('#login-modal').removeClass('claseSignin');
						}
					}

					//cambiar clase para responsibe
					$('#signup-box').show();
					$('#login-box').hide();
					$('#forgot-box').hide();
					$('#login-modal-content').css('float', 'left');
					$('#login-modal-content').css('background', 'none');
					$('#login-modal-content').css('padding', '0px');
					$('#login-modal').css('width', '639px');
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
		//movil
		$('#registermele').on('click', function(e) {
			if($('#login-modal').length) {
					$('#error-mensaje').hide();
					$('#error-mensaje').html("");
					$('#signup-box').show();
					$('#login-box').hide();
					$('#forgot-box').hide();
					$('#login-modal-content').css('float', 'left');
					$('#login-modal-content').css('background', 'none');
					$('#login-modal-content').css('padding', '0px');
					$('#login-modal').css('width', '639px');
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
		
		//no permitir el envio del forgot your-password si el email está vacio 
		/*
		$(document).on('keyup', '#email_address_forgot', function(e) {
			var email_regex = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
			
			if(email_regex.test($(this).val()))
			{
				$('#submitemail').prop("disabled",false);
			}
			else{
				$('#submitemail').prop("disabled",true);
			}
		});*/
	});
 
 	// Activates events on the login form & handles AJAX form posts with JSON data return
	function initLoginBox() {
		//para mostrar la pantalla de si estas registrado
		$('#already-registered-link').click(function(e) {
			$('#error-mensaje').hide();
			$('#error-mensaje').html("");
			$('#signup-box').hide();
			$('#login-box').show();
			$('#forgot-box').hide();
			$('#login-modal-content').css('float', '');
			$('#login-modal-content').css('background', '');
			$('#login-modal-content').css('padding', '');
			$('#login-modal').css('width', '');
			e.preventDefault();
		});
		//mostrar la pantalla para registrarse
		$('#need-account-link').click(function(e) {
			$('#login-modal').trigger('close');
			if($('#login-modal').length) {
					$('#error-mensaje').hide();
					$('#error-mensaje').html("");
					//cambiar 

					if(!$('#login-modal').hasClass("registrarse")){
						$('#login-modal').addClass("registrarse");
						if($('#login-modal').hasClass('claseSignin'))
						{
							$('#login-modal').removeClass('claseSignin');
						}
					}

					//cambiar clase para responsibe
					$('#signup-box').show();
					$('#login-box').hide();
					$('#forgot-box').hide();
					$('#login-modal-content').css('float', 'left');
					$('#login-modal-content').css('background', 'none');
					$('#login-modal-content').css('padding', '0px');
					$('#login-modal').css('width', '639px');
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
		//mostrar la pantalla de olvido su password
		$('#forgot-your-password').click(function(e) {
			$('#error-mensaje').hide();
			$('#error-mensaje').html("");
			$('#signup-box').hide();
			$('#login-box').hide();
			$('#forgot-box').show();
			$('#login-modal-content').css('float', '');
			$('#login-modal-content').css('background', '');
			$('#login-modal-content').css('padding', '');
			$('#login-modal').css('width', '');
			e.preventDefault();
		});
		//mostrar la pantalla de volver al login
		$('#back-to-login').click(function(e) {
			$('#error-mensaje').hide();
			$('#error-mensaje').html("");
			$('#signup-box').hide();
			$('#login-box').show();
			$('#forgot-box').hide();
			$('#login-modal-content').css('float', '');
			$('#login-modal-content').css('background', '');
			$('#login-modal-content').css('padding', '');
			$('#login-modal').css('width', '');
			e.preventDefault();
		});
		//recordamos al usuario si este está registrado
		$('#rememberme').click(function(e) {
					if ($('#rememberme').is(':checked')) 
					{
                        // guardamos el usuario y el password
                        localStorage.usrname = $('#email').val();
                        localStorage.pass = $('#pass').val();
                        localStorage.chkbx = $('#rememberme').val();
                    } else {
                        localStorage.usrname = '';
                        localStorage.pass = '';
                        localStorage.chkbx = '';
                    }
		});
		//recordamos al usuario si este está registrado
		$('#Mrs').click(function(e) {
					$('#Mrs').prop('checked',true);
					$('#Ms').prop('checked',false);
					$('#Mr').prop('checked',false);
		});
		$('#Mr').click(function(e) {
					$('#Mrs').prop('checked',false);
					$('#Ms').prop('checked',false);
					$('#Mr').prop('checked',true);
		});
		$('#Ms').click(function(e) {
					$('#Mrs').prop('checked',false);
					$('#Ms').prop('checked',true);
					$('#Mr').prop('checked',false);
		});
		//no permitimos el envio del formulario si no acepta los terminos y condiciones
		$('#termsandcondition').on('change',$('#termsandcondition'), function(e) {			
					if ($("#send2").hasClass('activo')) 
					{
						$('#send2').prop("disabled",true);
						$("#send2").removeClass('activo');
						alert(Translator.translate('You have to accept the terms and conditions'));
                    } 
					else{
						$('#send2').prop("disabled",false);
						$("#send2").addClass('activo');
					}
		});
		//realizar la accion deseada por ajax para el formulario de registro
		$('#signup-form').unbind().submit(function() {
			$('#error-mensaje').html("");//vacio los mensajes de error
			showProgressAnimation();//mostramos el circulo dando vueltas
			$.post($(this).attr('action'), $(this).serialize(), function(data) {
				
				if(!data.exceptions) {
					console.log('nos hemos registrado');
					$('#login-modal').trigger('close');
					/*updateLogin($('#login-form').attr('urllogin'));

					wishlist_panel = new Wishlist_Panel();
					ajaxcartproshow($('#login-form').attr('urlcart'));*/
					$('.col-main').prepend('<div class="std" id="messages-activity"><ul class="messages"><li class="success-msg"><ul><li><span>Account confirmation is required. Please, check your email for the confirmation link.</span></li></ul></li></ul></div>');
					setTimeout(function(){ $('#messages-activity').hide(); }, 6000);
					hideProgressAnimation();
				} else {
					hideProgressAnimation();
					for(var i = 0; i < data.exceptions.length; i++) {
						$('#error-mensaje').show();
						$('#error-mensaje').html($('#error-mensaje').html()+'<br/>'+data.exceptions[i]);
					}
				}
			}, 'json');
		});
		//funcion que llamamos cuando nos logamos
		$('#login-form').unbind().submit(function() {
			$('#error-mensaje').html("");
			showProgressAnimation();
			$.post($(this).attr('action'), $(this).serialize(), function(data) {
				if(!data.exceptions) {
					$('#login-modal').trigger('close');
					updateLogin($('#login-form').attr('urllogin'));
					wishlist_panel = new Wishlist_Panel();
					ajaxcartproshow($('#login-form').attr('urlcart'));
					$('.wishlist-login').html('View my wishlist');
					var url=MAGE_STORE_URL+"wishlistpanel/";
					$('.wishlist-login').attr("href",url);
		            $('.register-top-title').addClass('wislist--logged');
		            $('.register-top-title').removeClass('register-top-title');
					hideProgressAnimation();
				} else {
					hideProgressAnimation();
					for(var i = 0; i < data.exceptions.length; i++) {
						$('#error-mensaje').show();
						$('#error-mensaje').html($('#error-mensaje').html()+'<br/>'+data.exceptions[i]);
					}
				}
			}, 'json');
		});
		
		//llamada a la funcion ajax para cuando olvidamos el password
		$('#forgot-form').unbind().submit(function() {
			$('#error-mensaje').html("");
			showProgressAnimation();//mostramos la animacion
			$.post($(this).attr('action'), $(this).serialize(), function(data) {
				if(!data.exceptions) {
					hideProgressAnimation();
					$('#login-modal').trigger('close');
					//$('#error-mensaje').html(data.success);
					$('#forgot-box').hide();
					
				} else {
					hideProgressAnimation();
					for(var i = 0; i < data.exceptions.length; i++) {
						$('#error-mensaje').show();
						$('#error-mensaje').html($('#error-mensaje').html()+'<br/>'+data.exceptions[i]);
					}
				}
			}, 'json');
		});
	}
})(jQuery);

function updateLogin($url){
	jQuery.ajax({
        type: "POST",
        url:$url,
		data : {baseUrl: MAGE_STORE_URL},
    })
          .done(function(msg) {
            jQuery("#header-login-trigger-cabecera").html(msg);
			jQuery('.login--mobile--content').html(msg);
          })
          .fail(function() {
          })
          .always(function() {
          });
}