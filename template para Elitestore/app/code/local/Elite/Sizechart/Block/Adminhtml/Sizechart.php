<?php
class Elite_Sizechart_Block_Adminhtml_Sizechart extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_sizechart';
        $this->_blockGroup = 'sizechart';
        $this->_headerText = Mage::helper('sizechart')->__('Item Manager');
        $this->_addButtonLabel = Mage::helper('sizechart')->__('Add Item');
        parent::__construct();
    }
}
?>