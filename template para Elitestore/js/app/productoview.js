jQuery(document).on('ready',function($){
	
	jQuery('.more-views ul li').on('click', function(){
			event.preventDefault();
			jQuery('#image').attr('src', jQuery(this).attr('url'));
			
		});


		//<![CDATA[
        var productAddToCartForm = new VarienForm('product_addtocart_form');
        productAddToCartForm.submit = function(button, url) {
            if (this.validator.validate()) {
                var form = this.form;
                var oldUrl = form.action;

                if (url) {
                   form.action = url;
                }
                var e = null;
                try {
                    this.form.submit();
                } catch (e) {
                }
                this.form.action = oldUrl;
                if (e) {
                    throw e;
                }

                if (button && button != 'undefined') {
                    button.disabled = true;
                }
            }
        }.bind(productAddToCartForm);

        productAddToCartForm.submitLight = function(button, url){
            if(this.validator) {
                var nv = Validation.methods;
                delete Validation.methods['required-entry'];
                delete Validation.methods['validate-one-required'];
                delete Validation.methods['validate-one-required-by-name'];
                // Remove custom datetime validators
                for (var methodName in Validation.methods) {
                    if (methodName.match(/^validate-datetime-.*/i)) {
                        delete Validation.methods[methodName];
                    }
                }

                if (this.validator.validate()) {
                    if (url) {
                        this.form.action = url;
                    }
                    this.form.submit();
                }
                Object.extend(Validation.methods, nv);
            }
        }.bind(productAddToCartForm);
    //]]>
	/************** Actualizar botón Wishlist ***************************/
	//Cambia  Wishlist button-----------------------
	//	jQuery('.bindRemove button span').html('Remove to Wishlist');		

	/************** Order by request popup ******************************/
	jQuery('#product-orderbyrequest-button').on('click',function(event){
			event.preventDefault();
			event.stopPropagation();
			var param=jQuery('#product_addtocart_form').serializeArray();
			var talla='';
			param.forEach(function(element){
				if(element['name']=='super_attribute[133]'){
					talla=element['value'];
				}
			});
			if (talla==''){
				jQuery('#attribute133').after("<div class='validation-advice'>This is a required field.</div>");
			}else{
				var login=jQuery('#header-login-trigger-cabecera div').hasClass('account');
				if (!login){
					//Cargar Login box
					if(jQuery('#login-modal').length) {
						jQuery('#error-mensaje').hide();
						jQuery('#error-mensaje').html("");
						jQuery('#signup-box').hide();
						//cambiar 

						if(!jQuery('#login-modal').hasClass("claseSignin")){
							jQuery('#login-modal').addClass("claseSignin");
							if(jQuery('#login-modal').hasClass('registrarse'))
							{
								jQuery('#login-modal').removeClass('registrarse');
							}
						}

						//cambiar clase para responsibe
						jQuery('#login-box').show();
						jQuery('#forgot-box').hide();
						jQuery('#login-modal-content').css('float', '');
						jQuery('#login-modal-content').css('background', '');
						jQuery('#login-modal-content').css('padding', '');
						jQuery('#login-modal').css('width', '');
						jQuery('#login-modal').lightbox_me({
							centered: true,
							zIndex:1,
							onLoad: function() {
								
								initLoginBox();
							}
						});		 
						event.preventDefault();
					}
				}else{
					jQuery(".modalwindow .border .content").html("<div class='close'>&#62134;</div><div class='loading'><span id='floatingCirclesG-content' class='frame sprite'></span><br/>Loading...</div>");
					jQuery('.modalwindow').lightbox_me({
							centered: true,
							zIndex:1,
							onLoad: function() {
								jQuery('.modalwindow').show();				}
						});
					var url=jQuery(this).attr('url');
					jQuery.ajax({
						type: "POST",
						url:url,
						data:{talla: talla},
					})
						.done(function(msg) {
						jQuery(".modalwindow .border .content").html(msg);
						})
						.fail(function() {
					  })
						.always(function() {
					  });
				}
			}
		});
	/************** Boton Size Guide ***********************************/
	jQuery('#link-sizechart').on("click",function(event){
		event.preventDefault();
        event.stopPropagation();
		jQuery(".modalwindow .border .content").html("<div class='close'>&#62134;</div><div class='loading'><span id='floatingCirclesG-content' class='frame sprite'></span><br/>Loading...</div>");
		jQuery('.modalwindow').lightbox_me({
					centered: true,
					zIndex:1,
					onLoad: function() {
						jQuery('.modalwindow').show();				}
				});
			
		var url=jQuery(this).attr('href');
		jQuery.ajax({
			type: "POST",
			url:url,
			data : {baseUrl: MAGE_STORE_URL},
		})
			.done(function(msg) {
            jQuery(".modalwindow .border .content").html(msg);
			})
			.fail(function() {
          })
			.always(function() {
          });
	});
	

	
	/************* Ventana Product Contact Form **************************/
	jQuery('.productcontact-link').on('click', function(event){
		event.preventDefault();
        event.stopPropagation();
		var login=jQuery('#header-login-trigger-cabecera div').hasClass('account');
		if (!login){
			//Cargar Login box
			if(jQuery('#login-modal').length) {
				jQuery('#error-mensaje').hide();
				jQuery('#error-mensaje').html("");
				jQuery('#signup-box').hide();
				//cambiar 

				if(!jQuery('#login-modal').hasClass("claseSignin")){
					jQuery('#login-modal').addClass("claseSignin");
					if(jQuery('#login-modal').hasClass('registrarse'))
					{
						jQuery('#login-modal').removeClass('registrarse');
					}
				}

				//cambiar clase para responsibe
				jQuery('#login-box').show();
				jQuery('#forgot-box').hide();
				jQuery('#login-modal-content').css('float', '');
				jQuery('#login-modal-content').css('background', '');
				jQuery('#login-modal-content').css('padding', '');
				jQuery('#login-modal').css('width', '');
				jQuery('#login-modal').lightbox_me({
					centered: true,
					zIndex:1,
					onLoad: function() {
						
						initLoginBox();
					}
				});		 
				event.preventDefault();
			}
		}else{
				jQuery(".modalwindow .border .content").html("<div class='close'>&#62134;</div><div class='loading'><span id='floatingCirclesG-content' class='frame sprite'></span><br/>Loading...</div>");
				jQuery('.modalwindow').lightbox_me({
							centered: true,
							zIndex:1,
							onLoad: function() {
								jQuery('.modalwindow').show();				}
						});
					
				var url=jQuery(this).attr('href');
				jQuery.ajax({
					type: "POST",
					url:url,
					data : {baseUrl: MAGE_STORE_URL},
				})
					.done(function(msg) {
					jQuery(".modalwindow .border .content").html(msg);
					})
					.fail(function() {
				  })
					.always(function() {
				  });
		}
		
	});
	
	/*********************************************************************/
	
	/************* Ventana Share Via Email Form **************************/
	jQuery('#share-via-email').on('click', function(event){
		event.preventDefault();
        event.stopPropagation();
		var login=jQuery('#header-login-trigger-cabecera div').hasClass('account');
		if (!login){
			//Cargar Login box
			if(jQuery('#login-modal').length) {
				jQuery('#error-mensaje').hide();
				jQuery('#error-mensaje').html("");
				jQuery('#signup-box').hide();
				//cambiar 

				if(!jQuery('#login-modal').hasClass("claseSignin")){
					jQuery('#login-modal').addClass("claseSignin");
					if(jQuery('#login-modal').hasClass('registrarse'))
					{
						jQuery('#login-modal').removeClass('registrarse');
					}
				}

				//cambiar clase para responsibe
				jQuery('#login-box').show();
				jQuery('#forgot-box').hide();
				jQuery('#login-modal-content').css('float', '');
				jQuery('#login-modal-content').css('background', '');
				jQuery('#login-modal-content').css('padding', '');
				jQuery('#login-modal').css('width', '');
				jQuery('#login-modal').lightbox_me({
					centered: true,
					zIndex:1,
					onLoad: function() {
						
						initLoginBox();
					}
				});		 
				event.preventDefault();
			}
		}else{
			jQuery(".modalwindow .border .content").html("<div class='close'>&#62134;</div><div class='loading'><span id='floatingCirclesG-content' class='frame sprite'></span><br/>Loading...</div>");
			jQuery('.modalwindow').lightbox_me({
						centered: true,
						zIndex:1,
						onLoad: function() {
							jQuery('.modalwindow').show();				}
					});
				
			var url=jQuery(this).attr('href');
			jQuery.ajax({
				type: "POST",
				url:url,
				data : {baseUrl: MAGE_STORE_URL},
			})
				.done(function(msg) {
				jQuery(".modalwindow .border .content").html(msg);
				})
				.fail(function() {
			  })
				.always(function() {
			  });
		}
	});
	
	/*********************************************************************/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
    var producto = jQuery('.product-view').attr("id");

     jQuery.ajax({
        type: "POST",
        url:MAGE_STORE_URL+"/ajaxcontrol/index/estilismo/",
        data: { productoSku: producto}
    })
          .done(function(msg) {
            jQuery("#estilismo").html(msg);
          })
          .fail(function() {
          })
          .always(function() {
          });

    jQuery.ajax({
        type: "POST",
        url:MAGE_STORE_URL+"/ajaxcontrol/index/sugerencias/",
        data: { productoSku: producto}
    })
          .done(function(msg) {
            jQuery("#more-products").html(msg);
          })
          .fail(function() {
          })
          .always(function() {
          });
});

