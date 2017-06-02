$(function() {
    
   $('.sustituir').on('click',function(event){
         event.preventDefault();
		 var formulario= $(this).parent();
         var data = new FormData(formulario[0]),
             ruta = "ajax/imagen-ajax.php";
             
            $.ajax({
               url: ruta,
               type: 'POST',
               mimeType: "multipart/form-data",
                   data: data,
               async: false,
               success: function (data) {
                  var obj=JSON.parse(data);
                  if (obj.error=='0'){
                     $(obj.imageId).attr('src',obj.image);
                     $(obj.id).html("<span class='glyphicon glyphicon-ok'></span> Guardado");
                     
                  }else{
                     $(obj.id).html("<span class='glyphicon glyphicon-error'></span> Error");
                  }                 
               },
               error: function (){
                  
                  alert ('Error');
               },
               cache: false,
               contentType: false,
               processData: false
            });
   });
   
   $('.incrementar').on('input',function(){
      var valor = $(this).val(), foto, codigo, enrango, id=$(this).attr('id'), repetido= 0;
          
          foto = $(this).attr('id').substr(6,$(this).attr('id').length).toString();
          codigo = $(this).parent().find('form').attr('id').substr(10,$(this).parent().find('form').attr('id').length).toString();
          if(valor>0&&valor<8){
            enrango=0;
          }
          else
          {
            enrango=1;
          }
          $(this).parent().parent().find('td').each(function(index,element){
               if($(element).find('.incrementar').attr('id')!=id){
                  if(valor==$(element).find('.incrementar').val()){
                     repetido=1;
                  }
               }
            });
          if(repetido==0&&
             enrango==0)
          {
            alert('todo correcto');  
          }
          else if(repetido!=0)
          {
              alert('El numero que has puesto a la foto está repetido en otra');
          }
          else
          {
            alert('El numero que has puesto a la foto no pertenece al rango de 1 a 7');
          }
          
          $("#"+codigo+"cambiarimage"+foto).val(valor);

   });
   
   $(".formcambiarorder").submit(function(event){
      event.preventDefault();
      var ruta = "ajax/orden-ajax.php";
      var data = new FormData($(this)[0]);
      
      $.ajax({
               url: ruta,
               type: 'POST',
               data: data,
               async: false,
               success: function (data) {
                  var obj=JSON.parse(data);
                  if (obj.error=='0'){
                     var rutacomun="https://www.elitestores.com/media/import/";
                     
                     $('#'+obj.codigo+'-image1').attr('src',rutacomun+obj.image1);
                     $('#'+obj.codigo+'-image2').attr('src',rutacomun+obj.image2);
                     $('#'+obj.codigo+'-image3').attr('src',rutacomun+obj.image3);
                     $('#'+obj.codigo+'-image4').attr('src',rutacomun+obj.image4);
                     $('#'+obj.codigo+'-image5').attr('src',rutacomun+obj.image5);
                     $('#'+obj.codigo+'-image6').attr('src',rutacomun+obj.image6);
                     $('#'+obj.codigo+'-image7').attr('src',rutacomun+obj.image7);
                     
                     $(obj.id).html("<span class='glyphicon glyphicon-ok'></span> Guardado");
                     
                  }else{
                     $(obj.id).html("<span class='glyphicon glyphicon-error'></span> Error");
                  }
                  
               },
               error: function (){
                  
                  alert ('Error');
               },
               cache: false,
               contentType: false,
               processData: false
            });
      
      });
   $('.elimininar').on('click',function(event){
         event.preventDefault();
			var idEliminar= $(this).attr('id').substr(0,$(this).attr('id').length-13),
         foto = $(this).attr('id').substr($(this).attr('id').length-1,$(this).attr('id').length);
         var ruta = "ajax/eliminar-ajax.php";
          var parametros = {
                'id':idEliminar,
                'foto':foto
        };
         $.ajax({
               data: parametros,
               url: ruta,
               type: 'post',
            })
         .done(function(msg) {
               var obj=JSON.parse(msg);
               var rutacomun="https://www.elitestores.com/media/import/desaparecido.jpg";
                  $(obj.image).attr('src', rutacomun);
                  $(obj.boton).html("<span class='glyphicon glyphicon-ok'></span> Guardado");
             });
   });
});

