jQuery(document).ready(function(){
    //actualizar la cantidad en el carrito de la compra
	jQuery(".choose-shipping").on("click",function(){
		jQuery('.modalwindow').lightbox_me({
					centered: true,
					zIndex:1,
					onLoad: function() {
						jQuery('.modalwindow').show();				}
				});
	});
})