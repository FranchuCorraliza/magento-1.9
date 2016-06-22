<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */   
class Amasty_Audit_Block_Adminhtml_Userlog_Edit_Tab_View_Details extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        if(Mage::registry('current_log') && (Mage::registry('current_log')->getCategoryName() == "Cache" || Mage::registry('current_log')->getCategoryName() == "Index Management")) {
            $this->setTemplate('amaudit/tab/view/detailsCache.phtml');    
        }
        else {
            $this->setTemplate('amaudit/tab/view/details.phtml');    
        }

    }
    
    public function getLogRows() 
    {
         $collection = Mage::getModel('amaudit/log_details')->getCollection();
         if (!Mage::registry('current_log')) 
         {
            return array();
         }
         else
         {
            $collection->addFieldToFilter('log_id', array('in' => Mage::registry('current_log')->getId()));  
            $collection->getSelect()->order('model');
            return $collection;
         }
    }

}
