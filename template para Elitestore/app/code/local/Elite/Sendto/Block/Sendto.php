<?php
class Elite_Sendto_Block_Sendto extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getZones()
    {
        $asia       = array();
        $china      = array();
        $europa     = array();
        $global     = array();
        $Oceania    = array();
        $usaycanada = array();
        $hablaHispana    = array();

    	$zona1 = array('AF', 'AL', 'AM', 'AZ', 'BH', 'BD', 'BT', 'BA', 'BG', 'KH', 'HR', 'IR', 'IQ', 'IL', 'JP', 'JO', 'KZ', 'KW', 'KG', 'LA', 'LB', 'MV', 'MN', 'NP', 'NC', 'KP', 'QA', 'RU', 'TJ', 'TH', 'UZ', 'VN');//asia
    	$zona2 = array('CN', 'HK', 'MO', 'MM', 'SG', 'TW');//china
    	$zona3 = array('AD', 'AI', 'BY', 'BE', 'CY', 'CZ', 'DK', 'EE', 'FK', 'FO', 'FI', 'FR', 'Germany', 'GI', 'GR', 'GG', 'HU', 'IS', 'IE', 'IM', 'IT', 'JE', 'LV', 'LI', 'LT', 'LU', 'MK', 'MT', 'MD', 'MC', 'ME', 'NL', 'PL', 'PT', 'RO', 'MF', 'PM', 'SM', 'RS', 'SK', 'SI', 'ES', 'SJ', 'SE', 'CH', 'UA', 'GB', 'VA', 'AX');//europa
    	$zona4 = array('AU', 'BN', 'CC', 'CK', 'FJ', 'PF', 'HM', 'ID', 'KI', 'MY', 'MH', 'NR', 'NZ', 'NU', 'NF', 'MP', 'PW', 'PG', 'PH', 'PN', 'WS', 'SB', 'TL', 'TK', 'TO', 'TV', 'UM', 'VU', 'WF');//oceania
    	$zona5 = array('CA', 'GL', 'NO', 'US');//Estados Unidos y Canada
    	$zona6 = array('DZ', 'AS', 'AO', 'AQ', 'AG', 'AR', 'AW', 'BS', 'BB', 'BZ', 'BJ', 'BM', 'BO', 'BW', 'BV', 'BR', 'IO', 'VG', 'BF', 'BI', 'CM', 'CV', 'KY', 'CF', 'TD', 'CL', 'CX', 'CO', 'KM', 'CG', 'CD', 'CR', 'CU', 'CI', 'DJ', 'DM', 'DO', 'EC', 'EG', 'SV', 'GQ', 'ER', 'ET', 'GF', 'TF', 'GA', 'GM', 'GE', 'GH', 'GD', 'GP', 'GU', 'GT', 'GN', 'GW', 'GY', 'HT', 'HN', 'IN', 'JM', 'KE', 'LS', 'LR', 'LY', 'MW', 'ML', 'MQ', 'MR', 'MG', 'MU', 'YT', 'MX', 'FM', 'MS', 'MA', 'MZ', 'NA', 'AN', 'NI', 'NE', 'NG', 'OM', 'PK', 'PS', 'PA', 'PY', 'PE', 'PR', 'QA', 'RW', 'RE', 'BL', 'SH', 'KN', 'LC', 'SA', 'SN', 'SC', 'SL', 'SO', 'ZA', 'GS', 'KR', 'LK', 'VC', 'SD', 'SR', 'SZ', 'SY', 'ST', 'TZ', 'TG', 'TT', 'TN', 'TR', 'TC', 'VI', 'UG', 'AE', 'UY', 'VE', 'EH', 'YE', 'ZM', 'ZW');//global
        $hispana = array('MX', 'CO', 'ES', 'AR', 'VE', 'PE', 'CL', 'EC', 'GT', 'CU', 'BO', 'DO', 'HN', 'SV', 'PY', 'CR', 'NI', 'PR', 'PA', 'UY', 'GQ', 'EH');//Metemos los paises que estan en habla hispana

    	$countryList = Mage::getModel('directory/country')->getResourceCollection()
                            ->loadByStore()
                            ->toOptionArray(true);
        //recorremos los paises para asignarlos al array de la zona a enviar
            foreach ($countryList as $country) {
                if($country['value']!="")
                {
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
                    
                    if(in_array($country['value'], $hispana))
                        $hablaHispana = array($country['label'] => $country['value']);
                }
            }
        // fin de recorrer los paises

        $countryListFinal = array('Asia' => $asia, 'China' => $china, 'Europa' => $europa, 'Oceania' => $oceania, 'U.S.A. & Canada' => $usaycanada, 'Global' => $global, 'hispana' => $hablaHispana);        
Mage::log($countryListFinal, null, "paises.log");
         return $countryListFinal;
    }
}

?>