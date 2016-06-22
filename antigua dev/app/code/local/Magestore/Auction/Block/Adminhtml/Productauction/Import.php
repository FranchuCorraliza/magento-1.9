<?php

class Magestore_Auction_Block_Adminhtml_Productauction_Import 
	extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('auction/import.phtml');
    }
	
	public function getImportUrl()
	{
		return $this->getUrl('*/*/importPost',array());
	}
}