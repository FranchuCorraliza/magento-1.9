/* USE THIS FUNCTION TO EXECUTE SCRIPTS AFTER THE AJAX LOAD
 * IN THIS CASE, WE RECREATE THE EXPAND /  COLLAPSE PATTERN USED IN THE RWD THEME
 * BECAUSE IT IS IMPLEMENTED WITHOUT USING A LIVE FUNCTION SO IT NEEDS TO BE RECREATED EACH TIME THE AJAX IS LOADED
 */


function afterAjaxReload(){



// Insertamos este código que cargará videos si los hubiese y cambiará la ubicación de las descripciones 
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    
jQuery(document).ready(function($){
	var parrafo=[];
	


			jQuery(".products-grid .video").each(function(){
					var codigo;
					codigo = "<video style=\"max-width:100%\" autoplay loop><source src=\"" + "<?php echo Mage::getUrl('media') ?>" + this.getAttribute('data-video') + ".mp4\" type=\"video/mp4\"><source src=\"" + "<?php echo Mage::getUrl('media') ?>" + this.getAttribute('data-video') + ".webm\" type=\"video/webm\"></video>";
 					this.innerHTML = codigo;

			});
		decorateGeneric($$('ul.products-grid'), ['odd','even','first','last']);

$(".text--js .paragraf").each(function( index, element ){
	var transic=[];

		transic[1]= $(element).find(".parrafo--title").html();
		transic[2]= $(element).find(".parrafo--text").html();
	parrafo.push(transic);
	});
if(parrafo.length>0)
{
	$('.products-grid li:eq(0)').before("<li class=\"text-center item\"><div class=\"textoli\"><h5 class=\"product-manufacturer\">" + parrafo[0][1] + "</h5><br/><div class=\"line\"></div><br/><div class=\"parrafo\"><p>" + parrafo[0][2] + "</p></div></div></li>");
}
if(parrafo.length>1 && $(".products-grid li").size()>10)
{
	$('.products-grid li:eq(10)').before("<li class=\"text-center item\"><div class=\"textoli\"><h5 class=\"product-manufacturer\">" + parrafo[1][1] + "</h5><br/><div class=\"line\"></div><br/><div class=\"parrafo\"><p>" + parrafo[1][2] + "</p></div></div></li>");
}
else if(parrafo.length>1 && $(".products-grid li").size()<10)
{
	$('.products-grid').append("<li class=\"text-center item\"><div class=\"textoli\"><h5 class=\"product-manufacturer\">" + parrafo[1][1] + "</h5><br/><div class=\"line\"></div><br/><div class=\"parrafo\"><p>" + parrafo[1][2] + "</p></div></div></li>");

}
$(".text--js").hide();
})
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------    
}