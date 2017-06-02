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
 * @package   Full Page Cache
 * @version   1.0.32
 * @build     662
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_FpcCrawler_Block_Adminhtml_System_BtnAction extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _prepareLayout()
    {
        if ($this->isMagentoEe()) {
            return false;
        }
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('fpccrawler/system/btn_action.phtml');
        }
        return $this;
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if ($this->isMagentoEe()) {
            return false;
        }
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        if ($this->isMagentoEe()) {
            return false;
        }
        $originalData = $element->getOriginalData();
        $this->addData(array(
            'button_label' => Mage::helper('customer')->__($originalData['button_label']),
            'html_id' => $element->getHtmlId(),
            'ajax_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/fpccrawler_system_generate/'.$originalData['button_action'])
        ));

        return $this->_toHtml();
    }

    protected function isMagentoEe() {
        $isMagentoEe = false;
        if (Mage::helper('mstcore/version')->getEdition() == 'ee') {
            $isMagentoEe = true;
        }

        return $isMagentoEe;
    }
}
