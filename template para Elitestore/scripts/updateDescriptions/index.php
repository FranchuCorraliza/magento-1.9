<?php
require_once "../../app/Mage.php";
Mage::app();
umask(0);

$categories = Mage::getModel('catalog/category')
        ->getCollection()
        ->addAttributeToSelect('*')
        ->setStoreId(3)
        ->addFieldToFilter('is_active', 1)
        ->load();	
$attribute = Mage::getModel('catalog/resource_eav_attribute')->load(187);
$marcas = $attribute->getSource()->getAllOptions();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Importador Elite Store</title>

      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	  <script src="js/updateDescriptions.js"></script>
      
  </head>
  <body>
  <div class="container" style="width: 1070px;margin:0px;padding:0px;">
		<h3>Buscar Contenido</h3>
	
		<form  class="form-inline" id="form-buscar-descripcion">
			<label for="Categoría">Categor&iacute;a</label>
			<select class='form-control' id="categorías" name="categorias">
				<option value="">Ninguna</option>
				<?php foreach ($categories as $category){
					?>
						<option value="<?php echo $category->getId() ?>"><?php echo $category->getUrlPath() ?></option>
					<?php
				}
				?>
			</select>
		  
			<label  for="marca">Marca</label>
			<select class='form-control' id="marca" name="marca">
				<option value="">Ninguna</option>
				<?php foreach ($marcas as $marca){
					?>
						<option value="<?php echo $marca['value'] ?>"><?php echo $marca['label'] ?></option>
					<?php
				}
				?>
			</select>
		  
			<label for="idioma">Idioma</label>
			<select class='form-control' id="idioma" name="idioma">
				<option value="en">English</option>
				<option value="es">Spanish</option>
			</select>
		  <button type="submit" class="btn btn-primary" id="buscadorB">Buscar</button>
        </form>
		<hr>
		<h3>Editar Contenido</h3>
		<div id="description">
			...
		</div>
  </div>

  </body>