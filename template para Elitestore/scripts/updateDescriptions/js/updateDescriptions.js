$(document).ready(function($) {
	$('#form-buscar-descripcion').submit(function(event){
		event.preventDefault();
		var url="cargarDescripcion.php";
		$.ajax({
                    type: "POST",
                    url: url,
                    cache: false,
                    async: false,
					data: $(this).serialize(),
                    success: function(result) {
						$("#description").html(result);
                        
                    },
                    error: function(result) {
                        alert("Data not found");
                    }
                });
	});
	
	
});