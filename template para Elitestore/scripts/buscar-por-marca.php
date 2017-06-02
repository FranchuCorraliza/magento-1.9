	<html lang="en">
	  <head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Buscar por Codigo de barras</title>
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	  <script>
			jQuery(document).ready(function(){
				jQuery('#dpto').change(function(){
					if (this.value==4){
						jQuery('#seccion option[value=1]').text("GIFTING");
						jQuery('#seccion option[value=2]').text("RELOJES");
						jQuery('#seccion option[value=3]').text("GAFAS");
						jQuery('#seccion option[value=4]').text("VARIOS");
						jQuery('#seccion option[value=5]').text("HOGAR");
					}else{
						jQuery('#seccion option[value=1]').text("ROPA");
						jQuery('#seccion option[value=2]').text("COMPLEMENTOS");
						jQuery('#seccion option[value=3]').text("ZAPATERIA");
						jQuery('#seccion option[value=4]').text("BOLSOS");
						jQuery('#seccion option[value=5]').text("BISUTERIA");
					}
				});
				jQuery('#formulario').submit(function(){
					if(jQuery('#manufacturer option:selected').val()==""){
						alert ("Debe seleccionar al menos una marca");
					}
				});
			});
			
	  </script>
	  </head>
	  <body>
	  
<?php 
require_once "../app/Mage.php";
Mage::app();
umask(0);
Mage::app()->setCurrentStore(1);
if(!$_GET):
	 $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'manufacturer'); //"color" is the attribute_code
	  $allOptions = $attribute->getSource()->getAllOptions(true, true);
	  
	?>
		<div style="width: 50%;margin: 30px auto;border: solid;padding: 50px 0px;text-align: center;">
		<h1>Buscar por Marca</h1>
		
		<form id="formulario" class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-4 control-label" for="manufacturer">Marca: </label>
				<div class="col-sm-5">
					<select class="form-control" id="manufacturer" name="manufacturer">
						<?php 
							foreach ($allOptions as $instance) {
								if ($instance['label']!="Invisible" && $instance['label']!=""){
									echo "<option value='".$instance['value']."'>".$instance['label']."</option>";
								}
							}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label" for="genero">Departamento: </label>
				<div class="col-sm-5">
					<select class="form-control" id="dpto" name="dpto">
						<option value="">Todas</option>
						<option value="1">MUJER</option>
						<option value="2">HOMBRE</option>
						<option value="6">KIDS BOY</option>
						<option value="7">KIDS GIRL</option>
						<option value="4">ACCESSORIOS</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label" for="genero">Sección: </label>
				<div class="col-sm-5">
					<select class="form-control" id="seccion" name="seccion">
						<option value="">Todas</option>
						<option value="1">ROPA</option>
						<option value="2">COMPLEMENTOS</option>
						<option value="3">ZAPATERIA</option>
						<option value="4">BOLSOS</option>
						<option value="5">BISUTERIA</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label" for="tipo">Tipo: </label>
				<div class="col-sm-5">
					<input class="form-control" type="text" class="form-control" name="tipo" id="tipo">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label" for="tipo">Temporada: </label>
				<div class="col-sm-5">
					<input class="form-control" type="text" class="form-control" name="temporada" id="temporada">
				</div>
			</div>
			<div class="form-group">
				<input type="submit" value="Continuar"  class="btn btn-danger"/>
			</div>
		</form>
		<a href="buscar-items.php">Volver<a/>
	  </div>
	<?php
else:
	$manufacturerId=$_GET['manufacturer'];
	$tipo=$_GET['tipo'];
	$dpto=$_GET['dpto'];
	$seccion=$_GET['seccion'];
	$temporada=$_GET['temporada'];
	$collection = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*')
		->addAttributeToFilter('type_id', array('eq' => 'configurable'))
		->addAttributeToFilter('manufacturer', array('eq' => $manufacturerId));
	if ($dpto!=""){
		$collection->addAttributeToFilter('departamento', array('eq' => $dpto));
	}
	if ($seccion!=""){
		$collection->addAttributeToFilter('seccion', array('eq' => $seccion));
	}
	if ($tipo!=""){
		$collection->addAttributeToFilter('tipo', array('eq' => $tipo));
	}
	if ($temporada!=""){
		$options = Mage::getModel('eav/config')->getAttribute('catalog_product', 'temporada')->getSource()->getAllOptions(); //get all options
		$optionId = false;
		foreach ($options as $option) {
			if ($option['label'] == $temporada){ //find the Red option
				$optionId = $option['value']; //get it's id
				break;
			}
		}
		if ($optionId) { //if there is an id...
			$collection->addAttributeToFilter('temporada', $optionId);
			
		}else{
			echo "No existe la temporada seleccionada, mostramos todas<br/>";
		}
		
		//$collection->addAttributeToFilter('temporada', array('eq' => $temporada));
	}
	$collection->addAttributeToSort('departamento','asc');
	$collection->addAttributeToSort('seccion','asc');
	$collection->addAttributeToSort('name','asc');
	if (count($collection)>0):
		
		$html="<table  class='table' style='font-size: 1rem;'><tr><th>Imagen</th><th>Referencia</th><th>Nombre</th><th>Marca</th><th>Departamento</th><th>Sección</th><th>Tipo</th><th>Temporada</th><th>Is in stock</th><th>Link</th></tr>";
		
		foreach ($collection as $item):
			$product=Mage::getModel("catalog/product")->setStoreId(0)->load($item->getId());
			$productImage=Mage::helper('catalog/image')->init($product, 'image')->resize(90);
			$productName= $product->getName();
			$productManufacturer=$product->getAttributeText('manufacturer');
			$inStock=$product->getStockItem()->getIsInStock();
			$tipo=$product->getTipo();
			$dpto=$product->getDepartamento();
			$seccion=$product->getSeccion();
			$temporada=$product->getAttributeText('temporada');
			
			$html.="<tr><td><img style='width:90px' src='".$productImage."'/></td><td>".$item->getSku()."</td><td>".$productName."</td><td>".$productManufacturer."</td><td>".$dpto."</td><td>".$seccion."</td><td>".$tipo."</td><td>".$temporada."<td>".$inStock."</td><td><a href='http://elitestores.com/scripts/buscar-por-referencia.php?ref=".$item->getSku()."'>Ver m&aacute;s</a></td></tr>";
		endforeach;
		$html.="</table>";
		echo $html;
	else:
		echo "No existen registros de esta marca";
	endif;	
endif;