<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Orders Pro extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @author     MageWorx Dev Team
 */

class MageWorx_Adminhtml_Block_Orderspro_Sales_Order_View extends MageWorx_Adminhtml_Block_Orderspro_Sales_Order_View_Abstract
{
    
    protected $_helper = null;
    
    public function __construct() {
        $this->_helper = Mage::helper('orderspro');
        parent::__construct();
        
        if (!$this->_helper->isEnabled() || !$this->_helper->isEditEnabled()) return $this;
        $order = $this->getOrder();
        
        if ($this->_isAllowedAction('edit')) {
            if ($order->canEdit()) {
                if ($order->canInvoice()) $confirm = 0; else $confirm = 1;
                $this->_updateButton('order_edit', 'onclick', 'confirmEdit(event, '.$confirm.', \''.$this->getEditUrl().'\', '.$order->getId().')');            
            } else {
                 $this->_addButton('order_edit', array(
                    'label'    => Mage::helper('sales')->__('Edit'),
                    'onclick'  => 'confirmEdit(event, 2, \''.$this->getEditUrl().'\', '.$order->getId().')'                     
                ),
                -1);
            }
        }                        
    }
    
    protected function _isAllowedAction($action) {
        if ($action=='emails' && $this->_helper->isEnabled() && $this->_helper->isEnableDeleteOrdersCompletely() && Mage::getSingleton('admin/session')->isAllowed('sales/orderspro/actions/delete_completely')) {
            $message = $this->_helper->__('Are you sure you want to completely delete this order?');
            $this->_addButton('order_delete', array(
                'label'     => $this->_helper->__('Delete'),
                'onclick'   => 'deleteConfirm(\''.$message.'\', \'' . $this->getUrl('mageworx/orderspro_order/massDeleteCompletely') . '\')',
                'class'  => 'delete'
                )
            );
        }         
        return parent::_isAllowedAction($action);                        
    }        
    
    
    public function getHeaderText() {
        $text = parent::getHeaderText();
        if ($this->_helper->isEnabled() && $this->getOrder()->getIsEdited()) $text .= ' ('.$this->_helper->__('Edited').')';        
        return $text;
    }    
}
