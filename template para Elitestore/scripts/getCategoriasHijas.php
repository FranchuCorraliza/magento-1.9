<?php 
require_once "../app/Mage.php";
Mage::app();
umask(0);

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$primarias = array(
	"Ropa Mujer" => 1003,
	"Zapatos Mujer" =>1027,
	"Bolsos Mujer" => 1039,
	"Accs Mujer" => 1046,
	"Ropa Hombre" =>1073,
	"Zapatos Hombre" => 1092,
	"Bolsos Hombre" => 1101,
	"Accs Hombre" => 1109,
	"Ropa Kids" =>1140,
	"Zapatos Kids" =>1148,
	"Accs Kids" =>1149);
	foreach ($primarias as $descripcion => $categoria){
		$mujer = Mage::getModel('catalog/category')->load($categoria);
		$hijosmujer = $mujer->getChildrenCategories(); 
		echo 'Descendientes de'.$descripcion.": ";
		foreach ($hijosmujer as $hijo):
			if ($hijo->getIsActive()):
				echo $hijo->getId();
				echo ', ';
				$nietos=$hijo->getChildrenCategories();
				foreach ($nietos as $nieto):
					if ($nieto->getIsActive()):
						echo $nieto->getId();
						echo ', ';
						$biznietos=$nieto->getChildrenCategories();
						foreach ($biznietos as $biznieto):
							if ($biznieto->getIsActive()):
								echo $biznieto->getId();
								echo ', ';
								$tataranietos=$biznieto->getChildrenCategories();
								foreach ($tataranietos as $tataranieto):
									if ($tataranieto->getIsActive()):
										echo $tataranieto->getId();
										echo ', ';
									endif;
								endforeach;
							endif;								
						endforeach;					
					endif;
				endforeach;
			endif;
		endforeach;
		echo "<hr>";
	}
	
	
?>