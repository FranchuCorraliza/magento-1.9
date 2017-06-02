	<html lang="en">
	  <head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Buscar por Codigo de barras</title>
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	  </head>
	  <body>
	  
<?php 
require_once "../app/Mage.php";
Mage::app();
umask(0);
Mage::app()->setCurrentStore(1);


if(!$_GET):
	?>
		<div style="width: 50%;margin: 30px auto;border: solid;padding: 50px 0px;text-align: center;">
		<h1>Buscar por código de barras</h1>
		<p>Indique el código de barras buscado</p>
		<form>
			<div class="form-group">
				<input type="text" class="form-control" name="barcode">
				<input type="submit" value="Continuar"  class="btn btn-danger"/>
			</div>
		</form>
		<a href="buscar-items.php">Volver<a/>
	  </div>
	<?php
else:
	if ($_GET['barcode']):
		$barcode=$_GET['barcode'];
		$collection = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*')->addAttributeToFilter('type_id', array('eq' => 'configurable'))->addAttributeToFilter('codbarras', array('eq' => $barcode));
		if (count($collection)>0):
			$item=$collection->getFirstItem();
			$product=Mage::getModel('catalog/product')->load($item->getId());
			$nombre=$product->getName();
			$product=Mage::getModel('catalog/product')->setStoreId(2)->load($item->getId());
			$nombreEn=$product->getName();
			$marca=$product->getAttributeText('manufacturer');
			$precio=round($product->getFinalPrice()+$product->getFinalPrice()*0.21);
			$categorias=$product->getCategoryIds();
			foreach ($categorias as $cat):
				$categoria=Mage::getModel('catalog/category')->load($cat);
				$coll = $categoria->getResourceCollection();
				$pathIds = $categoria->getPathIds();
				$coll->addAttributeToSelect('name');
				$coll->addAttributeToFilter('entity_id', array('in' => $pathIds));
				$categoria = '';
				foreach ($coll as $cat) {
					$categoria .= $cat->getName().'/';
				}
			endforeach;
			$link = $product->getProductUrl();
			$referencia=$item->getData('sku');
			
			?>
			<div style="width: 50%;margin: 30px auto;border: solid;padding: 50px 0px;height: 432px;">
				<div style="width:35%;float:left"><a href="<?php echo (string)Mage::helper('catalog/image')->init($product,'image') ?>"><img src="<?php echo (string)Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(200); ?>"/></a></div>
				<div style="width:65%;float:right">
					<span style="font-weight:bold">Referencia:</span><?php echo $referencia ?><br/>
					<span style="font-weight:bold">Código:</span><?php echo $barcode?><br/>
					<span style="font-weight:bold">Nombre:</span><?php echo $nombre ?><br/>
					<span style="font-weight:bold">Nombre Inglés:</span><?php echo $nombreEn ?><br/>
					<span style="font-weight:bold">Marca:</span><?php echo $marca ?><br/>
					<span style="font-weight:bold">Precio:</span><?php echo $precio ?><br/>
					<span style="font-weight:bold">Categoria:</span><?php echo $categoria ?><br/>
					<span style="font-weight:bold">Link:</span><a href="<?php echo $link ?>"><?php echo $link ?></a><br/>
				</div>
				<div style="width:100%;float:left;text-align:center">
					<span style="font-weight:bold">Todas las imágenes</span><br/>
					<?php
					foreach ($product->getMediaGalleryImages() as $image) {
						
						?><a href="<?php echo Mage::helper('catalog/image')->init($product, 'image', $image->getFile()); ?>"><img src="<?php echo Mage::helper('catalog/image')->init($product, 'image', $image->getFile())->resize(100); ?>" /></a>
					<?php } ?>
				</div>
			</div>
			<div style="width:100%;text-align:center">
				<a href="buscar-por-codigo.php">Volver<a/></div>
			</div>
			<?php
		
		else:
			?>
			<div style="width: 50%;margin: 30px auto;border: solid;padding: 50px 0px;text-align: center;">
				No existe ningún producto con el código de barrras <?php echo $barcode ?>
				<br/>
				<a href="buscar-por-codigo.php">Volver<a/>
			</div>
			<?php
		endif;
	endif;		

endif;
?>
	</body>
</html>
