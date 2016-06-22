<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */   
class Amasty_Audit_Block_Adminhtml_Userlog_Edit_Tab_View  extends Mage_Adminhtml_Block_Template
{
    protected $_log;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amaudit/tab/view.phtml');
        $this->setChild('details',  Mage::app()->getLayout()->createBlock('amaudit/adminhtml_userlog_edit_tab_view_details'));
    }

    public function getLog()
    {
        if (!$this->_log) {
            $this->_log = Mage::registry('current_log');
        }
        return $this->_log;
    }

}
