<?php
class Elite_Sendto_Block_Sendto extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getHablaHispana(){
        $hablaHispana    = array();
        $hispana = array('MX', 'CO', 'ES', 'AR', 'VE', 'PE', 'CL', 'EC', 'GT', 'CU', 'BO', 'DO', 'HN', 'SV', 'PY', 'CR', 'NI', 'PR', 'PA', 'UY', 'GQ', 'EH');//Metemos los paises que estan en habla hispana
        return $hispana;
    }

    public function getZones()
    {
        $asia            = array();
        $china           = array();
        $europa          = array();
        $global          = array();
        $Oceania         = array();
        $usaycanada      = array();
        $hablaHispana    = array();
        $continenteafrica    = array();
        $continenteamerica    = array();
        $continenteeuropa    = array();
        $continenteasia    = array();
        $continenteoceania    = array();

    	//$zona1 = array('AF', 'BH', 'BD', 'BT', 'BA', 'BG', 'KH', 'KR', 'HR', 'IR', 'IQ', 'IL', 'JP', 'JO', 'KZ', 'KW', 'KG', 'LA', 'LB', 'MV', 'MN', 'NP', 'NC', 'KP', 'QA', 'RU', 'TJ', 'TH', 'UZ', 'VN');//asia
    	//$zona2 = array('CN', 'HK', 'MO', 'MM', 'SG', 'TW');//china
    	//$zona3 = array('AD', 'AL', 'AM', 'AZ', 'AI', 'BA', 'BY', 'BE', 'CY', 'CZ', 'DK', 'EE', 'FK', 'FO', 'FI', 'FR', 'DE', 'GI', 'GR', 'GG', 'HR', 'HU', 'IS', 'IE', 'IM', 'IT', 'JE', 'LV', 'LI', 'LT', 'LU', 'MK', 'MT', 'MD', 'MC', 'ME', 'NL', 'PL', 'PT', 'RO', 'MF', 'PM', 'SM', 'RS', 'SK', 'SI', 'ES', 'SJ', 'SE', 'CH', 'UA', 'GB', 'VA', 'AX');//europa
    	//$zona4 = array('AU', 'BN', 'CC', 'CK', 'FJ', 'PF', 'HM', 'ID', 'KI', 'MY', 'MH', 'NR', 'NZ', 'NU', 'NF', 'MP', 'PW', 'PG', 'PH', 'PN', 'WS', 'SB', 'TL', 'TK', 'TO', 'TV', 'UM', 'VU', 'WF');//oceania
    	//$zona5 = array('CA', 'GL', 'NO', 'US');//Estados Unidos y Canada
    	//$zona6 = array('DZ', 'AS', 'AO', 'AQ', 'AG', 'AR', 'AW', 'BS', 'BB', 'BZ', 'BJ', 'BM', 'BO', 'BW', 'BV', 'BR', 'IO', 'VG', 'BF', 'BI', 'CM', 'CV', 'KY', 'CF', 'TD', 'CL', 'CX', 'CO', 'KM', 'CG', 'CD', 'CR', 'CU', 'CI', 'DJ', 'DM', 'DO', 'EC', 'EG', 'SV', 'GQ', 'ER', 'ET', 'GF', 'TF', 'GA', 'GM', 'GE', 'GH', 'GD', 'GP', 'GU', 'GT', 'GN', 'GW', 'GY', 'HT', 'HN', 'IN', 'JM', 'KE', 'LS', 'LR', 'LY', 'MW', 'ML', 'MQ', 'MR', 'MG', 'MU', 'YT', 'MX', 'FM', 'MS', 'MA', 'MZ', 'NA', 'AN', 'NI', 'NE', 'NG', 'OM', 'PK', 'PS', 'PA', 'PY', 'PE', 'PR', 'QA', 'RW', 'RE', 'BL', 'SH', 'KN', 'LC', 'SA', 'SN', 'SC', 'SL', 'SO', 'ZA', 'GS', 'LK', 'VC', 'SD', 'SR', 'SZ', 'SY', 'ST', 'TZ', 'TG', 'TT', 'TN', 'TR', 'TC', 'VI', 'UG', 'AE', 'UY', 'VE', 'EH', 'YE', 'ZM', 'ZW');//global
		$zonaUE = array ('DE','AT','BE','BG','CY','HR','DK','SK','SI','ES','EE','FI','FR','GR','HU','IE','IT','LV','LT','LU','MT','NL','PL','PT','UK','CZ','RO','SE','GB');
        $hispana = array('MX', 'CO', 'ES', 'AR', 'VE', 'PE', 'CL', 'EC', 'GT', 'CU', 'BO', 'DO', 'HN', 'SV', 'PY', 'CR', 'NI', 'PR', 'PA', 'UY', 'GQ', 'EH');//Metemos los paises que estan en habla hispana

        $continenteafrica = array('AO', 'DZ', 'BJ', 'BW', 'BF', 'BI', 'CV', 'CM', 'TD', 'KM', 'CD', 'CG', 'CI', 'EG', 'ER', 'ET', 'GA', 'GM', 'GH', 'GW', 'GQ', 'GN', 'KE', 'LS', 'LR', 'LY', 'MG', 'MW', 'ML', 'MA', 'MU', 'MR', 'YT', 'MZ', 'NA', 'NG', 'NE', 'CF', 'RE', 'RW', 'EH', 'SH', 'ST', 'SN', 'SC', 'SL', 'SO', 'SZ', 'ZA', 'SS', 'SD', 'TZ', 'TG', 'TN', 'UG', 'DJ', 'ZM', 'ZW');//Metemos los paises que estan en habla hispana
        $continenteamerica = array('AI', 'AG', 'AR', 'AW', 'BS', 'BB', 'BZ', 'BM', 'BO', 'BQ', 'BR', 'CA', 'CL', 'CO', 'CR', 'CU', 'CW', 'DM', 'EC', 'SV', 'US', 'GD', 'GL', 'GP', 'GT', 'GF', 'GY', 'HT', 'HN', 'KY', 'FK', 'TC', 'VG', 'VI', 'JM', 'MQ', 'MX', 'MS', 'NI', 'PA', 'PY', 'PE', 'PR', 'DO', 'BL', 'KN', 'MF', 'PM', 'VC', 'LC', 'SX', 'SR', 'TT', 'UY', 'VE');//Metemos los paises que estan en habla hispana
        $continenteeuropa = array('AL', 'DE', 'AD', 'AT', 'BE', 'BY', 'BA', 'BG', 'HR', 'DK', 'SK', 'SI', 'ES', 'EE', 'RU', 'FI', 'FR', 'GI', 'GR', 'GG', 'HU', 'IE', 'IM', 'IS', 'AX', 'FO', 'IT', 'JE', 'LV', 'LI', 'LT', 'LU', 'MK', 'MT', 'MD', 'MC', 'ME', 'NO', 'NL', 'PL', 'PT', 'GB', 'CZ', 'RO', 'SM', 'VA', 'RS', 'SE', 'CH', 'SJ', 'TR', 'UA', 'CY');//Metemos los paises que estan en habla hispana
        $continenteasia = array('AF', 'SA', 'AM', 'AZ', 'BD', 'BH', 'BN', 'BT', 'KH', 'CN', 'KR', 'AE', 'PH', 'GE', 'HK', 'IN', 'ID', 'IQ', 'IR', 'CX', 'CC', 'IL', 'JP', 'JO', 'KZ', 'KG', 'KW', 'LB', 'MO', 'MY', 'MV', 'MN', 'MM', 'NP', 'OM', 'PK', 'PS', 'QA', 'LA', 'SG', 'SY', 'LK', 'TH', 'TW', 'TJ', 'IO', 'TL', 'TM', 'UZ', 'VN', 'YE');//Metemos los paises que estan en habla hispana
        $continenteoceania = array('AU', 'FJ', 'GU', 'NF', 'CK', 'MP', 'MH', 'SB', 'UM', 'KI', 'FM', 'NR', 'NU', 'NC', 'NZ', 'PW', 'PG', 'PN', 'PF', 'AS', 'WS', 'TK', 'TO', 'TV', 'VU', 'WF');//Metemos los paises que estan en habla hispana


    	/*$countryList = Mage::getModel('directory/country')->getResourceCollection()
                            ->loadByStore()
                            ->toOptionArray(true);*/
            $countryList = Mage::getModel('directory/country')
                ->getResourceCollection()
                ->loadByStore(0)
                ->toOptionArray(true);
        //recorremos los paises para asignarlos al array de la zona a enviar
            foreach ($countryList as $country) {
                if($country['value']!="")
                {
                    /*
                    if(in_array($country['value'], $zona1))
                        $asia[substr($country['label'], 0,1)][] = array($country['label'] => $country['value']);
                    elseif (in_array($country['value'], $zona2))
                        $china[substr($country['label'], 0,1)][] = array($country['label'] => $country['value']);
                    elseif (in_array($country['value'], $zona3))
                        $europa[substr($country['label'], 0,1)][] = array($country['label'] => $country['value']);
                    elseif (in_array($country['value'], $zona4))
                        $oceania[substr($country['label'], 0,1)][] = array($country['label'] => $country['value']);
                    elseif (in_array($country['value'], $zona5))
                        $usaycanada[substr($country['label'], 0,1)][] = array($country['label'] => $country['value']);
                    else
                        $global[substr($country['label'], 0,1)][] = array($country['label'] => $country['value']);
                    */
                    if(in_array($country['value'], $continenteasia))
                        $asia[substr($country['label'], 0,1)][] = array($country['label'] => $country['value']);
                    elseif (in_array($country['value'], $continenteeuropa))
                        $europa[substr($country['label'], 0,1)][] = array($country['label'] => $country['value']);
                    elseif (in_array($country['value'], $continenteoceania))
                        $oceania[substr($country['label'], 0,1)][] = array($country['label'] => $country['value']);
                    elseif (in_array($country['value'], $continenteamerica))
                        $usaycanada[substr($country['label'], 0,1)][] = array($country['label'] => $country['value']);
                    elseif (in_array($country['value'], $continenteafrica))
                        $africa[substr($country['label'], 0,1)][] = array($country['label'] => $country['value']);
                    
                    if(in_array($country['value'], $hispana))
                        $hablaHispana = array($country['label'] => $country['value']);
                }
            }
        // fin de recorrer los paises

       /* $countryListFinal = array('Asia' => $asia, 'China' => $china, 'Europa' => $europa, 'Oceania' => $oceania, 'U.S.A. & Canada' => $usaycanada, 'Global' => $global, 'hispana' => $hablaHispana, 'continenteafrica' => $continenteafrica, 'continenteamerica' => $continenteamerica, 'continenteeuropa' => $continenteeuropa, 'continenteasia' => $continenteasia, 'continenteoceania' => $continenteoceania);*/
        $countryListFinal = array('Asia' => $asia, 'Europa' => $europa, 'Oceania' => $oceania, 'America' => $usaycanada, 'Africa' => $africa, 'hispana' => $hablaHispana, 'zonaUE'=>$zonaUE);//, 'zona2'=>$zona2, 'zona3'=>$zona3, 'zona4'=>$zona4, 'zona5'=>$zona5, 'zona6'=>$zona6);
         return $countryListFinal;
    }
}

?>