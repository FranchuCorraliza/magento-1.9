<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buscar Items en Elite Store</title>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  </head>
  <body>
	<div style="    width: 30%;min-width: 400px;text-align: center;padding: 25px;margin: 0 auto;">
		<h3>Buscar Items en Elite Store</h3>
		<p>Seleccione una de las opciones</p>
		<button type="button" onclick="window.location.href = 'buscar-por-codigo.php';" class="btn btn-default btn-lg">Por código</button>
		<button type="button" onclick="window.location.href = 'buscar-por-referencia.php';" class="btn btn-default btn-lg">Por refencia</button>
		<button type="button" onclick="window.location.href = 'buscar-por-marca.php';" class="btn btn-default btn-lg">Por marca</button>
	</div>
  </body>