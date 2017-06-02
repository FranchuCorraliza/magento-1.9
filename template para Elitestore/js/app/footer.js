jQuery(document).on('ready',function($){  
    /************** Acordeon de product Info ***************************/
    
    jQuery('.acordeon dd').not('dt.desplegado + dd').hide();
    jQuery('.acordeon dt').click(function(){
          
          if (jQuery(this).hasClass('desplegado')) {
               jQuery(this).removeClass('desplegado');
               jQuery(this).next().slideUp();
          } else {
               jQuery('.acordeon dt').removeClass('desplegado');
               jQuery(this).addClass('desplegado');
               jQuery('.acordeon dd').slideUp();
               jQuery(this).next().slideDown();
          }
    });
    
    /*********************************************************************/
});