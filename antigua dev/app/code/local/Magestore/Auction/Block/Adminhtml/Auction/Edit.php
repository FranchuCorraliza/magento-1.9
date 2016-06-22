<?php

class Magestore_Auction_Block_Adminhtml_Auction_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'auction';
        $this->_controller = 'adminhtml_auction';
        
        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_removeButton('delete');
        $this->_removeButton('back');
       // $this->_updateButton('delete', 'label', Mage::helper('auction')->__('Delete Item'));
		
		$bid = Mage::registry('auction_data');
		
		$this->_addButton('_back', array(
				'label'     => Mage::helper('adminhtml')->__('Back'),
				'onclick'   => 'location.href=\''. $this->getUrl('*/adminhtml_productauction/edit',array('id'=>$bid->getProductauctionId())) .'\'',
				'class'     => 'back',
			), 1);			   
	   
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
        ";
    }

    public function getHeaderText()
    {
         return Mage::helper('auction')->__('Bid Information');
    }
}