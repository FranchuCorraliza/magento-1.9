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


    var producto = jQuery('.product-view').attr("id");

     jQuery.ajax({
        type: "POST",
        url:"http://192.168.1.201:8080/elitestore192/lux/en/ajaxcontrol/index/estilismo/",
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
        url:"http://192.168.1.201:8080/elitestore192/lux/en/ajaxcontrol/index/sugerencias/",
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