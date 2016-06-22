<?php
/**
* Product_import.php
* 
* @copyright  copyright (c) 2009 toniyecla[at]gmail.com
* @license    http://opensource.org/licenses/osl-3.0.php open software license (OSL 3.0)
*/
ini_set('memory_limit', '-1');
set_time_limit(0);

class Mage_Catalog_Model_Convert_Adapter_Productimport2
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
		
        //$product -> setData( array() );
        $product = new Mage_Catalog_Model_Product();
		//$product = $this -> getProductModel();
		$product -> setData( array() );
		
/* Añadimos este código para asignar las categorías correctas en función de la familia, sección y departamento de cada producto */
		if (isset($importData['departamento']) && isset($importData['seccion']) && isset($importData['familia'])){	
			switch ($importData['departamento']){
				case '1':
				
						switch ($importData['seccion']){
							case '1':
									switch ($importData['familia']){
										case '1':
												$categorias=array('23','24','25');
												$weight='2.0000';
												
											break;
										case '2':
												$categorias=array('23','24','26');
												$weight='0.5000';
											break;
										case '3':
												$categorias=array('23','24','27');
												$weight='2.0000';
											break;
										case '4':
												$categorias=array('23','24','36');
												$weight='2.0000';
											break;
										case '5':
												$categorias=array('23','24','30');
												$weight='2.0000';
											break;
										case '6':
												$categorias=array('23','24','35');
												$weight='2.0000';
											break;
										case '7':
												$categorias=array('23','24','31');
												$weight='2.0000';
											break;
										case '8':
												$categorias=array('23','24','29');
												$weight='2.0000';
											break;
										case '9':
												$categorias=array('23','24','37');
												$weight='3.0000';
											break;
										case '10':
												$categorias=array('23','24','38');
												$weight='0.5000';
											break;
										case '11':
												$categorias=array('23','24','155');
												$weight='0.5000';
											break;
										case '12':
												$categorias=array('23','24','32');
												$weight='3.0000';
											break;
										case '13':
												$categorias=array('23','24','28');
												$weight='2.0000';
											break;
										case '14':
												$categorias=array('23','24','40');
												$weight='2.0000';
											break;
										case '15':
												$categorias=array('23','24','33');
												$weight='2.0000';
											break;
										case '16':
												$categorias=array('23','24','42');
												$weight='2.0000';
											break;
										case '17':
												$categorias=array('23','24','44');
												$weight='3.0000';
											break;
									}
								break;
							case '2':
									switch ($importData['familia']){
										case '1':
												$categorias=array('23','45','49');
												$weight='0.5000';
											break;
										case '2':
												$categorias=array('23','45','47');
												$weight='0.5000';
											break;
										case '3':
												$categorias=array('23','45','46');
												$weight='0.5000';
											break;
										case '4':
												$categorias=array('23','45','54');
												$weight='0.5000';
											break;
										case '5':
												$categorias=array('23','45','53');
												$weight='0.5000';
											break;
										case '6':
												$categorias=array('23','45','48');
												$weight='0.5000';
											break;
										case '7':
												$categorias=array('23','45','156');
												$weight='0.5000';
											break;
										case '8':
												$categorias=array('23','45','57');
												$weight='0.5000';
											break;
									}
								break;
							case '3':
									switch ($importData['familia']){
										case '1':
												$categorias=array('23','59','60');
												$weight='3.0000';
											break;
										case '2':
												$categorias=array('23','59','176');
												$weight='4.0000';
											break;
										case '3':
												$categorias=array('23','59','177');
												$weight='3.0000';
											break;
										case '4':
												$categorias=array('23','59','178');
												$weight='3.0000';
											break;
										case '5':
												$categorias=array('23','59','179');
												$weight='3.0000';
											break;
										case '6':
												$categorias=array('23','59','180');
												$weight='3.0000';
											break;
										case '7':
												$categorias=array('23','59','181');
												$weight='3.0000';
											break;
										case '8':
												$categorias=array('23','59','182');
												$weight='3.0000';
											break;
									}
								break;
							case '4':
									switch ($importData['familia']){
										case '1':
												$categorias=array('23','61','152');
												$weight='3.0000';
											break;
										case '2':
												$categorias=array('23','61','183');
												$weight='3.0000';
											break;
										case '3':
												$categorias=array('23','61','184');
												$weight='3.0000';
											break;
										case '4':
												$categorias=array('23','61','185');
												$weight='2.0000';
											break;
										case '5':
												$categorias=array('23','61','186');
												$weight='3.0000';
											break;
										case '6':
												$categorias=array('23','61','187');
												$weight='2.0000';
											break;
										case '7':
												$categorias=array('23','61','188');
												$weight='2.0000';
											break;
										case '8':
												$categorias=array('23','61','189');
												$weight='2.0000';
											break;
										case '9':
												$categorias=array('23','61','190');
												$weight='2.0000';
											break;
									}
								break;
							}
					break;
				case '2':
						switch ($importData['seccion']){
							case '1':
									switch ($importData['familia']){
										case '1':
												$categorias=array('62','63','64');
												$weight='2.0000';
											break;
										case '2':
												$categorias=array('62','63','65');
												$weight='0.5000';
											break;
										case '3':
												$categorias=array('62','63','66');
												$weight='2.0000';
											break;
										case '4':
												$categorias=array('62','63','67');
												$weight='2.0000';
											break;
										case '5':
												$categorias=array('62','63','68');
												$weight='2.0000';
											break;
										case '6':
												$categorias=array('62','63','70');
												$weight='2.0000';
											break;
										case '7':
												$categorias=array('62','63','71');
												$weight='3.0000';
											break;
										case '8':
												$categorias=array('62','63','69');
												$weight='3.0000';
											break;
										case '9':
												$categorias=array('62','63','72');
												$weight='0.5000';
											break;
										case '10':
												$categorias=array('62','63','73');
												$weight='0.5000';
											break;
										case '11':
												$categorias=array('62','63','77');
												$weight='3.0000';
											break;
									}
								break;
							case '2':
									switch ($importData['familia']){
										case '1':
												$categorias=array('62','78','84');
												$weight='0.5000';
											break;
										case '2':
												$categorias=array('62','78','81');
												$weight='0.5000';
											break;
										case '3':
												$categorias=array('62','78','80');
												$weight='0.5000';
											break;
										case '4':
												$categorias=array('62','78','87');
												$weight='0.5000';
											break;
										case '5':
												$categorias=array('62','78','82');
												$weight='0.5000';
											break;
										case '6':
												$categorias=array('62','78','83');
												$weight='0.5000';
											break;
										case '7':
												$categorias=array('62','78','153');
												$weight='0.5000';
											break;
										case '8':
												$categorias=array('62','78','90');
												$weight='0.5000';
											break;
										case '9':
												$categorias=array('62','78','79');
												$weight='0.5000';
											break;
									}
								break;
							case '3':
									switch ($importData['familia']){
										case '1':
												$categorias=array('62','92','93');
												$weight='3.0000';
											break;
										case '2':
												$categorias=array('62','92','191');
												$weight='4.0000';
											break;
										case '3':
												$categorias=array('62','92','192');
												$weight='3.0000';
											break;
										case '4':
												$categorias=array('62','92','193');
												$weight='3.0000';
											break;
										case '5':
												$categorias=array('62','92','194');
												$weight='3.0000';
											break;
									}
								break;
							case '4':
									switch ($importData['familia']){
										case '1':
												$categorias=array('62','94','95');
												$weight='3.0000';
											break;
										case '2':
												$categorias=array('62','94','195');
												$weight='3.0000';
											break;
										case '3':
												$categorias=array('62','94','196');
												$weight='3.0000';
											break;
										case '4':
												$categorias=array('62','94','197');
												$weight='3.0000';
											break;
										case '5':
												$categorias=array('62','94','198');
												$weight='3.0000';
											break;
										case '6':
												$categorias=array('62','94','199');
												$weight='2.0000';
											break;
										case '7':
												$categorias=array('62','94','200');
												$weight='2.0000';
											break;
										case '8':
												$categorias=array('62','94','201');
												$weight='2.0000';
											break;
									}
								break;
							}
					break;
				case '4':
						switch ($importData['seccion']){
							case '1':
									switch ($importData['familia']){
										case '1':
												$categorias=array('101','171','102');
												$weight='0.5000';
											break;
										case '2':
												$categorias=array('101','171','104');
												$weight='0.5000';
											break;
										case '3':
												$categorias=array('101','171','105');
												$weight='0.5000';
											break;
										case '4':
												$categorias=array('101','171','120');
												$weight='0.5000';
											break;
										case '5':
												$categorias=array('101','171','121');
												$weight='0.5000';
											break;
										case '6':
												$categorias=array('101','171','116');
												$weight='0.5000';
											break;
										case '7':
												$categorias=array('101','171','115');
												$weight='0.5000';
											break;
										case '8':
												$categorias=array('101','171','119');
												$weight='3.0000';
											break;
										case '9':
												$categorias=array('101','171','112');
												$weight='2.0000';
											break;
										case '10':
												$categorias=array('101','171','167');
												$weight='0.5000';
											break;
										case '11':
												$categorias=array('101','171','168');
												$weight='2.0000';
											break;
										case '12':
												$categorias=array('101','171','111');
												$weight='0.5000';
											break;
										case '13':
												$categorias=array('101','171','110');
												$weight='2.0000';
											break;
										case '14':
												$categorias=array('101','171','117');
												$weight='2.0000';
											break;
										case '15':
												$categorias=array('101','171','113');
												$weight='2.0000';
											break;
										case '16':
												$categorias=array('101','171','118');
												$weight='0.5000';
											break;
									}
								break;
							case '2':
									switch ($importData['familia']){
										case '1':
												$categorias=array('101','161','162');
												$weight='0.5000';
											break;
										case '2':
												$categorias=array('101','161','163');
												$weight='0.5000';
											break;
									}
								break;
							case '3':
									switch ($importData['familia']){
										case '1':
												$categorias=array('101','158','159');
												$weight='0.5000';												
											break;
										case '2':
												$categorias=array('101','158','160');
												$weight='0.5000';			
											break;
									}
								break;
							case '4':
									switch ($importData['familia']){
										case '1':
												$categorias=array('101','106','169');
												$weight='0.5000';
											break;
										case '2':
												$categorias=array('101','106','170');
												$weight='0.5000';
											break;
										case '3':
												$categorias=array('101','106','108');
												$weight='2.0000';
											break;
										case '4':
												$categorias=array('101','106','109');
												$weight='0.5000';
											break;
										case '5':
												$categorias=array('101','106','205');
												$weight='0.5000';
										}
								break;
							case '5':
									switch ($importData['familia']){
										case '1':
												$categorias=array('101','96','164');
												$weight='2.0000';
											break;
										case '2':
												$categorias=array('101','96','97');
												$weight='2.0000';
											break;
										case '3':
												$categorias=array('101','96','166');
												$weight='2.0000';
											break;
										case '4':
												$categorias=array('101','96','165');
												$weight='4.0000';
											break;
										case '5':
												$categorias=array('101','96','99');
												$weight='4.0000';
											break;
										}
								break;	
							}
					break;
				case '7':
						switch ($importData['seccion']){
							case '1':
									switch ($importData['familia']){
										case '1':
												$categorias=array('122','123','124');
												$weight='2.0000';
											break;
										case '2':
												$categorias=array('122','123','125');
												$weight='0.5000';
											break;
										case '3':
												$categorias=array('122','123','126');
												$weight='2.0000';
											break;
										case '4':
												$categorias=array('122','123','127');
												$weight='2.0000';
											break;
										case '5':
												$categorias=array('122','123','128');
												$weight='2.0000';
											break;
										case '6':
												$categorias=array('122','123','129');
												$weight='2.0000';
											break;
										case '7':
												$categorias=array('122','123','130');
												$weight='2.0000';
											break;
										case '8':
												$categorias=array('122','123','132');
												$weight='2.0000';
											break;
										case '9':
												$categorias=array('122','123','133');
												$weight='2.0000';
											break;
										case '10':
												$categorias=array('122','123','134');
												$weight='2.0000';
											break;
										case '11':
												$categorias=array('122','123','135');
												$weight='3.0000';
											break;
										case '12':
												$categorias=array('122','123','136');
												$weight='0.5000';
											break;
										case '13':
												$categorias=array('122','123','137');
												$weight='0.5000';
											break;
										case '14':
												$categorias=array('122','123','138');
												$weight='2.0000';
											break;
										case '15':
												$categorias=array('122','123','140');
												$weight='2.0000';
											break;
									}
								break;
							case '2':
									switch ($importData['familia']){
										case '1':
												$categorias=array('122','142','144');
												$weight='0.5000';
											break;
										case '2':
												$categorias=array('122','142','145');
												$weight='0.5000';
											break;
									}
								break;
							case '3':
									switch ($importData['familia']){
										case '1':
												$categorias=array('122','148','149');
												$weight='3.0000';
											break;
									}
								break;
							case '4':
									switch ($importData['familia']){
										case '1':
												$categorias=array('122','150','151');
												$weight='3.0000';
											break;
										
										}
								break;		
							}
					break;
				}
			
			
						
	}
	
		
        if ( $stockItem = $product -> getStockItem() ) {
            $stockItem -> setData( array() );
            } 
        
        if ( empty( $importData['store'] ) ) {
            if ( !is_null( $this -> getBatchParams( 'store' ) ) ) {
                $store = $this -> getStoreById( $this -> getBatchParams( 'store' ) );
                } else {
                $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, required field "%s" not defined', 'store' );
                Mage :: throwException( $message );
                } 
            } else {
            $store = $this -> getStoreByCode( $importData['store'] );
            } 
        
        if ( $store === false ) {
            $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, store "%s" field not exists', $importData['store'] );
            Mage :: throwException( $message );
            } 
        
        if ( empty( $importData['sku'] ) ) {
            $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, required field "%s" not defined', 'sku' );
            Mage :: throwException( $message );
            } 
        
        $product -> setStoreId( $store -> getId() );
        $productId = $product -> getIdBySku( $importData['sku'] );
        $new = true; // fix for duplicating attributes error
        if ( $productId ) {
            $product -> load( $productId );
            $new = false; // fix for duplicating attributes error
            } 
        $productTypes = $this -> getProductTypes();
        $productAttributeSets = $this -> getProductAttributeSets();
        
        // delete disabled products
        if ( $importData['status'] == 'Disabled' ) {
            $product = Mage :: getSingleton( 'catalog/product' ) -> load( $productId );
            $this -> _removeFile( Mage :: getSingleton( 'catalog/product_media_config' ) -> getMediaPath( $product -> getData( 'image' ) ) );
            $this -> _removeFile( Mage :: getSingleton( 'catalog/product_media_config' ) -> getMediaPath( $product -> getData( 'small_image' ) ) );
            $this -> _removeFile( Mage :: getSingleton( 'catalog/product_media_config' ) -> getMediaPath( $product -> getData( 'thumbnail' ) ) );
            $media_gallery = $product -> getData( 'media_gallery' );
            foreach ( $media_gallery['images'] as $image ) {
                $this -> _removeFile( Mage :: getSingleton( 'catalog/product_media_config' ) -> getMediaPath( $image['file'] ) );
                } 
            $product -> delete();
            return true;
            } 
        
        if ( empty( $importData['type'] ) || !isset( $productTypes[strtolower( $importData['type'] )] ) ) {
            $value = isset( $importData['type'] ) ? $importData['type'] : '';
            $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, is not valid value "%s" for field "%s"', $value, 'type' );
            Mage :: throwException( $message );
            } 
        $product -> setTypeId( $productTypes[strtolower( $importData['type'] )] );
        
        if ( empty( $importData['attribute_set'] ) || !isset( $productAttributeSets[$importData['attribute_set']] ) ) {
            $value = isset( $importData['attribute_set'] ) ? $importData['attribute_set'] : '';
            $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, is not valid value "%s" for field "%s"', $value, 'attribute_set' );
            Mage :: throwException( $message );
            } 
        $product -> setAttributeSetId( $productAttributeSets[$importData['attribute_set']] );
        
        foreach ( $this -> _requiredFields as $field ) {
            $attribute = $this -> getAttribute( $field );
            if ( !isset( $importData[$field] ) && $attribute && $attribute -> getIsRequired() ) {
                $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, required field "%s" for new products not defined', $field );
                Mage :: throwException( $message );
                } 
            } 
        if ( $importData['type'] == 'configurable' ) {
            $product -> setCanSaveConfigurableAttributes( true );
            $configAttributeCodes = $this -> userCSVDataAsArray( $importData['config_attributes'] );
            $usingAttributeIds = array();
            foreach( $configAttributeCodes as $attributeCode ) {
                $attribute = $product -> getResource() -> getAttribute( $attributeCode );
                if ( $product -> getTypeInstance() -> canUseAttribute( $attribute ) ) {
                    if ( $new ) { // fix for duplicating attributes error
                        $usingAttributeIds[] = $attribute -> getAttributeId();
                        } 
                    } 
                } 
            if ( !empty( $usingAttributeIds ) ) {
                $product -> getTypeInstance() -> setUsedProductAttributeIds( $usingAttributeIds );
                $product -> setConfigurableAttributesData( $product -> getTypeInstance() -> getConfigurableAttributesAsArray() );
                $product -> setCanSaveConfigurableAttributes( true );
                $product -> setCanSaveCustomOptions( true );
                } 
            
		}
        //************************************************************************************************************
	
		if ( isset( $importData['related'] ) ) {
            $linkIds = $this -> skusToIds( $importData['related'], $product );
            if ( !empty( $linkIds ) ) {
                $product -> setRelatedLinkData( $linkIds );
                } 
            } 
        
        if ( isset( $importData['upsell'] ) ) {
            $linkIds = $this -> skusToIds( $importData['upsell'], $product );
            if ( !empty( $linkIds ) ) {
                $product -> setUpSellLinkData( $linkIds );
                } 
            } 
        
        if ( isset( $importData['crosssell'] ) ) {
            $linkIds = $this -> skusToIds( $importData['crosssell'], $product );
            if ( !empty( $linkIds ) ) {
                $product -> setCrossSellLinkData( $linkIds );
                } 
            } 
        
        if ( isset( $importData['grouped'] ) ) {
            $linkIds = $this -> skusToIds( $importData['grouped'], $product );
            if ( !empty( $linkIds ) ) {
                $product -> setGroupedLinkData( $linkIds );
                } 
            } 
        
        if ( isset( $importData['category_ids'] ) ) {
            $product -> setCategoryIds( $importData['category_ids'] );
            } 
        
        
        if ( isset( $importData['categories'] ) ) {
            
            if ( isset( $importData['store'] ) ) {
                $cat_store = $this -> _stores[$importData['store']];
                } else {
                $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, required field "store" for new products not defined', $field );
                Mage :: throwException( $message );
                } 
            
            $categoryIds = $this -> _addCategories( $importData['categories'], $cat_store );
            if ( $categoryIds ) {
                $product -> setCategoryIds( $categoryIds );
                } 
            
            } 
        
        foreach ( $this -> _ignoreFields as $field ) {
            if ( isset( $importData[$field] ) ) {
                unset( $importData[$field] );
                } 
            } 
        
        if ( $store -> getId() != 0 ) {
            $websiteIds = $product -> getWebsiteIds();
            if ( !is_array( $websiteIds ) ) {
                $websiteIds = array();
                } 
            if ( !in_array( $store -> getWebsiteId(), $websiteIds ) ) {
                $websiteIds[] = $store -> getWebsiteId();
                } 
            $product -> setWebsiteIds( $websiteIds );
            } 
        
        if ( isset( $importData['websites'] ) ) {
            $websiteIds = $product -> getWebsiteIds();
            if ( !is_array( $websiteIds ) ) {
                $websiteIds = array();
                } 
            $websiteCodes = split( ',', $importData['websites'] );
            foreach ( $websiteCodes as $websiteCode ) {
                try {
                    $website = Mage :: app() -> getWebsite( trim( $websiteCode ) );
                    if ( !in_array( $website -> getId(), $websiteIds ) ) {
                        $websiteIds[] = $website -> getId();
                        } 
                    } 
                catch ( Exception $e ) {
                    } 
                } 
            $product -> setWebsiteIds( $websiteIds );
            unset( $websiteIds );
            } 
        
        foreach ( $importData as $field => $value ) {
            //if ( in_array( $field, $this -> _inventorySimpleFields ) ) {
            if ( in_array( $field, $this -> _inventoryFields ) ) { 
                continue;
                } 
            if ( in_array( $field, $this -> _imageFields ) ) {
                continue;
                } 
            
			$attribute = $this -> getAttribute( $field );
            if ( !$attribute ) {
                continue;
                } 
            
            $isArray = false;
            $setValue = $value;
            
            if ( $attribute -> getFrontendInput() == 'multiselect' ) {
                $value = split( self :: MULTI_DELIMITER, $value );
                $isArray = true;
                $setValue = array();
                } 
            
            if ( $value && $attribute -> getBackendType() == 'decimal' ) {
                $setValue = $this -> getNumber( $value );
                } 
            
            if ( $attribute -> usesSource() ) {
                $options = $attribute -> getSource() -> getAllOptions( false );
                
                if ( $isArray ) {
                    foreach ( $options as $item ) {
                        if ( in_array( $item['label'], $value ) ) {
                            $setValue[] = $item['value'];
                            } 
                        } 
                    } 
                else {
                    $setValue = null;
                    foreach ( $options as $item ) {
                        if ( $item['label'] == $value ) {
                            $setValue = $item['value'];
                            } 
                        } 
                    } 
                } 
            
            $product -> setData( $field, $setValue );
            } 
        
        if ( !$product -> getVisibility() ) {
            $product -> setVisibility( Mage_Catalog_Model_Product_Visibility :: VISIBILITY_NOT_VISIBLE );
            } 
        
        $stockData = array();
        //$inventoryFields = $product -> getTypeId() == 'simple' ? $this -> _inventorySimpleFields : $this -> _inventoryOtherFields; 
        $inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()])
            ? $this->_inventoryFieldsProductTypes[$product->getTypeId()]
            : array(); 
            
        foreach ( $inventoryFields as $field ) {
        	//echo "entro simple";
            if ( isset( $importData[$field] ) ) {
                if ( in_array( $field, $this -> _toNumber ) ) {
                    $stockData[$field] = $this -> getNumber( $importData[$field] );
                    } 
                else {
                    $stockData[$field] = $importData[$field];
                    } 
                } 
            } 
        $product -> setStockData( $stockData );
        
        $imageData = array();
        foreach ( $this -> _imageFields as $field ) {
            if ( !empty( $importData[$field] ) && $importData[$field] != 'no_selection' ) {
                if ( !isset( $imageData[$importData[$field]] ) ) {
                    $imageData[$importData[$field]] = array();
                    } 
                $imageData[$importData[$field]][] = $field;
                } 
            } 
        
        foreach ( $imageData as $file => $fields ) {
            try {
                $product -> addImageToMediaGallery( Mage :: getBaseDir( 'media' ) . DS . 'import/' . $file, $fields, false );
                } 
            catch ( Exception $e ) {
                } 
            } 
        
        
        
        if ( !empty( $importData['gallery'] ) ) {
            $galleryData = explode( ',', $importData["gallery"] );
            foreach( $galleryData as $gallery_img ) {
                try {
                    $product -> addImageToMediaGallery( Mage :: getBaseDir( 'media' ) . DS . 'import' . $gallery_img, null, false, false );
                    } 
                catch ( Exception $e ) {
                    } 
                } 
            } 
        
		$product->setData('weight',$weight);
		//*****************************************************************
		
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
    