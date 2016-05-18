<?php
/**
 * Survey config collection
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Mageplace
 */
class Belitsoft_Survey_Model_Mysql4_Config_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('belitsoft_survey/config');
    }
}
