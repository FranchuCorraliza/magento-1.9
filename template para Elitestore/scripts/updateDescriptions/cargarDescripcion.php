<?php
	require_once "../../app/Mage.php";
	Mage::app();
	umask(0);
	Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
	$categoryId="";
	$marca="";
	$idioma="";
	if ($_POST['categorias']){
		$categoryId=$_POST['categorias'];
	}
	if ($_POST['marca']){
		$marca=$_POST['marca'];
	}
	if ($_POST['idioma']){
		$idioma=$_POST['idioma'];
	}
	if ($idioma=="en"){
		$storeId=0;
		//$stores=array(1,3,5,7,9,11);
	}else{
		$storeId=2;
		//$stores=array(2,4,6,8.10,12);
	}
	$html="<form id='form-updateDescription' >";
	$html.="<input type='hidden' name='categoryId' value='".$categoryId."' />";
	$html.="<input type='hidden' name='marca' value='".$marca."' />";
	$html.="<input type='hidden' name='idioma' value='".$idioma."' />";
	if ($categoryId!="" && $marca==""){ //Descripciones de categorías
		$category=Mage::getModel("catalog/category")->setStoreId($storeId)->load($categoryId);
		$html.="<label for='descripcion'>Bloque Descripci&oacute;n</label><textarea class='form-control' rows='10' name='descripcion'>".$category->getDescription()."</textarea>";
		$html.="<button type='submit' class='btn btn-primary' id='guardar'>Guardar</button></form>";
	
	
	
	
	
	
	
	}elseif ($categoryId=="" && $marca!=""){ //Descripciones de diseñadores
		$manufacturer= Mage::getModel("manufacturer/manufacturer")->getManufacturerByOptionId($marca,$storeId);
		$inputDefaultImagemanufacturer2="";
		if ($storeId==2 && $manufacturer->getDefaultImagemanufacturer2()==1){
			$inputDefaultImagemanufacturer2="<input type='checkbox' name='default_imagemanufacturer2' onclick='habilitarInput(this)' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultImagemanufacturer2()==0){
			$inputDefaultImagemanufacturer2="<input type='checkbox' name='default_imagemanufacturer2' onclick='habilitarInput(this)' >";
		}
		
		
		$html.="<div class='form-group col-xs-6'>
					".$inputDefaultImagemanufacturer2."
					<label for='imagemanufacturer2'>Imagen Logo</label>
					<div  style='width:100%;height:400px'>
						
						<img style='text-align:center' src='".Mage::helper('manufacturer')->getUrlImagePath($manufacturer->getName()) .'/'. $manufacturer->getImagemanufacturer2()."' width='50%' />
						<input name='imagemanufacturer2' id='imagemanufacturer2' type='file' />
						<input type='checkbox' name='imagemanufacturer2[delete]'>Borrar Imagen
					</div>
				</div>";
		$inputDefaultImage="";
		if ($storeId==2 && $manufacturer->getDefaultImage()==1){
			$inputDefaultImage="<input type='checkbox'  onclick='habilitarInput(this)' name='default_image' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultImage()==0){
			$inputDefaultImage="<input type='checkbox'  onclick='habilitarInput(this)'name='default_image' >";
		}
		$html.="<div class='form-group col-xs-6 '>
					".$inputDefaultImage."
					<label for='image'>Imagen Cabecera</label>
					<div style='width:100%;height:400px'>
						<img style='text-align:center' src='".Mage::helper('manufacturer')->getUrlImagePath($manufacturer->getName()) .'/'. $manufacturer->getImage()."' width='50%' />
						<input name='image' id='image' type='file' />
						<input type='checkbox' name='image[delete]'>Borrar Imagen
					</div>
				</div>";
		$html.="</div>";
		$html.="<div class='form-group col-xs-6 '>";
		$inputDefaultTitulodesc1="";
		if ($storeId==2 && $manufacturer->getDefaultTitulodesc1()==1){
			$inputDefaultTitulodesc1="<input type='checkbox'  onclick='habilitarInput(this)' name='default_titulodesc1' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultTitulodesc1()==0){
			$inputDefaultTitulodesc1="<input type='checkbox'  onclick='habilitarInput(this)' name='default_titulodesc1' >";
		}
		$html.=$inputDefaultTitulodesc1;
		$html.="<label for='titulodesc1'>Titulo 1</label>
					<input class='form-control' type='text' id='titulodesc1' name='titulodesc1' value='".$manufacturer->getTitulodesc1()."'/>";
		$inputDefaultDescripcion1="";
		if ($storeId==2 && $manufacturer->getDefaultDescripcion1()==1){
			$inputDefaultDescripcion1="<input type='checkbox'  onclick='habilitarInput(this)' name='default_descripcion1' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultDescripcion1()==0){
			$inputDefaultDescripcion1="<input type='checkbox'  onclick='habilitarInput(this)' name='default_descripcion1' >";
		}
		$html.=$inputDefaultDescripcion1;					
		$html.="<label for='descripcion1'>Bloque 1</label><textarea class='form-control' rows='10' id='descripcion1' name='descripcion1'>".$manufacturer->getDescripcion1()."</textarea>";
		$html.="</div>";
		$html.="<div class='form-group col-xs-6 '>";
		$inputDefaultTitulodesc2="";
		if ($storeId==2 && $manufacturer->getDefaultTitulodesc2()==1){
			$inputDefaultTitulodesc2="<input type='checkbox'  onclick='habilitarInput(this)' name='default_titulodesc2' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultTitulodesc2()==0){
			$inputDefaultTitulodesc2="<input type='checkbox'  onclick='habilitarInput(this)' name='default_titulodesc2' >";
		}
		$html.=$inputDefaultTitulodesc2;
		$html.="<label for='titulodesc2'>Titulo 2</label><input class='form-control' type='text' id='titulodesc2' name='titulodesc2' value='".$manufacturer->getTitulodesc2()."'/>";
		$inputDefaultDescripcion2="";
		if ($storeId==2 && $manufacturer->getDefaultDescripcion2()==1){
			$inputDefaultDescripcion2="<input type='checkbox'  onclick='habilitarInput(this)' name='default_descripcion2' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultDescripcion2()==0){
			$inputDefaultDescripcion2="<input type='checkbox'  onclick='habilitarInput(this)' name='default_descripcion2' >";
		}
		$html.=$inputDefaultDescripcion2;
		$html.="<label for='descripcion2'>Bloque 2</label><textarea class='form-control' rows='10' id='descripcion2' name='descripcion2'>".$manufacturer->getDescripcion2()."</textarea>";
		$html.="</div>";
		$html.="<div class='form-group col-xs-12'>";
		$inputDefaultTheicons="";
		if ($storeId==2 && $manufacturer->getDefaultTheicons()==1){
			$inputDefaultTheicons="<input type='checkbox'  onclick='habilitarInput(this)' name='default_theicons' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultTheicons()==0){
			$inputDefaultTheicons="<input type='checkbox'  onclick='habilitarInput(this)' name='default_theicons' >";
		}
		$html.=$inputDefaultTheicons;
		$html.="<label for='theicons'>The Icons</label><textarea class='form-control' rows='10' id='theicons' name='theicons'>".$manufacturer->getTheicons()."</textarea>";
		$html.="</div>";
		$html.="<div class='form-group col-xs-6 '>";
		$inputDefaultIdsubcat="";
		if ($storeId==2 && $manufacturer->getDefaultIdsubcat()==1){
			$inputDefaultIdsubcat="<input type='checkbox' onclick='habilitarInput(this)' name='default_idsubcat' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultIdsubcat()==0){
			$inputDefaultIdsubcat="<input type='checkbox' onclick='habilitarInput(this)' name='default_idsubcat' >";
		}
		$html.=$inputDefaultIdsubcat;
		$categories = Mage::getModel('catalog/category')
        ->getCollection()
        ->addAttributeToSelect('*')
        ->setStoreId(3)
        ->addFieldToFilter('is_active', 1)
        ->load();
		$seleccionadas= explode(',',$manufacturer->getIdsubcat());
		
		$html.="<label for='idsubcat'>Categorias Destacadas</label><select class='form-control' id='idsubcat' name='idsubcat' multiple='multiple'>";
		foreach ($categories as $category){
			$selected='';
			if (in_array($category->getId(),$seleccionadas)){
				$selected="selected";
			}
			$html.="<option value=".$category->getId()." ".$selected." >".$category->getUrlPath()."</option>";
		}
		$html.="</select>";
		$html.="</div>";
		$html.="<div class='form-group col-xs-6 '>";
		$inputDefaultGenero="";
		if ($storeId==2 && $manufacturer->getDefaultGenero()==1){
			$inputDefaultGenero="<input type='checkbox' onclick='habilitarInput(this)' name='default_genero' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultGenero()==0){
			$inputDefaultGenero="<input type='checkbox' onclick='habilitarInput(this)' name='default_genero' >";
		}
		$html.=$inputDefaultGenero;
		$genero=explode(',',$manufacturer->getGenero());
		$generoWomen=(in_array('WOMEN',$genero))?'selected':'';
		$generoMen=(in_array('MEN',$genero))?'selected':'';
		$generoKids=(in_array('KIDS',$genero))?'selected':'';
		$html.="<label for='genero'>Bloque Botones</label>
				<select class='form-control' id='genero' name='genero' multiple='multiple'>
					<option $generoWomen>WOMEN</option>
					<option $generoMen>MEN</option>
					<option $generoKids>KIDS</option>
				</select>";
		$html.="</div>";
		$html.="<div class='form-group col-xs-4 '>";
		
		$inputDefaultImagebanner1="";
		if ($storeId==2 && $manufacturer->getDefaultImagebanner1()==1){
			$inputDefaultImagebanner1="<input type='checkbox' onclick='habilitarInput(this)' name='default_imagebanner1' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultImagebanner1()==0){
			$inputDefaultImagebanner1="<input type='checkbox' onclick='habilitarInput(this)' name='default_imagebanner1' >";
		}
		$html.=$inputDefaultImagebanner1;
		$html.="<label for='imagebanner1'>Imagen Banner 1</label>";
		$html.="
				<div style='width:100%;height:200px'>
					<img style='text-align:center' src='".Mage::helper('manufacturer')->getUrlImagePath($manufacturer->getName()) .'/'. $manufacturer->getImagebanner1()."' width='50%' />
					<input style='text-align:center' id='imagebanner1' name='imagebanner1' type='file' />
					<input type='checkbox' name='imagebanner1[delete]'>Borrar Imagen
				</div>";
		$inputDefaultTextobanner1="";
		if ($storeId==2 && $manufacturer->getDefaultTextobanner1()==1){
			$inputDefaultTextobanner1="<input type='checkbox' onclick='habilitarInput(this)'' name='default_textobanner1' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultTextobanner1()==0){
			$inputDefaultTextobanner1="<input type='checkbox' onclick='habilitarInput(this)' name='default_textobanner1' >";
		}
		$html.=$inputDefaultTextobanner1;
		$html.="<label for='textobanner1'>Texto Banner 1</label><input class='form-control' type='text' id='textobanner1' name='textobanner1' value='".$manufacturer->getTextobanner1()."'/>";
		$inputDefaultLinkbanner1="";
		if ($storeId==2 && $manufacturer->getDefaultLinkbanner1()==1){
			$inputDefaultLinkbanner1="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkbanner1' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultLinkbanner1()==0){
			$inputDefaultLinkbanner1="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkbanner1' >";
		}
		$html.=$inputDefaultLinkbanner1;
		$html.="<label for='linkbanner1'>Link Banner 1</label><input class='form-control' type='text' id='linkbanner1' name='linkbanner1' value='".$manufacturer->getLinkbanner1()."'/>";
		$html.="</div>";
		$html.="<div class='form-group col-xs-4 '>";
		$inputDefaultImagebanner2="";
		if ($storeId==2 && $manufacturer->getDefaultImagebanner2()==1){
			$inputDefaultImagebanner2="<input type='checkbox' onclick='habilitarInput(this)' name='default_imagebanner2' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultImagebanner2()==0){
			$inputDefaultImagebanner2="<input type='checkbox' onclick='habilitarInput(this)' name='default_imagebanner2' >";
		}
		$html.=$inputDefaultImagebanner2;
		$html.="<label for='imagebanner2'>Imagen Banner 2</label>
				<div style='width:100%;height:200px'>
					<img style='text-align:center' src='".Mage::helper('manufacturer')->getUrlImagePath($manufacturer->getName()) .'/'. $manufacturer->getImagebanner2()."' width='50%' />
					<input style='text-align:center' id='imagebanner2' name='imagebanner2' type='file' />
					<input type='checkbox' name='imagebanner2[delete]'>Borrar Imagen
				</div>";
		$inputDefaultTextobanner2="";
		if ($storeId==2 && $manufacturer->getDefaultTextobanner2()==1){
			$inputDefaultTextobanner2="<input type='checkbox' onclick='habilitarInput(this)' name='default_textobanner2' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultTextobanner2()==0){
			$inputDefaultTextobanner2="<input type='checkbox' onclick='habilitarInput(this)' name='default_textobanner2' >";
		}
		$html.=$inputDefaultTextobanner2;
		$html.="<label for='textobanner2'>Texto Banner 2</label><input class='form-control' type='text' id='textobanner2' name='textobanner2' value='".$manufacturer->getTextobanner2()."'/>";
		$inputDefaultLinkbanner2="";
		if ($storeId==2 && $manufacturer->getDefaultLinkbanner2()==1){
			$inputDefaultLinkbanner2="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkbanner2' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultLinkbanner2()==0){
			$inputDefaultLinkbanner2="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkbanner2' >";
		}
		$html.=$inputDefaultLinkbanner2;
		$html.="<label for='linkbanner2'>Link Banner 2</label><input class='form-control' type='text' id='linkbanner2' name='linkbanner2' value='".$manufacturer->getLinkbanner2()."'/>";
		$html.="</div>";
		$html.="<div class='form-group col-xs-4 '>";
		$inputDefaultImagebanner3="";
		if ($storeId==2 && $manufacturer->getDefaultImagebanner3()==1){
			$inputDefaultImagebanner3="<input type='checkbox' onclick='habilitarInput(this)' name='default_imagebanner3' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultImagebanner3()==0){
			$inputDefaultImagebanner3="<input type='checkbox' onclick='habilitarInput(this)' name='default_imagebanner3' >";
		}
		$html.=$inputDefaultImagebanner3;
		$html.="<label for='imagebanner3'>Imagen Banner 3</label>
				<div style='width:100%;height:200px'>
					<img style='text-align:center' src='".Mage::helper('manufacturer')->getUrlImagePath($manufacturer->getName()) .'/'. $manufacturer->getImagebanner3()."' width='50%' />
					<input style='text-align:center' id='imagebanner3' name='imagebanner3' type='file' />
					<input type='checkbox' name='imagebanner3[delete]'>Borrar Imagen
				</div>";
		$inputDefaultTextobanner3="";
		if ($storeId==2 && $manufacturer->getDefaultTextobanner3()==1){
			$inputDefaultTextobanner3="<input type='checkbox' onclick='habilitarInput(this)' name='default_textobanner3' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultTextobanner3()==0){
			$inputDefaultTextobanner3="<input type='checkbox' onclick='habilitarInput(this)' name='default_textobanner3' >";
		}
		$html.=$inputDefaultTextobanner3;	
		$html.="<label for='textobanner3'>Texto Banner 3</label><input class='form-control' type='text' id='textobanner3' name='textobanner3' value='".$manufacturer->getTextobanner3()."'/>";
		$inputDefaultLinkbanner3="";
		if ($storeId==2 && $manufacturer->getDefaultLinkbanner3()==1){
			$inputDefaultLinkbanner3="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkbanner3' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultLinkbanner3()==0){
			$inputDefaultLinkbanner3="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkbanner3' >";
		}
		$html.=$inputDefaultLinkbanner3;
		$html.="<label for='linkbanner3'>Link Banner 3</label><input class='form-control' type='text' id='linkbanner3' name='linkbanner3' value='".$manufacturer->getLinkbanner3()."'/>";
		$html.="</div>";
		$html.="<div class='form-group col-xs-6 '>";
		$inputDefaultMorefor1="";
		if ($storeId==2 && $manufacturer->getDefaultMorefor1()==1){
			$inputDefaultMorefor1="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor1' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultMorefor1()==0){
			$inputDefaultMorefor1="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor1' >";
		}
		$html.=$inputDefaultMorefor1;
		$html.="<label for='morefor1'>Texto More for 1</label><input class='form-control' type='text' id='morefor1' name='morefor1' value='".$manufacturer->getMorefor1()."'/>";
		$inputDefaultLinkmorefor1="";
		if ($storeId==2 && $manufacturer->getDefaultLinkmorefor1()==1){
			$inputDefaultLinkmorefor1="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor1' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultLinkmorefor1()==0){
			$inputDefaultLinkmorefor1="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor1' >";
		}
		$html.=$inputDefaultLinkmorefor1;
		$html.="<label for='linkmorefor1'>Link More for 1</label><input class='form-control' type='text' id='linkmorefor1' name='linkmorefor1' value='".$manufacturer->getLinkmorefor1()."'/>";
		$html.="<p></p>";
		$inputDefaultMorefor2="";
		if ($storeId==2 && $manufacturer->getDefaultMorefor2()==1){
			$inputDefaultMorefor2="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor2' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultMorefor2()==0){
			$inputDefaultMorefor2="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor2' >";
		}
		$html.=$inputDefaultMorefor2;
		$html.="<label for='morefor2'>Texto More for 2</label><input class='form-control' type='text' id='morefor2' name='morefor2' value='".$manufacturer->getMorefor2()."'/>";
		$inputDefaultLinkmorefor2="";
		if ($storeId==2 && $manufacturer->getDefaultLinkmorefor2()==1){
			$inputDefaultLinkmorefor2="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor2' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultLinkmorefor2()==0){
			$inputDefaultLinkmorefor2="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor2' >";
		}
		$html.=$inputDefaultLinkmorefor2;
		$html.="<label for='linkmorefor2'>Link More for 2</label><input class='form-control' type='text' id='linkmorefor2' name='linkmorefor2' value='".$manufacturer->getLinkmorefor2()."'/>";
		$html.="<p></p>";
		$inputDefaultMorefor3="";
		if ($storeId==2 && $manufacturer->getDefaultMorefor3()==1){
			$inputDefaultMorefor3="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor3' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultMorefor3()==0){
			$inputDefaultMorefor3="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor3' >";
		}
		$html.=$inputDefaultMorefor3;
		$html.="<label for='morefor3'>Texto More for 3</label><input class='form-control' type='text' id='morefor3' name='morefor3' value='".$manufacturer->getMorefor3()."'/>";
		$inputDefaultLinkmorefor3="";
		if ($storeId==2 && $manufacturer->getDefaultLinkmorefor3()==1){
			$inputDefaultLinkmorefor3="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor3' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultLinkmorefor3()==0){
			$inputDefaultLinkmorefor3="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor3' >";
		}
		$html.=$inputDefaultLinkmorefor3;
		$html.="<label for='linkmorefor3'>Link More for 3</label><input class='form-control' type='text' id='linkmorefor3' name='linkmorefor3' value='".$manufacturer->getLinkmorefor3()."'/>";
		$html.="<p></p>";
		$inputDefaultMorefor4="";
		if ($storeId==2 && $manufacturer->getDefaultMorefor4()==1){
			$inputDefaultMorefor4="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor4' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultMorefor4()==0){
			$inputDefaultMorefor4="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor4' >";
		}
		$html.=$inputDefaultMorefor4;
		$html.="<label for='morefor4'>Texto More for 4</label><input class='form-control' type='text' id='morefor4' name='morefor4' value='".$manufacturer->getMorefor4()."'/>";
		$inputDefaultLinkmorefor4="";
		if ($storeId==2 && $manufacturer->getDefaultLinkmorefor4()==1){
			$inputDefaultLinkmorefor4="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor4' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultLinkmorefor4()==0){
			$inputDefaultLinkmorefor4="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor4' >";
		}
		$html.=$inputDefaultLinkmorefor4;
		$html.="<label for='linkmorefor4'>Link More for 4</label><input class='form-control' type='text' id='linkmorefor4' name='linkmorefor4' value='".$manufacturer->getLinkmorefor4()."'/>";
		$html.="</div>";
		$html.="<div class='form-group col-xs-6 '>";
		$inputDefaultMorefor5="";
		if ($storeId==2 && $manufacturer->getDefaultMorefor5()==1){
			$inputDefaultMorefor5="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor5' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultMorefor5()==0){
			$inputDefaultMorefor5="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor5' >";
		}
		$html.=$inputDefaultMorefor5;
		$html.="<label for='morefor5'>Texto More for 5</label><input class='form-control' type='text' id='morefor5' name='morefor5' value='".$manufacturer->getMorefor5()."'/>";
		$inputDefaultLinkmorefor5="";
		if ($storeId==2 && $manufacturer->getDefaultLinkmorefor5()==1){
			$inputDefaultLinkmorefor5="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor5' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultLinkmorefor5()==0){
			$inputDefaultLinkmorefor5="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor5' >";
		}
		$html.=$inputDefaultLinkmorefor5;
		$html.="<label for='linkmorefor5'>Link More for 5</label><input class='form-control' type='text' id='linkmorefor5' name='linkmorefor5' value='".$manufacturer->getLinkmorefor5()."'/>";
		$html.="<p></p>";
		$inputDefaultMorefor6="";
		if ($storeId==2 && $manufacturer->getDefaultMorefor6()==1){
			$inputDefaultMorefor6="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor6' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultMorefor6()==0){
			$inputDefaultMorefor6="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor6' >";
		}
		$html.=$inputDefaultMorefor6;
		$html.="<label for='morefor6'>Texto More for 6</label><input class='form-control' type='text' id='morefor6' name='morefor6' value='".$manufacturer->getMorefor6()."'/>";
		$inputDefaultLinkmorefor6="";
		if ($storeId==2 && $manufacturer->getDefaultLinkmorefor6()==1){
			$inputDefaultLinkmorefor6="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor6' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultLinkmorefor6()==0){
			$inputDefaultLinkmorefor6="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor6' >";
		}
		$html.=$inputDefaultLinkmorefor6;
		$html.="<label for='linkmorefor6'>Link More for 6</label><input class='form-control' type='text' id='linkmorefor6' name='linkmorefor6' value='".$manufacturer->getLinkmorefor6()."'/>";
		$html.="<p></p>";
		$inputDefaultMorefor7="";
		if ($storeId==2 && $manufacturer->getDefaultMorefor7()==1){
			$inputDefaultMorefor7="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor7' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultMorefor7()==0){
			$inputDefaultMorefor7="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor7' >";
		}
		$html.=$inputDefaultMorefor7;
		$html.="<label for='morefor7'>Texto More for 7</label><input class='form-control' type='text' id='morefor7' name='morefor7' value='".$manufacturer->getMorefor7()."'/>";
		$inputDefaultLinkmorefor7="";
		if ($storeId==2 && $manufacturer->getDefaultLinkmorefor7()==1){
			$inputDefaultLinkmorefor7="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor7' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultLinkmorefor7()==0){
			$inputDefaultLinkmorefor7="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor7' >";
		}
		$html.=$inputDefaultLinkmorefor7;
		$html.="<label for='linkmorefor7'>Link More for 7</label><input class='form-control' type='text' id='linkmorefor7' name='linkmorefor7' value='".$manufacturer->getLinkmorefor7()."'/>";
		$html.="<p></p>";
		$inputDefaultMorefor8="";
		if ($storeId==2 && $manufacturer->getDefaultMorefor8()==1){
			$inputDefaultMorefor8="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor8' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultMorefor8()==0){
			$inputDefaultMorefor8="<input type='checkbox' onclick='habilitarInput(this)' name='default_morefor8' >";
		}
		$html.=$inputDefaultMorefor8;
		$html.="<label for='morefor8'>Texto More for 8</label><input class='form-control' type='text' id='morefor8' name='morefor8' value='".$manufacturer->getMorefor8()."'/>";
		$inputDefaultLinkmorefor8="";
		if ($storeId==2 && $manufacturer->getDefaultLinkmorefor8()==1){
			$inputDefaultLinkmorefor8="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor8' checked >";
		}else if($storeId==2 && $manufacturer->getDefaultLinkmorefor8()==0){
			$inputDefaultLinkmorefor8="<input type='checkbox' onclick='habilitarInput(this)' name='default_linkmorefor8' >";
		}
		$html.=$inputDefaultLinkmorefor8;
		$html.="<label for='linkmorefor8'>Link More for 8</label><input class='form-control' type='text' id='linkmorefor8' name='linkmorefor8' value='".$manufacturer->getLinkmorefor8()."'/>";
		$html.="</div>";
		$html.="<button type='submit' class='btn btn-primary' id='guardar'>Guardar</button></form>";
	
	
	
	
	
	
	
	
	}elseif ($categoryId!="" && $marca!=""){ //Descripciones compuestas por marcas y categorias
		$manufacturer= Mage::getModel("manufacturer/manufacturer")->getManufacturerByOptionId($marca,$storeId);
		$descripcion="";
		$descripciones=explode(";",$manufacturer->getDescription());
		foreach ($descripciones as $candidata){
			if (explode("|",$candidata)[0]==$categoryId){
				$descripcion=explode("|",$candidata)[1];
			}
		
		}
		$html.="<label for='descripcion'>Bloque Descripci&oacute;n</label><p>(Prohibido utilizar los caract&eacute;res <i>;</i> y <i>|</i> )<textarea class='form-control' rows='10' name='descripcion'>".$descripcion."</textarea>";
		$html.="<button type='submit' class='btn btn-primary' id='guardar'>Guardar</button></form>";
	
	
	
	
	
	
	
	
	
	
	
	
	} else{
		$html="Debe seleccionar al menos una Categor&iacute;a o Marca";
	}
	$html.="<script>
	$(document).ready(function($) {
		
		$('#form-updateDescription input:checkbox').each(function(i,e){
			if ($(e).attr('checked')){
				$('#'+$(e).attr('name').split('_')[1]).attr('disabled',true);
			}
		});
		
		$('#form-updateDescription').submit(function(event){
			event.preventDefault();
			var data = new FormData($(this)[0]);
			/*
			$('#form-updateDescription input[type=\'file\']').each(function(i,file){
				data.append('file'+i,file);
			});
			*/
			var url='guardarDescripcion.php';
			$.ajax({
               url: url,
               type: 'POST',
               mimeType: 'multipart/form-data',
                   data: data,
               async: false,
               success: function (data) {
                    $('#guardar').html(data);             
               },
               error: function (){
                  
                  alert ('Error');
               },
               cache: false,
               contentType: false,
               processData: false
            });
		});
	});
	function habilitarInput(este){
		var name=jQuery(este).attr(\"name\");
		var identificador=name.split('_')[1];
		if (este.checked){
			$('#'+identificador).attr('disabled',true);
		}else{
			$('#'+identificador).attr('disabled',false);
		}
	}
	</script>";
	echo $html;