$(function() {
	$('.formulario').submit(function(event){
		event.preventDefault();
		
		var url="./ajax/guardarDescripcion.php";//?"+$(this).serialize();
      var datos = $(this).serialize();
     
        
		$("#guardar"+this.codigo.value).html("<span class='glyphicon glyphicon-refresh glyphicon-refresh-animate'></span> Loading...");
		$.ajax({
               data: datos,
               url: url,
               type: 'post',
            })
         .done(function(msg) {
               var json=JSON.parse(msg);
               var codigo = json[0].codigo;
						var msg = json[0].msg;
						
						$("#guardar"+codigo).html(msg);
             });
	});
});