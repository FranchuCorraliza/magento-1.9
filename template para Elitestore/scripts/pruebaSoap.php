<?php

try{
	libxml_disable_entity_loader(false);
	$context = stream_context_create(array(
			'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
			)));
    $client = new SoapClient('https://www.elitestores.com/lux/en/api/?wsdl',
                             array('stream_context' => $context,
                                   'cache_wsdl' => WSDL_CACHE_NONE));
	$session = $client->login('prueba', 'Elite2017.');
    $result = $client->call($session, 'catalog_category.tree');
	var_dump($result);
}
catch(Exception $e){
    echo $e->getMessage();
}
?>