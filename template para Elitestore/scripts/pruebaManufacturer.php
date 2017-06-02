<?php 
require_once "../app/Mage.php";
Mage::app();
umask(0);
ob_end_clean();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$model=Mage::getModel("manufacturer/manufacturer");
$manufacturers=$model->getManufacturersTest(null,null,null);
		$recuentoTotal=0;
		foreach ($manufacturers as $designers):
			$recuentoTotal+=count($designers);
		endforeach;
		$promedioColumna=$recuentoTotal/4;
		if(count($manufacturers) > 0){
?>
				<div class="listado-designers">
					<ul class="listado-designers-columna">
				<?php
				$contador=0;
				$columnas=1;
				foreach ($manufacturers as $letra => $designers):
					if (($contador+count($designers)>$promedioColumna) && ($columnas<4)):
						?>
						</ul>
						<ul class="listado-designers-columna">
						<?php
						$contador=0;
						$columnas++;
					endif;
					?>
					
					<li class="letra"  letra="<?php echo $letra ?>" ><span><?php  echo $letra ?></span>
						<ul class="listado-designers-letra">
						<?php
						foreach ($designers as $nombre => $marca):
							$contador++;
							$new="";
							$manufacturer=$marca[0];
							if ($manufacturer['newdesigner']==1):
								$new='<span class="new">'.'New Designer'.'</span>';
							endif;
							
							?>
							<li class="diseñador">
								<?php if ($marca[1]): ?>
									<?php echo ("<a href='". $marca[0]['url_key'] . "' title='" . $nombre . "'>" .$nombre."</a>".$new); ?>
								<?php else: ?>
									<?php echo $nombre.$new ?>
								<?php endif; ?>
							</li>
						
						<?php
						endforeach;
						?>
						</li></ul>
						<?php 
					endforeach;
					?>
				</ul></div>
		<?php
		}
		?>