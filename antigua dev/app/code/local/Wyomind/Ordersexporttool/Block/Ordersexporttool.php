<?php
class Wyomind_Ordersexporttool_Block_Ordersexporttool extends Mage_Core_Block_Template
{
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

	public function getOrdersexporttool()
	{
		if (!$this->hasData('ordersexporttool')) {
			$this->setData('ordersexporttool', Mage::registry('ordersexporttool'));
		}
		return $this->getData('ordersexporttool');

	}
}