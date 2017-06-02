<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Fast Asynchronous Re-indexing
 * @version   1.1.6
 * @build     285
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_AsyncIndex_Block_Adminhtml_System_Config_Form_Field_ValidateCategoryIndex extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        if (!Mage::getStoreConfigFlag(Mage_Catalog_Helper_Category_Flat::XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY)) {
            $element->setDisabled('disabled')
                ->setValue(0)
                ->setComment('<span style="color:red">Category Flat Catalog</span> must be enabled for use this option. <br> For enable flat catalog, go to System > Configuration > Catalog > Frontend and set "Use Flat Catalog Category" to "Yes"');
        }

        return parent::_getElementHtml($element);
    }

}