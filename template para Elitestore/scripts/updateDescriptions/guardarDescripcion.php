<?php 
	require_once "../../app/Mage.php";
	Mage::app();
	umask(0);
	Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
	$categoryId="";
	$marca="";
	$idioma="";
	if ($_POST['categoryId']){
		$categoryId=$_POST['categoryId'];
	}
	if ($_POST['marca']){
		$marca=$_POST['marca'];
	}
	if ($_POST['idioma']){
		$idioma=$_POST['idioma'];
	}
	if ($idioma=="en"){
		$stores=array(0);
	}else{
		$stores=array(2,4,6,8,10,12);
	}
	
	if ($categoryId!="" && $marca==""){ //Descripciones de categorías
		$descripcion="";
		if ($_POST['descripcion']){
			$descripcion=$_POST['descripcion'];
		}
		try{
			$resource = Mage::getResourceModel('catalog/category');
			foreach ($stores as $storeId){
				$category=Mage::getModel("catalog/category")->setStoreId($storeId)->load($categoryId);
				$category->setDescription($descripcion);
				$category->save();
			}
		}catch (Exception $e){
			echo "<span class='glyphicon glyphicon-error'></span> Error";
			var_dump($e);
		}
		echo "<span class='glyphicon glyphicon-ok'></span> Guardado";
	}elseif ($categoryId=="" && $marca!=""){ //Descripciones de diseñadores
		$defaults = array('default_imagemanufacturer2','default_image','default_imagebanner1','default_imagebanner2','default_imagebanner3','default_titulodesc1','default_descripcion1','default_titulodesc2','default_descripcion2','default_theicons','default_idsubcat','default_genero','default_textobanner1','default_linkbanner1','default_textobanner2','default_linkbanner2','default_textobanner3','default_linkbanner3','default_morefor1','default_linkmorefor1','default_morefor2','default_linkmorefor2','default_morefor3','default_linkmorefor3','default_morefor4','default_linkmorefor4','default_morefor5','default_linkmorefor5','default_morefor6','default_linkmorefor6','default_morefor7','default_linkmorefor7','default_morefor8','default_linkmorefor8');
		$inputs=array('titulodesc1','descripcion1','titulodesc2','descripcion2','theicons','idsubcat','genero','textobanner1','linkbanner1','textobanner2','linkbanner2','textobanner3','linkbanner3','morefor1','linkmorefor1','morefor2','linkmorefor2','morefor3','linkmorefor3','morefor4','linkmorefor4','morefor5','linkmorefor5','morefor6','linkmorefor6','morefor7','linkmorefor7','morefor8','linkmorefor8');
		$images=array('imagemanufacturer2','image','imagebanner1','imagebanner2','imagebanner3');
		
		
		try{
			foreach ($stores as $storeId){
				$manufacturer= Mage::getModel("manufacturer/manufacturer")->getManufacturerByOptionId($marca,$storeId);
				foreach ($defaults as $default){
					if(isset($_POST[$default])){
						$manufacturer->setData($default,1);
					}else{
						$manufacturer->setData($default,0);
					}
				}
				foreach ($images as $image){
					  if(isset($_POST[$image]['delete'])){
						Mage::helper('manufacturer')->deleteImageFile($manufacturer->getName(),$manufacturer->getData($image));
						$manufacturer->setData($image,"");
					  }
					  if(isset($_FILES[$image]) && $_FILES[$image][name]!=""){
						if ($storeId==2 || $storeId==0){
							$upload[$image]=Mage::helper('manufacturer')->uploadManufacturerImage($manufacturer->getName(),$_FILES[$image], $image);  
						}
						$manufacturer->setData($image,$upload[$image]);
					  }
				}
				foreach ($inputs as $input){
					if (isset($_POST[$input])){
						$manufacturer->setData($input,$_POST[$input]);
					}
				}
				$manufacturer->save();	
			}
		}catch(Exception $e){
			echo "<span class='glyphicon glyphicon-error'></span> Error";
			var_dump($e);
		}
		echo "<span class='glyphicon glyphicon-ok'></span> Guardado";
		
	}elseif ($categoryId!="" && $marca!=""){ //Descripciones compuestas por marcas y categorias
		try{
			foreach ($stores as $storeId){
				$manufacturer= Mage::getModel("manufacturer/manufacturer")->getManufacturerByOptionId($marca,$storeId);
				$descripciones=explode(";",$manufacturer->getDescription());
				$encontrado=false;
				foreach ($descripciones as $key => $descripcion){
					$descripcion=explode("|",$descripcion);
					if ($categoryId==$descripcion[0]){
						$encontrado=true;
						echo "Descripcion: ".$_POST["descripcion"];
						$descripciones[$key][1]=$_POST["descripcion"];
					}			
				}
				if (!$encontrado){
					$descripciones[]=$categoryId."|".$_POST["descripcion"];
				}
				
				$manufacturer->setData('description',implode(";",$descripciones));
				$manufacturer->save();
			}
		}catch(Exception $e){
			echo "<span class='glyphicon glyphicon-error'></span> Error";
			var_dump($e);
		}
		echo "<span class='glyphicon glyphicon-ok'></span> Guardado";
	
	}
	