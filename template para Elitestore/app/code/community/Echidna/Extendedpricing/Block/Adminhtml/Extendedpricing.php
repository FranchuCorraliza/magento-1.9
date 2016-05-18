<?php  
/**
 * 
 *
 *
 * Author@ Nimila Jose
 * Company@ Echidna Software Pvt Ltd
 * Purpose@ Extended Pricing Sheet
 * 
 *
 */
 
class Echidna_Extendedpricing_Block_Adminhtml_Extendedpricing extends Mage_Adminhtml_Block_Widget_Grid_Container 
{
	public function __construct()
	{
		$this->_controller = "adminhtml_extendedpricing";
		$this->_blockGroup = "extendedpricing";
		$this->_headerText = Mage::helper("extendedpricing")->__("Manage Price Sheet");
				
		$data = array(
                'label' =>  'Import Product Sheet List',
                'onclick'   => "setLocation('".$this->getUrl('*/*/import')."')"
                );

		$this->addButton('price_book', $data, 0, 100,  'header', 'header'); 
		
		parent::__construct();
		$this->_removeButton('add');
	}
}