<?php
/**
* Product_import.php
* 
* @copyright  copyright (c) 2009 toniyecla[at]gmail.com
* @license    http://opensource.org/licenses/osl-3.0.php open software license (OSL 3.0)
*/
ini_set('memory_limit', '-1');
set_time_limit(0);

class Mage_Catalog_Model_Convert_Adapter_Productimport
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
		
		/********************************************************************************************************************************/
		/* Añadimos este código para asignar las categorías correctas en función de la familia, sección y departamento de cada producto */
		if (isset($importData['departamento']) && isset($importData['seccion']) && isset($importData['familia'])){	
			switch ($importData['departamento']){
				case '1':
				
						switch ($importData['seccion']){
							case '1':
									switch ($importData['familia']){
										case '1':
												$categorias=array('23','24','25');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='210';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='383';
												}
											break;
										case '2':
												$categorias=array('23','24','26');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='211';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='384';
												}
											break;
										case '3':
												$categorias=array('23','24','27');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='212';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='385';
												}
											break;
										case '4':
												$categorias=array('23','24','36');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='213';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='386';
												}
											break;
										case '5':
												$categorias=array('23','24','30');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='214';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='387';
												}
											break;
										case '6':
												$categorias=array('23','24','35');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='215';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='388';
												}
											break;
										case '7':
												$categorias=array('23','24','31');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='216';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='389';
												}
											break;
										case '8':
												$categorias=array('23','24','29');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='217';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='390';
												}
											break;
										case '9':
												$categorias=array('23','24','37');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='218';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='391';
												}
											break;
										case '10':
												$categorias=array('23','24','38');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='219';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='392';
												}
											break;
										case '11':
												$categorias=array('23','24','155');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='220';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='393';
												}
											break;
										case '12':
												$categorias=array('23','24','32');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='221';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='394';
												}
											break;
										case '13':
												$categorias=array('23','24','28');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='222';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='395';
												}
											break;
										case '14':
												$categorias=array('23','24','40');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='226';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='396';
												}
											break;
										case '15':
												$categorias=array('23','24','33');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='228';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='397';
												}
											break;
										case '16':
												$categorias=array('23','24','42');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='230';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='398';
												}
											break;
										case '17':
												$categorias=array('23','24','44');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='209';
													$categorias[]='235';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='366';
													$categorias[]='399';
												}
											break;
									}
								break;
							case '2':
									switch ($importData['familia']){
										case '1':
												$categorias=array('23','45','49');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='236';
													$categorias[]='238';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='367';
													$categorias[]='400';
												}
											break;
										case '2':
												$categorias=array('23','45','47');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='236';
													$categorias[]='239';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='367';
													$categorias[]='401';
												}
											break;
										case '3':
												$categorias=array('23','45','46');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='236';
													$categorias[]='240';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='367';
													$categorias[]='402';
												}
											break;
										case '4':
												$categorias=array('23','45','54');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='236';
													$categorias[]='241';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='367';
													$categorias[]='403';
												}
											break;
										case '5':
												$categorias=array('23','45','53');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='236';
													$categorias[]='242';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='367';
													$categorias[]='404';
												}
											break;
										case '6':
												$categorias=array('23','45','48');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='236';
													$categorias[]='243';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='367';
													$categorias[]='405';
												}
											break;
										case '7':
												$categorias=array('23','45','156');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='236';
													$categorias[]='244';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='367';
													$categorias[]='406';
												}
											break;
										case '8':
												$categorias=array('23','45','57');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='236';
													$categorias[]='245';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='367';
													$categorias[]='407';
												}
											break;
									}
								break;
							case '3':
									switch ($importData['familia']){
										case '1':
												$categorias=array('23','59','60');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='237';
													$categorias[]='246';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='368';
													$categorias[]='408';
												}
											break;
										case '2':
												$categorias=array('23','59','176');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='237';
													$categorias[]='247';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='368';
													$categorias[]='409';
												}
											break;
										case '3':
												$categorias=array('23','59','177');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='237';
													$categorias[]='248';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='368';
													$categorias[]='410';
												}
											break;
										case '4':
												$categorias=array('23','59','178');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='237';
													$categorias[]='249';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='368';
													$categorias[]='411';
												}
											break;
										case '5':
												$categorias=array('23','59','179');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='237';
													$categorias[]='250';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='368';
													$categorias[]='412';
												}
											break;
										case '6':
												$categorias=array('23','59','180');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='237';
													$categorias[]='251';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='368';
													$categorias[]='413';
												}
											break;
										case '7':
												$categorias=array('23','59','181');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='237';
													$categorias[]='252';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='368';
													$categorias[]='414';
												}
											break;
										case '8':
												$categorias=array('23','59','182');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='237';
													$categorias[]='253';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='368';
													$categorias[]='415';
												}
											break;
									}
								break;
							case '4':
									switch ($importData['familia']){
										case '1':
												$categorias=array('23','61','152');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='254';
													$categorias[]='255';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='369';
													$categorias[]='416';
												}
											break;
										case '2':
												$categorias=array('23','61','183');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='254';
													$categorias[]='256';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='369';
													$categorias[]='417';
												}
											break;
										case '3':
												$categorias=array('23','61','184');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='254';
													$categorias[]='257';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='369';
													$categorias[]='418';
												}
											break;
										case '4':
												$categorias=array('23','61','185');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='254';
													$categorias[]='258';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='369';
													$categorias[]='419';
												}
											break;
										case '5':
												$categorias=array('23','61','186');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='254';
													$categorias[]='259';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='369';
													$categorias[]='420';
												}
											break;
										case '6':
												$categorias=array('23','61','187');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='254';
													$categorias[]='260';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='369';
													$categorias[]='421';
												}
											break;
										case '7':
												$categorias=array('23','61','188');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='254';
													$categorias[]='261';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='369';
													$categorias[]='422';
												}
											break;
										case '8':
												$categorias=array('23','61','189');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='254';
													$categorias[]='262';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='369';
													$categorias[]='423';
												}
											break;
										case '9':
												$categorias=array('23','61','190');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='208';
													$categorias[]='254';
													$categorias[]='263';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='362';
													$categorias[]='369';
													$categorias[]='424';
												}
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
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='265';
													$categorias[]='266';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='370';
													$categorias[]='425';
												}
											break;
										case '2':
												$categorias=array('62','63','65');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='265';
													$categorias[]='267';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='370';
													$categorias[]='426';
												}
											break;
										case '3':
												$categorias=array('62','63','66');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='265';
													$categorias[]='268';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='370';
													$categorias[]='427';
												}
											break;
										case '4':
												$categorias=array('62','63','67');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='265';
													$categorias[]='269';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='370';
													$categorias[]='428';
												}
											break;
										case '5':
												$categorias=array('62','63','68');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='265';
													$categorias[]='270';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='370';
													$categorias[]='429';
												}
											break;
										case '6':
												$categorias=array('62','63','70');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='265';
													$categorias[]='271';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='370';
													$categorias[]='430';
												}
											break;
										case '7':
												$categorias=array('62','63','71');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='265';
													$categorias[]='272';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='370';
													$categorias[]='431';
												}
											break;
										case '8':
												$categorias=array('62','63','69');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='265';
													$categorias[]='273';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='370';
													$categorias[]='432';
												}
											break;
										case '9':
												$categorias=array('62','63','72');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='265';
													$categorias[]='274';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='370';
													$categorias[]='433';
												}
											break;
										case '10':
												$categorias=array('62','63','73');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='265';
													$categorias[]='275';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='370';
													$categorias[]='434';
												}
											break;
										case '11':
												$categorias=array('62','63','77');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='265';
													$categorias[]='276';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='370';
													$categorias[]='435';
												}
											break;
									}
								break;
							case '2':
									switch ($importData['familia']){
										case '1':
												$categorias=array('62','78','84');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='277';
													$categorias[]='278';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='371';
													$categorias[]='436';
												}
											break;
										case '2':
												$categorias=array('62','78','81');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='277';
													$categorias[]='279';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='371';
													$categorias[]='437';
												}
											break;
										case '3':
												$categorias=array('62','78','80');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='277';
													$categorias[]='280';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='371';
													$categorias[]='438';
												}
											break;
										case '4':
												$categorias=array('62','78','87');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='277';
													$categorias[]='281';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='371';
													$categorias[]='439';
												}
											break;
										case '5':
												$categorias=array('62','78','82');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='277';
													$categorias[]='282';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='371';
													$categorias[]='440';
												}
											break;
										case '6':
												$categorias=array('62','78','83');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='277';
													$categorias[]='283';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='371';
													$categorias[]='441';
												}
											break;
										case '7':
												$categorias=array('62','78','153');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='277';
													$categorias[]='284';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='371';
													$categorias[]='442';
												}
											break;
										case '8':
												$categorias=array('62','78','90');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='277';
													$categorias[]='285';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='371';
													$categorias[]='443';
												}
											break;
										case '9':
												$categorias=array('62','78','79');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='277';
													$categorias[]='286';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='371';
													$categorias[]='444';
												}
											break;
									}
								break;
							case '3':
									switch ($importData['familia']){
										case '1':
												$categorias=array('62','92','93');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='287';
													$categorias[]='288';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='372';
													$categorias[]='445';
												}
											break;
										case '2':
												$categorias=array('62','92','191');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='287';
													$categorias[]='289';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='372';
													$categorias[]='446';
												}
											break;
										case '3':
												$categorias=array('62','92','192');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='287';
													$categorias[]='290';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='372';
													$categorias[]='447';
												}
											break;
										case '4':
												$categorias=array('62','92','193');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='287';
													$categorias[]='291';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='372';
													$categorias[]='448';
												}
											break;
										case '5':
												$categorias=array('62','92','194');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='287';
													$categorias[]='292';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='372';
													$categorias[]='449';
												}
											break;
									}
								break;
							case '4':
									switch ($importData['familia']){
										case '1':
												$categorias=array('62','94','95');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='293';
													$categorias[]='294';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='373';
													$categorias[]='450';
												}
											break;
										case '2':
												$categorias=array('62','94','195');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='293';
													$categorias[]='295';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='373';
													$categorias[]='451';
												}
											break;
										case '3':
												$categorias=array('62','94','196');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='293';
													$categorias[]='296';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='373';
													$categorias[]='452';
												}
											break;
										case '4':
												$categorias=array('62','94','197');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='293';
													$categorias[]='297';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='373';
													$categorias[]='453';
												}
											break;
										case '5':
												$categorias=array('62','94','198');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='293';
													$categorias[]='298';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='373';
													$categorias[]='454';
												}
											break;
										case '6':
												$categorias=array('62','94','199');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='293';
													$categorias[]='299';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='373';
													$categorias[]='455';
												}
											break;
										case '7':
												$categorias=array('62','94','200');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='293';
													$categorias[]='300';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='373';
													$categorias[]='456';
												}
											break;
										case '8':
												$categorias=array('62','94','201');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='264';
													$categorias[]='293';
													$categorias[]='301';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='363';
													$categorias[]='373';
													$categorias[]='457';
												}
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
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='332';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='477';
												}
											break;
										case '2':
												$categorias=array('101','171','104');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='333';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='478';
												}
											break;
										case '3':
												$categorias=array('101','171','105');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='334';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='479';
												}
											break;
										case '4':
												$categorias=array('101','171','120');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='335';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='480';
												}
											break;
										case '5':
												$categorias=array('101','171','121');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='336';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='481';
												}
											break;
										case '6':
												$categorias=array('101','171','116');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='337';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='482';
												}
											break;
										case '7':
												$categorias=array('101','171','115');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='338';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='483';
												}
											break;
										case '8':
												$categorias=array('101','171','119');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='339';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='484';
												}
											break;
										case '9':
												$categorias=array('101','171','112');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='340';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='485';
												}
											break;
										case '10':
												$categorias=array('101','171','167');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='341';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='486';
												}
											break;
										case '11':
												$categorias=array('101','171','168');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='342';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='487';
												}
											break;
										case '12':
												$categorias=array('101','171','111');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='343';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='488';
												}
											break;
										case '13':
												$categorias=array('101','171','110');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='344';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='489';
												}
											break;
										case '14':
												$categorias=array('101','171','117');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='345';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='490';
												}
											break;
										case '15':
												$categorias=array('101','171','113');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='346';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='491';
												}
											break;
										case '16':
												$categorias=array('101','171','118');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='327';
													$categorias[]='347';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='378';
													$categorias[]='492';
												}
											break;
									}
								break;
							case '2':
									switch ($importData['familia']){
										case '1':
												$categorias=array('101','161','162');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='329';
													$categorias[]='350';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='380';
													$categorias[]='495';
												}
											break;
										case '2':
												$categorias=array('101','161','163');
												
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='329';
													$categorias[]='351';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='380';
													$categorias[]='496';
												}
											break;
									}
								break;
							case '3':
									switch ($importData['familia']){
										case '1':
												$categorias=array('101','158','159');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='328';
													$categorias[]='348';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='379';
													$categorias[]='493';
												}												
											break;
										case '2':
												$categorias=array('101','158','160');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='328';
													$categorias[]='349';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='379';
													$categorias[]='494';
												}			
											break;
									}
								break;
							case '4':
									switch ($importData['familia']){
										case '1':
												$categorias=array('101','106','169');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='330';
													$categorias[]='352';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='381';
													$categorias[]='497';
												}
											break;
										case '2':
												$categorias=array('101','106','170');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='330';
													$categorias[]='353';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='381';
													$categorias[]='498';
												}
											break;
										case '3':
												$categorias=array('101','106','108');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='330';
													$categorias[]='354';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='381';
													$categorias[]='499';
												}
											break;
										case '4':
												$categorias=array('101','106','109');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='330';
													$categorias[]='355';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='381';
													$categorias[]='500';
												}
											break;
										case '5':
												$categorias=array('101','106','205');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='330';
													$categorias[]='356';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='381';
													$categorias[]='501';
												}
										}
								break;
							case '5':
									switch ($importData['familia']){
										case '1':
												$categorias=array('101','96','164');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='331';
													$categorias[]='357';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='382';
													$categorias[]='502';
												}
											break;
										case '2':
												$categorias=array('101','96','97');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='331';
													$categorias[]='358';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='382';
													$categorias[]='503';
												}
											break;
										case '3':
												$categorias=array('101','96','166');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='331';
													$categorias[]='359';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='382';
													$categorias[]='504';
												}
											break;
										case '4':
												$categorias=array('101','96','165');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='331';
													$categorias[]='360';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='382';
													$categorias[]='505';
												}
											break;
										case '5':
												$categorias=array('101','96','99');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='326';
													$categorias[]='331';
													$categorias[]='361';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='365';
													$categorias[]='382';
													$categorias[]='506';
												}
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
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='303';
													$categorias[]='304';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='374';
													$categorias[]='458';
												}
											break;
										case '2':
												$categorias=array('122','123','125');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='303';
													$categorias[]='305';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='374';
													$categorias[]='459';
												}
											break;
										case '3':
												$categorias=array('122','123','126');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='303';
													$categorias[]='306';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='374';
													$categorias[]='460';
												}
											break;
										case '4':
												$categorias=array('122','123','127');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='303';
													$categorias[]='307';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='374';
													$categorias[]='461';
												}
											break;
										case '5':
												$categorias=array('122','123','128');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='303';
													$categorias[]='308';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='374';
													$categorias[]='462';
												}
											break;
										case '6':
												$categorias=array('122','123','129');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='303';
													$categorias[]='309';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='374';
													$categorias[]='463';
												}
											break;
										case '7':
												$categorias=array('122','123','130');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='303';
													$categorias[]='310';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='374';
													$categorias[]='464';
												}
											break;
										case '8':
												$categorias=array('122','123','132');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='303';
													$categorias[]='311';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='374';
													$categorias[]='465';
												}
											break;
										case '9':
												$categorias=array('122','123','133');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='303';
													$categorias[]='312';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='374';
													$categorias[]='466';
												}
											break;
										case '10':
												$categorias=array('122','123','134');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='303';
													$categorias[]='313';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='374';
													$categorias[]='467';
												}
											break;
										case '11':
												$categorias=array('122','123','135');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='303';
													$categorias[]='314';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='374';
													$categorias[]='468';
												}
											break;
										case '12':
												$categorias=array('122','123','136');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='303';
													$categorias[]='315';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='374';
													$categorias[]='469';
												}
											break;
										case '13':
												$categorias=array('122','123','137');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='303';
													$categorias[]='316';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='374';
													$categorias[]='470';
												}
											break;
										case '14':
												$categorias=array('122','123','138');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='303';
													$categorias[]='317';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='374';
													$categorias[]='471';
												}
											break;
										case '15':
												$categorias=array('122','123','140');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='303';
													$categorias[]='318';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='374';
													$categorias[]='472';
												}
											break;
									}
								break;
							case '2':
									switch ($importData['familia']){
										case '1':
												$categorias=array('122','142','144');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='319';
													$categorias[]='320';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='375';
													$categorias[]='473';
												}
											break;
										case '2':
												$categorias=array('122','142','145');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='319';
													$categorias[]='321';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='375';
													$categorias[]='474';
												}

											break;
									}
								break;
							case '3':
									switch ($importData['familia']){
										case '1':
												$categorias=array('122','148','149');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='322';
													$categorias[]='324';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='376';
													$categorias[]='475';
												}

											break;
									}
								break;
							case '4':
									switch ($importData['familia']){
										case '1':
												$categorias=array('122','150','151');
												if ((($importData['tipo']=='132') && ($importData['special_price']=='.00')) || ($importData['tipo']=='141') ){
													$categorias[]='206';
													$categorias[]='302';
													$categorias[]='323';
													$categorias[]='325';
												}
				if($importData['special_price']!='.00'){
													$categorias[]='173';
													$categorias[]='364';
													$categorias[]='377';
													$categorias[]='476';
												}

											break;
										
										}
								break;		
							}
					break;
				}
			
			
						
	}
	
	/********************************************************************************************************************************/
	
	/********************************************************************************************************************************/
	// Asignamos Temporada en función del tipo --> El atributo temporada nos permite mostrar productos novedosos o rebajados en cualquiera de las categorías
	
		if (isset($importData['tipo'])){
			$temporada = Mage::getModel('eav/config')->getAttribute('catalog_product', 'temporada');
			foreach ( $temporada->getSource()->getAllOptions(true, true) as $opcion){
				if ($opcion['label']==$importData['tipo']){
					if (!(($importData['tipo']=='131') && ($importData['special_price']=='.00'))){
					$product->setData('temporada', $opcion['value']);
					}
				}
			}
		}	
	
	
	
		/* Añadimos este código para crear las opciones de los atributos color, talla y diseñador en caso de que no existan */
	
	if (isset($importData['color']) && $importData['color']!=''){
		$attribute_model = Mage::getModel('eav/entity_attribute');
		$attribute_options_model = Mage::getModel('eav/entity_attribute_source_table');
		$attribute_code = $attribute_model->getIdByCode('catalog_product', 'color');
		$attribute = $attribute_model->load($attribute_code);
		$attribute_options_model->setAttribute($attribute);
		$options = $attribute_options_model->getAllOptions(false);
		// determine if this option exists
		$value_exists = false;
		foreach($options as $option) {
			if ($option['label'] == $importData['color']) {
				$value_exists = true;
				break;
			}
		}
		// if this option does not exist, add it.
		if (!$value_exists) {
			$attribute->setData('option', array(
				'value' => array(
					'option' => array($importData['color'],$importData['color'])
				)
			));
			$attribute->save();
		}
		
	}
	if (isset($importData['talla']) && $importData['talla']!=''){
		$attribute_model = Mage::getModel('eav/entity_attribute');
		$attribute_options_model = Mage::getModel('eav/entity_attribute_source_table');
		$attribute_code = $attribute_model->getIdByCode('catalog_product', 'talla');
		$attribute = $attribute_model->load($attribute_code);
		$attribute_options_model->setAttribute($attribute);
		$options = $attribute_options_model->getAllOptions(false);
		// determine if this option exists
		$value_exists = false;
		foreach($options as $option) {
			if ($option['label'] == $importData['talla']) {
				$value_exists = true;
				break;
			}
		}
		// if this option does not exist, add it.
		if (!$value_exists) {
			$attribute->setData('option', array(
				'value' => array(
					'option' => array($importData['talla'],$importData['talla'])
				)
			));
			$attribute->save();
		}
	}
	
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
	/******************************************************************************/
		
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
            if ( isset( $importData['associated'] ) ) {
				
				/******************************************************************************/
                /* Añadido para asociar productos sin borrar los ya asociados previamente */				
				
				foreach ($this->userCSVDataAsArray($importData['associated']) as $oneSku){ //extraemos los nuevos productos asociados
					$news_ids[]=( int )$product -> getIdBySku( $oneSku );
				}
				
				if ($product -> getIdBySku( $importData['sku'] )){ //Si existia el producto antes.
					$config_product    = Mage::getModel('catalog/product') -> load($product -> getIdBySku( $importData['sku'] ));
					$current_ids = $config_product -> getTypeInstance() -> getUsedProductIds(); //Extraemos los productos asociados que ya tiene el producto
					$nuevos_ids = array_merge($news_ids, $current_ids);
					$nuevos_ids=array_unique($nuevos_ids);
					foreach($nuevos_ids as $temp_id){
							parse_str("position=", $definitivos_ids[$temp_id]);
						}
					$product -> setConfigurableProductsData( $definitivos_ids );
				}else{
					$product -> setConfigurableProductsData( $this -> skusToIds( $importData['associated'], $product ) );
				}
                //**********************************************************************************
				
				//**********************************************************************************
				/* Asignamos al producto configurable la fecha de alta del 1º producto simple asociado */
			if ($nuevos_ids[0]){ 
					$product_simple= Mage::getModel('catalog/product') -> load($nuevos_ids[0]);	
					$product->setData('fecha_alta',$product_simple->getData('fecha_alta'));
				}else if ($news_ids[0]){ //no tenia ningún producto asociado y ahora se está asociando el primero 
					$product_simple= Mage::getModel('catalog/product') -> load($news_ids[0]);	
					$product->setData('fecha_alta',$product_simple->getData('fecha_alta'));
				}
				/******************************************************************************/
				
            }
		}
        //************************************************************************************************************
	
		//************************************************************************************************************
		//Código añadido para establecer fecha_alta y periodo de novedad 1 semana
			if (($importData['type']=='configurable') && (!($product -> getIdBySku( $importData['sku'] )))){  //Si es configurable y no existia antes
				if (!($product -> getData('news_from_date') && $product -> getData('news_to_date'))){ //Si no existen fecha nuevo la creamos
					$currentTimestamp = Mage::getModel('core/date')->timestamp(time());
					$product -> setData('news_from_date',$currentTimestamp);
					$fecha_final = Mage::getModel('core/date')->timestamp(time() + (7 * 24 * 60 * 60));
					$product -> setData('news_to_date',$fecha_final);
				}
			}else{ //Si no se trata de un producto configurable asignamos fecha_alta como news_from_date
				$product -> setData('fecha_alta',$importData['news_from_date']);			
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
            
			//************************************************************************************************
			//Descartamos los campos news_from_date y news_to_date por no estar bien definidos en el csv
			
			if ( $field=='news_from_date' || $field=='news_to_date'){
				continue;
				}
			//*************************************************************************************************
			
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
            
         //añadido par alas imagenes 
			$ruta = "";

	//***************************************************************************************************
	// Código añadido para Insertar correctamente las imagenes
			$ruta = Mage :: getBaseDir( 'media' ) ."/". 'import' . str_replace("/media/import","",$importData['rutaimagen']);
			$ruta = strtolower($ruta);
			$finrutaimagen = substr($importData['rutaimagen'],strlen(strtolower($importData['rutaimagen']))-4);
			$is_file = ((file_exists($ruta)) && $finrutaimagen=='.jpg');

			$ruta2 = Mage :: getBaseDir( 'media' ) ."/". 'import' . str_replace("/media/import","",$importData['campo_cuatro']);
			$ruta2 = strtolower($ruta2);			
			$finrutaimagen2 = substr($importData['campo_cuatro'],strlen(strtolower($importData['campo_cuatro']))-4);
			$is_file2 = ((file_exists($ruta2)) && $finrutaimagen2=='.jpg'); 			

			$ruta3 = Mage :: getBaseDir( 'media' ) ."/". 'import' . str_replace("/media/import","",$importData['campo_cinco']);
			$ruta3 = strtolower($ruta3);			
			$finrutaimagen3 = substr($importData['campo_cinco'],strlen(strtolower($importData['campo_cinco']))-4);
			$is_file3 = ((file_exists($ruta3)) && $finrutaimagen3=='.jpg');
			
			if ($is_file){
				$subir= false;
				if ($is_file2 && $is_file3){
					if (!($product->getMediaGalleryImages()) || ($product -> getMediaGalleryImages() -> getSize()<3)){
						$subir=true;
						
					}
				}else if ($is_file2 && (!$is_file3)){
					if (!($product->getMediaGalleryImages()) || ($product -> getMediaGalleryImages() -> getSize()<2)){
						$subir=true;
					}
				}else if ((!$is_file2) && (!$is_file3)){
					if (!($product->getMediaGalleryImages()) || ($product -> getMediaGalleryImages() -> getSize()<1)){
						$subir=true;
					}
				}

				if ($subir){
					$mediaAttributes = array ('image','thumbnail','small_image');				
	         		//$product -> addImageToMediaGallery( $ruta , 'image', true );  
	         		$product ->addImageToMediaGallery($ruta,$mediaAttributes, false, false );
				}
				
			}
			

	
	
	
	
	
	          
        $product -> setIsMassupdate( true );
        $product -> setExcludeUrlRewrite( true );
        $product->setWebsiteIDs(array(1));
		
		if($importData['status'] == 'Deshabilitado'):
		$product->setStatus(2);
		else:
		$product->setStatus(1);
		endif;
        
		if($importData['visibility'] == 'Not Visible Individually'):
		$product->setVisibility(1);
		else:
		$product->setVisibility(4);
		endif;
		
		//print_r($importData);
		$catExist=$product->getCategoryIds();
		if (!empty($catExist)){
			$categorias = array_merge($categorias, $catExist);
			$categorias = array_unique($categorias);
		}
		 foreach($categorias as $categor):
			$cats .= $categor.",";
		 endforeach;
		 
		 $cats = substr($cats,0,-1);
		// echo $ruta;
		 //$product->setManufacturer($importData["manufacturer"]);
		 
		 $manufacturer = $this->getAttributeValue('Manufacturer',$importData["manufacturer"]);
		 $product->setManufacturer($manufacturer);
		 $color = $this->getAttributeValue('color',$importData["color"]);
		 $product->setData('color',$color);
		 $talla = $this->getAttributeValue('talla',$importData["talla"]);
		 $product->setData('talla',$talla);
		 $product->setPrice($importData["price"]);
		 if ($importData["special_price"]!='.00'):
		 $product->setSpecialPrice($importData["special_price"]);
		 $product->setData('special_from_date',$importData["special_from_date"]);
		 $product->setData('special_to_date',$importData["special_to_date"]);
		 
		 endif;
		$product->setCategoryIds($cats);
		$product->setTaxClassId(2);	
		
		//*****************************************************************
		// Rellenamos campos requeridos que no estan en el csv.
		$product->setData('short_description',$importData['campo_tres']);
		$product->setData('weight',$importData['weight']);
		//*****************************************************************
		
		$product -> save();
		
		//***************************************************************************************************
		// Insertamos Nombre, descripcion y url en ingles
		
		$product2 = new Mage_Catalog_Model_Product();
 		$product2 -> setData( array() );
		$product2->setStoreId(2);
 		$product2Id = $product2 -> getIdBySku( $importData['sku'] );
 		$product2 -> load( $product2Id );
 		$product2 -> setData('news_from_date', false);
		$product2 -> setData('news_to_date', false);
		$product2 -> setData('image', false);
		$product2 -> setData('small_image', false);
		$product2 -> setData('thumbnail', false);
		$product2 -> setStatus(false);
		$product2 -> setVisibility(false);
		$product2->setName($importData['campo_uno']);
		$product2->setData('description', $importData['campo_uno']);
		
		
		//creamos url amigable a partir del titulo en ingles
		$url = strtolower($importData['campo_uno']);
		$find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
		$repl = array('a', 'e', 'i', 'o', 'u', 'n');
		$url = str_replace ($find, $repl, $url);
		$find = array(' ', '&', '\r\n', '\n', '+'); 
		$url = str_replace ($find, '-', $url);
		$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
		$repl = array('', '-', '');
		$url = preg_replace ($find, $repl, $url);
		$product2->setData('url_key', $url);
		
		$product2->setData('meta_title',$importData['campo_uno']);
		$product2->setData('meta_description',$importData['campo_seis']);
		$product2->setData('short_description',$importData['campo_seis']);
		$product2->setData('meta_keyword', str_replace(' ',',', strtolower($importData['campo_uno'])));
		
		$product2 -> save();
		//****************************************************************************************

		$product->unSetData();
		$product2->unSetData();
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
    