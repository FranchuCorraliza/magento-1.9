<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
	public function render(Varien_Object $row)
	{
		$this->getColumn()->setActions(
		array(
		 
		array(
		            'url'     => $this->getUrl('*/adminhtml_profiles/edit', array('id' => $row->getFile_id())),
		            'caption' => Mage::helper('ordersexporttool')->__('Edit'),
		),
		array(
		            'url'     => $this->getUrl('*/adminhtml_profiles/delete', array('id' => $row->getFile_id())),
		         	  'confirm'   =>  Mage::helper('ordersexporttool')->__('Are you sure you want to delete this File ?'),
		            'caption' => Mage::helper('ordersexporttool')->__('Delete'),
		 

		),

		array(
		            'url'     => $this->getUrl('*/adminhtml_profiles/sample', array('file_id' => $row->getFile_id(), 'limit'=>10)),
		           'caption' => Mage::helper('ordersexporttool')->__('Preview'). " (10 ".Mage::helper('ordersexporttool')->__('products').")" ,
		           'popup'     =>  true

		),
		 
		 
		array(
		            'url'     => $this->getUrl('*/adminhtml_profiles/generate', array('file_id' => $row->getFile_id())),
		            'confirm'   =>  Mage::helper('ordersexporttool')->__('Generate a export file can take a while. Are you sure you want to generate it now ?'),
		            'caption' => Mage::helper('ordersexporttool')->__('Generate'),

		),
		)
		);
		return parent::render($row);
	}
}
