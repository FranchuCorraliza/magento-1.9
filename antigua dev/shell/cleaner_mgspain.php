<?php
set_time_limit(0);
require_once 'abstract.php';
require_once "../app/Mage.php";
Mage::app();
umask(0);  


class Ordenacion extends Mage_Shell_Abstract
{
    protected $_argname = array();
    public function __construct(){
		parent::__construct();
		//db connect
		$read  = Mage::getSingleton('core/resource')->getConnection('core_read');
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');


		
		$category_base 	= $this->getArg('id'); 
		$store_id   	= $this->getArg('store');
		
		if(!is_numeric($store_id) or !is_numeric($category_base)):		//SINO TENEMOS PARAMETRO EXIT 
			exit("Por favor introduce un valor numerico en el valor store e id \n\n");
		endif;
		
		
		Mage::app()->setCurrentStore($store_id);
		
		$categories = $this->retrieveAllChilds($category_base);
		
		

		array_push($categories,$category_base); //add base category to array

		echo "TOTAL DE CATEGORIAS A ACTUALIZAR : ".count($categories)." \n";
		$j = 0; 

		foreach($categories as $category_id):
			$category = Mage::getModel('catalog/category')->load($category_id);
			echo " \n--> Limpiamos (".$category->getId().") ".$category->getName();
			$this->category_clear($category_id);
			if($this->getArg('reorder') == "s"):
				//echo " --> Ordenamos ".$category->getName()." (".$category_id.") \n";
				//$this->category_order($category_id);
			endif;
			$j++;
		endforeach;

		echo "\n fin del proceso, categorias actualizadas: ".$j;
    }
    // Shell script point of entry
    public function run(){
    }
    // Usage instructions
	public function usageHelp(){
        return "Uso del script: \n Ejecutar el script con 'php reordenar.php --id id de la categoria a actualizar' \n Ejemplo: php reordenar.php --id 10 \n Con el parametro --reorder a 's' reordena los productos\n\n";
    }
	
	
	function retrieveAllChilds($id = null, $childs = null) {
		$category = Mage::getModel('catalog/category')->load($id);
		return $category->getResource()->getChildren($category, true);
	}
	 
	function category_order($id_categoria){
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$products =  Mage::getModel('ordenarcategorias/items');
		$collection = $products->getIds($id_categoria,'4');
		
		$contador = "0";
		foreach ($collection as $product) {
			$contador++;
			//update position in category 
			$query = "update  `catalog_category_product` set position='".$contador."' WHERE `category_id` = ".$id_categoria." and product_id='".$product->getId()."' limit 1";
			$write->query($query);
			//if($contador==100): exit('paramos'); endif;
		}
		unset($collection);
	}  
	  
	function category_clear($id_categoria){  
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		//$products =  Mage::getModel('ordenarcategorias/items');
		$_testproductCollection = $this->getIds($id_categoria,'1');
		 
		echo " -> Total a actualizar ".count($_testproductCollection)."  \t"; 

		foreach($_testproductCollection as $product){ 
		 	 $query = "delete from `catalog_category_product` WHERE `category_id` = ".$id_categoria." 	and product_id = ".$product->getId()." limit 1";
			 $write->query($query);
    	}

		unset($_testproductCollection);
		
		
	}
	
	function getIds($id_categoria,$visibility){   
		$_testproductCollection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToFilter("visibility", array("in" => array($visibility)))
            ->addAttributeToSort('entity_id', 'DESC')
            ->joinField('position',
                'catalog/category_product',
                'position',
                'product_id=entity_id',
                'category_id='.(int) $id_categoria,
                'inner');
                
     	return $_testproductCollection;

	}
	
}
// Instantiate 
$shell = new Ordenacion();
// Initiate script
$shell->run();
