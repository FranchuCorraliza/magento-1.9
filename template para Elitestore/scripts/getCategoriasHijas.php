<?php 
require_once "../app/Mage.php";
Mage::app();
umask(0);

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

	$mujer = Mage::getModel('catalog/category')->load(1003);
	$hijosmujer = $mujer->getChildrenCategories(); 
	echo 'Descendientes de Mujer:';
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
	
	$hombre = Mage::getModel('catalog/category')->load(10);
	$hijoshombre = $hombre->getChildrenCategories(); 	
	echo '<br/>Descendientes de Hombre:';
	foreach ($hijoshombre as $hijo):
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
		
	$kids = Mage::getModel('catalog/category')->load(11);
	$hijoskids = $kids->getChildrenCategories(); 	
	echo '<br/>Descendientes de Kids:';
	foreach ($hijoskids as $hijo):
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
	
	$gifts = Mage::getModel('catalog/category')->load(12);
	$hijosgifts = $gifts->getChildrenCategories(); 	
	echo '<br/>Descendientes de Gifts:';
	foreach ($hijosgifts as $hijo):
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
?>