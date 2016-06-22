<?php
Mage::app(); 
$geoIP = Mage::getSingleton('geoip/country');
$country = $geoIP->getCountry();
$zona2 = array ('FR','IT','GB');
if(in_array($country,$zona2)) {
    $mageRunType = 'website';
    $mageRunCode = 'zona2';

}
else
{
   $mageRunType = 'website';
   $mageRunCode = 'base';
}
Mage::reset();