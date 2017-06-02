
<?php 
$proxy = new SoapClient('<?php echo Mage::getBaseUrl()?>api/v2_soap/?wsdl'); // TODO : change url
$sessionId = $proxy->login('productos', '66ea3c758a0bdce0233e82261c337e1e'); // TODO : change login and pwd if necessary

$result = $proxy->catalogProductInfo($sessionId, array('sku'=> array('eq' => 'DFX1022A081')));
var_dump($result);