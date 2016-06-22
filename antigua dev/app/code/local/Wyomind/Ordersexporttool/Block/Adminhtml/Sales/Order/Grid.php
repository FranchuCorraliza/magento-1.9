<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid {

    protected function _filterFlags($collection, $column) {

        if (!$value = $column->getFilter()->getValue()) {
            $this->getCollection()->addFieldToFilter('export_flag', array('eq' => null));
            return;
        }

        $this->getCollection()->addFieldToFilter('export_flag', array('finset' => $value));
    }

    protected function _prepareColumns() {

      

            $profiles = Mage::getModel('ordersexporttool/profiles')->getCollection();

            $exportation[0] = Mage::helper('ordersexporttool')->__('Not exported');

            foreach ($profiles as $p) {

                $exportation[$p->getFileId()] = $p->getFileName();
            }



            $this->addColumn('export_flag', array(
                'header' => Mage::helper('sales')->__('Exported to '),
                'index' => 'export_flag',
                'type' => 'options',
                'width' => '150px',
                'options' => $exportation,
                'renderer' => "Wyomind_Ordersexporttool_Block_Adminhtml_Renderer_Exportedto",
                'filter_condition_callback' => array($this, '_filterFlags'),
            ));

            //if(version_compare(Mage::getVersion(), '1.3.0', '>')) {

            $this->addColumnsOrder('export_flag', 'status');

            $this->sortColumnsByOrder();

            //}
       
        parent::_prepareColumns();
    }

}