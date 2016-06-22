<?php
/**
* Product_import.php
* 
* @copyright  copyright (c) 2009 toniyecla[at]gmail.com
* @license    http://opensource.org/licenses/osl-3.0.php open software license (OSL 3.0)
*/
ini_set('memory_limit', '-1');
set_time_limit(0);

class Mage_Catalog_Model_Convert_Adapter_Productimport5
extends Mage_Catalog_Model_Convert_Adapter_Product
 {
    
    /**
    * Save product (import)
    * 
    * @param array $importData 
    * @throws Mage_Core_Exception
    * @return bool 
    */
    public function saveRow( array $importData )
    
    {
		
        $product = new Mage_Catalog_Model_Product();
 		$product -> setData( array() );
		$product->setStoreId(0);
 		$productId = $product -> getIdBySku( $importData['sku'] );
 		$product -> load( $productId );
		
		if (isset($importData['manufacturer']) && $importData['manufacturer']!=''){
			$attribute_model = Mage::getModel('eav/entity_attribute');
			$attribute_options_model = Mage::getModel('eav/entity_attribute_source_table');
			$attribute_code = $attribute_model->getIdByCode('catalog_product', 'manufacturer');
			$attribute = $attribute_model->load($attribute_code);
			$attribute_options_model->setAttribute($attribute);
			$options = $attribute_options_model->getAllOptions(false);
			// determine if this option exists
			$value_exists = false;
			foreach($options as $option) {
				if ($option['label'] == $importData['manufacturer']) {
					$value_exists = true;
					break;
				}
			}
			if (!$value_exists) {
				$attribute->setData('option', array(
					'value' => array(
						'option' => array($importData['manufacturer'],$importData['manufacturer'])
					)
				));
				$attribute->save();
			}
		}
		$manufacturer = $this->getAttributeValue('Manufacturer',$importData["manufacturer"]);
		$product->setManufacturer($manufacturer);
		$product -> save();
		
		
		return true;
        } 
		
	public function getAttributeValue($arg_attribute, $arg_option_id){
    	$arg_option_id = trim($arg_option_id);   	
	    $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product',$arg_attribute);
		$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attributeId);		
		//echo "buscamos :".$arg_option_id."<br>";
		foreach ( $attribute->getSource()->getAllOptions(true, true) as $option){
			if($option['label']==$arg_option_id):
				return $option['value'];			
			endif;
		}				
		return "99999"; //no insertara el valor
		//exit('error linea 385 no tenemos datos del id de la opcion: '.$arg_attribute." - ".$arg_option_id);
		
    }	
		
    
    protected function userCSVDataAsArray( $data )
    
    {
       //return explode( ',', str_replace( " ", "", $data ) );
       return explode( ',',$data);
        } 
    
    protected function skusToIds( $userData, $product )
    
    {
        $productIds = array();
        
        foreach ($this->userCSVDataAsArray($userData) as $oneSku ) {
        	//print_r($oneSku);
            if ( ( $a_sku = ( int )$product -> getIdBySku( $oneSku ) ) > 0 ) {

                parse_str( "position=", $productIds[$a_sku] );
                } 
            } 
        return $productIds;
        } 
    
    protected $_categoryCache = array();
    
    protected function _addCategories( $categories, $store )
    
    {
        // $rootId = $store->getRootCategoryId();
        // $rootId = Mage::app()->getStore()->getRootCategoryId();
        $rootId = 2; // our store's root category id
        if ( !$rootId ) {
            return array();
            } 
        $rootPath = '1/' . $rootId;
        if ( empty( $this -> _categoryCache[$store -> getId()] ) ) {
            $collection = Mage :: getModel( 'catalog/category' ) -> getCollection()
             -> setStore( $store )
             -> addAttributeToSelect( 'name' );
            $collection -> getSelect() -> where( "path like '" . $rootPath . "/%'" );
            
            foreach ( $collection as $cat ) {
                try {
                    $pathArr = explode( '/', $cat -> getPath() );
                    $namePath = '';
                    for ( $i = 2, $l = sizeof( $pathArr ); $i < $l; $i++ ) {
                        $name = $collection -> getItemById( $pathArr[$i] ) -> getName();
                        $namePath .= ( empty( $namePath ) ? '' : '/' ) . trim( $name );
                        } 
                    $cat -> setNamePath( $namePath );
                    } 
                catch ( Exception $e ) {
                    echo "ERROR: Cat - ";
                    print_r( $cat );
                    continue;
                    } 
                } 
            
            $cache = array();
            foreach ( $collection as $cat ) {
                $cache[strtolower( $cat -> getNamePath() )] = $cat;
                $cat -> unsNamePath();
                } 
            $this -> _categoryCache[$store -> getId()] = $cache;
            } 
        $cache = &$this -> _categoryCache[$store -> getId()];
        
        $catIds = array();
        foreach ( explode( ',', $categories ) as $categoryPathStr ) {
            //COMENTAMOS ESTO PORQUE ESTA DANDO PROBLEMAS CON LAS CATEGORIAS CON S AL FINAL
			$categoryPathStr = preg_replace( '#s*/s*#', '/', trim( $categoryPathStr ) );
            if ( !empty( $cache[$categoryPathStr] ) ) {
                $catIds[] = $cache[$categoryPathStr] -> getId();
                continue;
                } 
            $path = $rootPath;
            $namePath = '';
            foreach ( explode( '/', $categoryPathStr ) as $catName ) {
                $namePath .= ( empty( $namePath ) ? '' : '/' ) . strtolower( $catName );
                if ( empty( $cache[$namePath] ) ) {
                    $cat = Mage :: getModel( 'catalog/category' )
                     -> setStoreId( $store -> getId() )
                     -> setPath( $path )
                     -> setName( $catName )
                     -> setIsActive( 1 )
                     -> save();
                    $cache[$namePath] = $cat;
                    } 
                $catId = $cache[$namePath] -> getId();
                $path .= '/' . $catId;
                } 
            if ( $catId ) {
                $catIds[] = $catId;
                } 
            } 
        return join( ',', $catIds );
        } 
    
    protected function _removeFile( $file ){
        if ( file_exists( $file ) ) {
            if ( unlink( $file ) ) {
                return true;
                } 
            } 
        return false;
        }
		
}
?>
    