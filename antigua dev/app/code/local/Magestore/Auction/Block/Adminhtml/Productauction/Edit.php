<?php

class Magestore_Auction_Block_Adminhtml_Productauction_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'auction';
        $this->_controller = 'adminhtml_auction';

        $this->_updateButton('save', 'label', Mage::helper('auction')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('auction')->__('Delete Item'));
        $this->_removeButton('reset');
        if ($this->getRequest()->getParam('id') && $this->getRequest()->getParam('id') > 0) {
            $this->_addButton('duplicate', array(
                'label' => Mage::helper('adminhtml')->__('Duplicate'),
                'onclick' => 'location.href=\'' . $this->getUrl('auctionadmin/adminhtml_productauction/duplicate', array('id' => $this->getRequest()->getParam('id'))) . '\'',
                'class' => 'add',
                    ), 0);
        }
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -10);

        //	if($this->getProductauction()->getStatus() > 3)
        //	{
        $this->_removeButton('delete');
        $this->_addButton('cancel', array(
            'label' => Mage::helper('adminhtml')->__('Cancel'),
            'onclick' => 'cancelAuction()',
            'class' => 'scalable',
                ), 1);
        //	}


        $this->_formScripts[] = "
	
            function toggleEditor() {
                if (tinyMCE.getInstanceById('auction_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'auction_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'auction_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
			
			function viewbid(url)
			{
				location.href=url;
			}
			
			function cancelAuction()
			{
				if(confirm('Are you sure you want to cancel this auction?'))
				{
					location.href='" . $this->getUrl('*/*/cancel', array('id' => $this->getRequest()->getParam('id'))) . "';			
				}
			}
	
        ";
    }

    public function getHeaderText() {
        if (Mage::registry('productauction_data') && Mage::registry('productauction_data')->getId()) {
            if (Mage::registry('productauction_data')->getStatus() == 5) {
                return Mage::helper('auction')->__("Auction Infomation");
            } else {
                return Mage::helper('auction')->__("Edit Auction for '%s'", $this->htmlEscape(Mage::registry('productauction_data')->getProductName()));
            }
        } else {
            return Mage::helper('auction')->__('Add Auction');
        }
    }

    public function getProductauction() {
        if (!$this->hasData('productauction_data')) {
            $this->setData('productauction_data', Mage::registry('productauction_data'));
        }
        return $this->getData('productauction_data');
    }

}
