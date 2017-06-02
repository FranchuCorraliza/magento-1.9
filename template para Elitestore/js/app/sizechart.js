jQuery(document).ready(function($) {
    jQuery("td").hover(
        function(){
            jQuery(this).parent('tr').addClass('selected');
        }, function(){
            jQuery(this).parent('tr').removeClass('selected');
        }
    );
});
//sizechart-background