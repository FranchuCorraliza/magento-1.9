<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_GeoipRedirect
 */
class Amasty_GeoipRedirect_Block_System_Config_Form_Field_Currency extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        $this->addColumn('country_currency', array(
            'label' => Mage::helper('amgeoipredirect')->__('Country'),
            'style' => 'width:120px',
            'renderer'  => 'Amasty_GeoipRedirect_Block_System_Config_Form_Renderer_Currency',
        ));
        $this->addColumn('currency', array(
            'label' => Mage::helper('amgeoipredirect')->__('Currency'),
            'style' => 'width:120px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('amgeoipredirect')->__('Add');
        $this->setTemplate('amasty/geoipredirect/system/config/form/field/array.phtml');
        parent::__construct();
    }

    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $name = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if ($columnName == 'country_currency') {
            $options = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
            $country = '<select name="' . $name . '">';
            foreach ($options as $option) {
                $country .= '<option value="' . $option['value'] . '">' . $option['label'] . '</option>';
            }
            $country .= '</select>';
            return $country;
        } elseif ($columnName == 'currency') {
            $currencies = $this->getCurrencies();
            array_unshift($currencies, Mage::helper('adminhtml')->__('--Please Select--'));
            $currenciesHtml = '<select name="' . $name . '">';
            foreach ($currencies as $key => $currency) {
                $currenciesHtml .= '<option value="' . $key . '">' . $currency . '</option>';
            }
            $currenciesHtml .= '</select>';
            return $currenciesHtml;
        }
        return '<input type="text" name="' . $name . '" value="#{' . $columnName . '}" ' .
        ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
        (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
        (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '/>';

    }

    public function getCurrencies()
    {
        $currencies = $this->getData('currencies');
        if (is_null($currencies)) {
            $currencies = array();
            $codes = Mage::app()->getStore()->getAvailableCurrencyCodes(true);
            if (is_array($codes) && count($codes) > 1) {
                $rates = Mage::getModel('directory/currency')->getCurrencyRates(
                    Mage::app()->getStore()->getBaseCurrency(),
                    $codes
                );

                foreach ($codes as $code) {
                    if (isset($rates[$code])) {
                        $currencies[$code] = Mage::app()->getLocale()
                            ->getTranslation($code, 'nametocurrency');
                    }
                }
            }

            $this->setData('currencies', $currencies);
        }
        return $currencies;
    }
}
