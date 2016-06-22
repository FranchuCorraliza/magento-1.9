<?php
class Magestore_Auction_Block_Adminhtml_Product_Edit_Tab_Auctionform extends Mage_Adminhtml_Block_Widget_Form
{ 
	public function __construct()
	{
		parent::__construct();
		//$this->setTemplate('auction/auctionform.phtml');
	}
  
    public function getProduct()
    {
        return Mage::registry('current_product');
    } 
	
	protected function _prepareForm()
	{
      $product = $this->getProduct();
	  
	  $att = $product->getResource()
            ->getAttribute('auction_init_price');
	  
	 // var_dump($att->getDefaultValueByInput('decimal'));die();
	  
	  $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('auction_edit', array('legend'=>Mage::helper('auction')->__('Auction information')));
      
	  $image_calendar = Mage::getBaseUrl('skin') .'adminhtml/default/default/images/grid-cal.gif';
   
      $fieldset->addField('auction_is_auction', 'select', array(
          'label'     => Mage::helper('auction')->__('Is Auction'),
          'name'      => 'product[auction_is_auction]',
		  'value'     => $product->getData('auction_is_auction'),
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('auction')->__('Yes'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('auction')->__('No'),
              ),
          ),
      ));	 
	 
      $fieldset->addField('auction_init_price', 'text', array(
          'label'     => Mage::helper('auction')->__('Starting Price'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'product[auction_init_price]',
		  'value'     => $product->getData('auction_init_price'),
      ));

      $fieldset->addField('auction_min_interval_price', 'text', array(
          'label'     => Mage::helper('auction')->__('Minimum Bid Increment'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'product[auction_min_interval_price]',
      ));
	  
      $fieldset->addField('auction_max_interval_price', 'text', array(
          'label'     => Mage::helper('auction')->__('Minimum Bid Increment'),
          'name'      => 'product[auction_max_interval_price]',
      ));	

      $fieldset->addField('auction_start_date', 'date', array(
          'label'     => Mage::helper('auction')->__('Start Date'),
          'class'     => 'required-entry',
          'required'  => true,		  
          'name'      => 'product[auction_start_date]',
		  'format'    => 'yyyy-MM-dd',
		  'image'     => $image_calendar,
      ));	
  

      $fieldset->addField('auction_end_date', 'date', array(
          'label'     => Mage::helper('auction')->__('End Date'),
          'class'     => 'required-entry',
          'required'  => true,		  
          'name'      => 'product[auction_end_date]',
		  'format'    => 'yyyy-MM-dd',	
		  'image'     => $image_calendar,
      ));		  
		
     /*
      if ( Mage::getSingleton('adminhtml/session')->getAuctionData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getAuctionData());
          Mage::getSingleton('adminhtml/session')->setAuctionData(null);
      } elseif ( Mage::registry('auction_data') ) {
          $form->setValues(Mage::registry('auction_data')->getData());
      }
	  */
      return parent::_prepareForm();
	}	
}