<?php 
require_once "../app/Mage.php";
Mage::app();
umask(0);


Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

function getCategoriasHijas($category){
	$arbol=array();
	if ($category->getIsActive()):
		$hijos=$category->getChildrenCategories();
		if (count($hijos)>0):
			foreach ($hijos as $hijo):
				$arbol[]=$hijo->getId();
				$arbol=array_merge($arbol,getCategoriasHijas($hijo));
			endforeach;
		else:
			$arbol[]=$category->getId();
		endif;
	endif;
	
	return $arbol; 
}

$category = Mage::getModel('catalog/category')->load(1002);
$arbol=array();
$arbol=getCategoriasHijas($category);
echo '<br/>Categorias Hijas de Women: '.implode(',',$arbol);

$category = Mage::getModel('catalog/category')->load(1072);
$arbol=array();
$arbol=getCategoriasHijas($category);
echo '<br/>Categorias Hijas de Men: '.implode(',',$arbol);

$category = Mage::getModel('catalog/category')->load(1139);
$arbol=array();
$arbol=getCategoriasHijas($category);
echo '<br/>Categorias Hijas de Kids: '.implode(',',$arbol);

$category = Mage::getModel('catalog/category')->load(1150);
$arbol=array();
$arbol=getCategoriasHijas($category);
echo '<br/>Categorias Hijas de Sale: '.implode(',',$arbol);

$category = Mage::getModel('catalog/category')->load(1298);
$arbol=array();
$arbol=getCategoriasHijas($category);
echo '<br/>Categorias Hijas de Outlet: '.implode(',',$arbol);