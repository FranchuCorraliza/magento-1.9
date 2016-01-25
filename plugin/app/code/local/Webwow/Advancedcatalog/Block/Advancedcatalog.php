<?php
class Webwow_Advancedcatalog_Block_Advancedcatalog extends Mage_Catalog_Block_Product_View
{
    public function _prepareLayout()
    {
    	$this->getProduct()->setName($this->getProduct()->getShortDescription());

        return parent::_prepareLayout();
    }

    public function getHelloworld()
    {
        return 'Hello world';
    }
}

?>