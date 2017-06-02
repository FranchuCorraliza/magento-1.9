/* USE THIS FUNCTION TO EXECUTE SCRIPTS AFTER THE AJAX LOAD
 * IN THIS CASE, WE RECREATE THE EXPAND /  COLLAPSE PATTERN USED IN THE RWD THEME
 * BECAUSE IT IS IMPLEMENTED WITHOUT USING A LIVE FUNCTION SO IT NEEDS TO BE RECREATED EACH TIME THE AJAX IS LOADED
 */


function afterAjaxReload(){
// Actualizamos elementos del Wishlist_Panel
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        wishlist_panel = new Wishlist_Panel();
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------




// Insertamos este código que cargará videos si los hubiese y cambiará la ubicación de las descripciones 
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    

	jQuery(".products-grid .video").each(function(){
			var codigo;
			codigo = "<video style=\"max-width:100%; height:390px\" autoplay loop><source src=\"" + this.getAttribute('data-video') + ".mp4\" type=\"video/mp4\"><source src=\"" + this.getAttribute('data-video') + ".webm\" type=\"video/webm\"></video>";
			this.innerHTML = codigo;
	});
	
	jQuery(".product-image").hover(function(){
    	if(jQuery(this).find('img').length>2)
		{
            jQuery(this).find('.imagen--principal').css({'display':'none'});
            jQuery(this).find('.imagen--back').css({'display':'block'});
		}
    }, function(){
        if(jQuery(this).find('img').length>2) {
            jQuery(this).find('.imagen--principal').css({'display': 'block'});
            jQuery(this).find('.imagen--back').css({'display': 'none'});
        }
    });
}