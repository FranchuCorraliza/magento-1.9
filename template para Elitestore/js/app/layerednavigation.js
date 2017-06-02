
jQuery(document).ready(function($){
	$(document).on( "click", ".block-layered-nav #narrow-by-list .filter-content ol li a", function(e) {
		e.preventDefault();
		updateLayerNavigation(this);
		showProgressAnimation();
		updateProductList(jQuery(this).attr('filtros'));
		hideProgressAnimation();
	});
});
	
function updateLayerNavigation(element){
	
	if (jQuery(element).closest('#filter-cat-content').length){ // Es el filtro de categorías
		if(jQuery(element).parent().find("ul").length){ // La categoría clicada tiene subcategorías
			if(jQuery(element).hasClass("selected")){ // La categoría clicada estaba seleccionada anteriormente
				if (jQuery(element).parent().find('ul').find(".selected").length>0){ // Alguna subcategoría de la categoría clicada estaba seleccionada anteriormente
					jQuery(element).parent().find('ul').find(".selected").each(function(index, element){ //Eliminamos la clase selected de todas las categorías interiores
						jQuery(element).removeClass("selected");
					});
					jQuery(element).parent().find("ul").hide(); // Plegamos todas las subcategorías internas
					jQuery(element).parent().find("ul").first().show(); //Desplegamos únicamente la 1ª línea de subcategorías
				}else{// No había ninguna subcategoría de la categoría clicada seleccionada anteriormente
					jQuery(element).removeClass("selected");
					jQuery(element).parent().find("ul").hide(); // Plegamos todas las subcategorías internas
				}
			}else{ // La categoría clicada no estaba seleccionada anteriormente
				jQuery(element).addClass("selected");
				jQuery(element).parent().find("ul").first().show();
			}
		}else{ //La categoría clicada no tiene subcategorías
			if(jQuery(element).hasClass("selected")){ // La categoría clicada estaba seleccionada anteriormente
				jQuery(element).removeClass("selected");
			}else{ //La categoría clicada no estaba seleccionada
				jQuery(element).addClass("selected");
			}
		}
	}else{
		if(jQuery(element).hasClass("selected")){ // La categoría clicada estaba seleccionada anteriormente
			jQuery(element).removeClass("selected");
		}else{ //La categoría clicada no estaba seleccionada
			jQuery(element).addClass("selected");
		}
	}
}

function updateProductList(filtros){
	jQuery.ajax({
        type: "POST",
        url:"http://desarrollo.elitestore.es/lux/en/layernavigation/",
		data : filtros,
    })
          .done(function(msg) {
            jQuery(".col-main").html(msg);
          })
          .fail(function() {
          })
          .always(function() {
          });
   

}