function activarSugerencias(este){
		var selector = ".sugerencias-nav li";
		jQuery(selector).removeClass('active');
		jQuery(este).addClass('active');
		jQuery("#contenido-sugerencias-relacionadas").hide();
			jQuery("#contenido-sugerencias-recientes").hide();
			if (jQuery(este).attr('activar')=="contenido-sugerencias-relacionadas"){
				jQuery("#contenido-sugerencias-relacionadas").show();
			}else if (jQuery(este).attr('activar')=="contenido-sugerencias-recientes"){
				jQuery("#contenido-sugerencias-recientes").show(); 
			}
		
		jQuery( ".listado-sugerencias li" ).last().addClass( "last" );
}

function initLoginBox() {
		//para mostrar la pantalla de si estas registrado
		jQuery('#already-registered-link').click(function(e) {
			jQuery('#error-mensaje').hide();
			jQuery('#error-mensaje').html("");
			jQuery('#signup-box').hide();
			jQuery('#login-box').show();
			jQuery('#forgot-box').hide();
			jQuery('#login-modal-content').css('float', '');
			jQuery('#login-modal-content').css('background', '');
			jQuery('#login-modal-content').css('padding', '');
			jQuery('#login-modal').css('width', '');
			e.preventDefault();
		});
		//mostrar la pantalla para registrarse
		jQuery('#need-account-link').click(function(e) {
			jQuery('#login-modal').trigger('close');
			if(jQuery('#login-modal').length) {
					jQuery('#error-mensaje').hide();
					jQuery('#error-mensaje').html("");
					//cambiar 

					if(!jQuery('#login-modal').hasClass("registrarse")){
						jQuery('#login-modal').addClass("registrarse");
						if(jQuery('#login-modal').hasClass('claseSignin'))
						{
							jQuery('#login-modal').removeClass('claseSignin');
						}
					}

					//cambiar clase para responsibe
					jQuery('#signup-box').show();
					jQuery('#login-box').hide();
					jQuery('#forgot-box').hide();
					jQuery('#login-modal-content').css('float', 'left');
					jQuery('#login-modal-content').css('background', 'none');
					jQuery('#login-modal-content').css('padding', '0px');
					jQuery('#login-modal').css('width', '639px');
				jQuery('#login-modal').lightbox_me({
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
		jQuery('#forgot-your-password').click(function(e) {
			jQuery('#error-mensaje').hide();
			jQuery('#error-mensaje').html("");
			jQuery('#signup-box').hide();
			jQuery('#login-box').hide();
			jQuery('#forgot-box').show();
			jQuery('#login-modal-content').css('float', '');
			jQuery('#login-modal-content').css('background', '');
			jQuery('#login-modal-content').css('padding', '');
			jQuery('#login-modal').css('width', '');
			e.preventDefault();
		});
		//mostrar la pantalla de volver al login
		jQuery('#back-to-login').click(function(e) {
			jQuery('#error-mensaje').hide();
			jQuery('#error-mensaje').html("");
			jQuery('#signup-box').hide();
			jQuery('#login-box').show();
			jQuery('#forgot-box').hide();
			jQuery('#login-modal-content').css('float', '');
			jQuery('#login-modal-content').css('background', '');
			jQuery('#login-modal-content').css('padding', '');
			jQuery('#login-modal').css('width', '');
			e.preventDefault();
		});
		//recordamos al usuario si este está registrado
		jQuery('#rememberme').click(function(e) {
					if (jQuery('#rememberme').is(':checked')) 
					{
                        // guardamos el usuario y el password
                        localStorage.usrname = jQuery('#email').val();
                        localStorage.pass = jQuery('#pass').val();
                        localStorage.chkbx = jQuery('#rememberme').val();
                    } else {
                        localStorage.usrname = '';
                        localStorage.pass = '';
                        localStorage.chkbx = '';
                    }
		});
		//recordamos al usuario si este está registrado
		jQuery('#Mrs').click(function(e) {
					jQuery('#Mrs').prop('checked',true);
					jQuery('#Ms').prop('checked',false);
					jQuery('#Mr').prop('checked',false);
		});
		jQuery('#Mr').click(function(e) {
					jQuery('#Mrs').prop('checked',false);
					jQuery('#Ms').prop('checked',false);
					jQuery('#Mr').prop('checked',true);
		});
		jQuery('#Ms').click(function(e) {
					jQuery('#Mrs').prop('checked',false);
					jQuery('#Ms').prop('checked',true);
					jQuery('#Mr').prop('checked',false);
		});
		//no permitimos el envio del formulario si no acepta los terminos y condiciones
		jQuery('#termsandcondition').on('change',jQuery('#termsandcondition'), function(e) {			
					if (jQuery("#send2").hasClass('activo')) 
					{
						jQuery('#send2').prop("disabled",true);
						jQuery("#send2").removeClass('activo');
						alert(Translator.translate('You have to accept the terms and conditions'));
                    } 
					else{
						jQuery('#send2').prop("disabled",false);
						jQuery("#send2").addClass('activo');
					}
		});
		//realizar la accion deseada por ajax para el formulario de registro
		jQuery('#signup-form').unbind().submit(function() {
			jQuery('#error-mensaje').html("");//vacio los mensajes de error
			showProgressAnimation();//mostramos el circulo dando vueltas
			jQuery.post(jQuery(this).attr('action'), jQuery(this).serialize(), function(data) {
				
				if(!data.exceptions) {
					console.log('nos hemos registrado');
					jQuery('#login-modal').trigger('close');
					/*updateLogin(jQuery('#login-form').attr('urllogin'));

					wishlist_panel = new Wishlist_Panel();
					ajaxcartproshow(jQuery('#login-form').attr('urlcart'));*/
					jQuery('.col-main').prepend('<div class="std" id="messages-activity"><ul class="messages"><li class="success-msg"><ul><li><span>Account confirmation is required. Please, check your email for the confirmation link.</span></li></ul></li></ul></div>');
					setTimeout(function(){ jQuery('#messages-activity').hide(); }, 6000);
					hideProgressAnimation();
				} else {
					hideProgressAnimation();
					for(var i = 0; i < data.exceptions.length; i++) {
						jQuery('#error-mensaje').show();
						jQuery('#error-mensaje').html(jQuery('#error-mensaje').html()+'<br/>'+data.exceptions[i]);
					}
				}
			}, 'json');
		});
		//funcion que llamamos cuando nos logamos
		jQuery('#login-form').unbind().submit(function() {
			jQuery('#error-mensaje').html("");
			showProgressAnimation();
			jQuery.post(jQuery(this).attr('action'), jQuery(this).serialize(), function(data) {
				if(!data.exceptions) {
					jQuery('#login-modal').trigger('close');
					updateLogin(jQuery('#login-form').attr('urllogin'));
					wishlist_panel = new Wishlist_Panel();
					ajaxcartproshow(jQuery('#login-form').attr('urlcart'));
					jQuery('.wishlist-login').html('View my wishlist');
					var url=MAGE_STORE_URL+"wishlistpanel/";
					jQuery('.wishlist-login').attr("href",url);
		            jQuery('.register-top-title').addClass('wislist--logged');
		            jQuery('.register-top-title').removeClass('register-top-title');
					hideProgressAnimation();
				} else {
					hideProgressAnimation();
					for(var i = 0; i < data.exceptions.length; i++) {
						jQuery('#error-mensaje').show();
						jQuery('#error-mensaje').html(jQuery('#error-mensaje').html()+'<br/>'+data.exceptions[i]);
					}
				}
			}, 'json');
		});
		
		//llamada a la funcion ajax para cuando olvidamos el password
		jQuery('#forgot-form').unbind().submit(function() {
			jQuery('#error-mensaje').html("");
			showProgressAnimation();//mostramos la animacion
			jQuery.post(jQuery(this).attr('action'), jQuery(this).serialize(), function(data) {
				if(!data.exceptions) {
					hideProgressAnimation();
					jQuery('#login-modal').trigger('close');
					//jQuery('#error-mensaje').html(data.success);
					jQuery('#forgot-box').hide();
					
				} else {
					hideProgressAnimation();
					for(var i = 0; i < data.exceptions.length; i++) {
						jQuery('#error-mensaje').show();
						jQuery('#error-mensaje').html(jQuery('#error-mensaje').html()+'<br/>'+data.exceptions[i]);
					}
				}
			}, 'json');
		});
	}