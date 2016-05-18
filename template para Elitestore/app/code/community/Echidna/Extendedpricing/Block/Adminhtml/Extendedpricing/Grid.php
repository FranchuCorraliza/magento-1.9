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

class Echidna_Extendedpricing_Block_Adminhtml_Extendedpricing_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("extendedpricingGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("extendedpricing/extendedpricing")->getCollection(); 
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
			        

                                $this->addColumn("sku", array(
				"header" => Mage::helper("extendedpricing")->__("SKU"),
				"index" =>"sku",
				));
				
				$this->addColumn("priceregion", array(
				"header" => Mage::helper("extendedpricing")->__("Price Region"),
				"index" => "priceregion",
                                'type'  => 'options',
                                'options'=> Mage::helper('extendedpricing')->priceregion()    
				));
				
				$this->addColumn("subpriceregion", array(
				"header" => Mage::helper("extendedpricing")->__("Price Book"),
				"index" => "subpriceregion",
                                 'type'  => 'options',
                                'options'=> Mage::helper('extendedpricing')->pricebook()
				));
				
				$this->addColumn("price", array(
				"header" => Mage::helper("extendedpricing")->__("Price"),
				"index" => "price",
				));
				
						
				$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
				$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_pricesheet', array(
					 'label'=> Mage::helper('extendedpricing')->__('Remove Pricesheet'),
					 'url'  => $this->getUrl('*/adminhtml_extendedpricing/massRemove'),
					 'confirm' => Mage::helper('extendedpricing')->__('Are you sure?')
				));
			return $this;
		}
			
}