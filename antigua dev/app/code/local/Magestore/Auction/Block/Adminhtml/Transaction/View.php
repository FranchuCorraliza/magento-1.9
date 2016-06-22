<?php

class Magestore_Auction_Block_Adminhtml_Transaction_View extends Mage_Adminhtml_Block_Template {

    public function __construct() {
        parent::_construct();
       
        $this->setTemplate('auction/transaction/view.phtml');

        return $this;
    }

    public function getTransaction() {
        $id = $this->getRequest()->getParam('id');

        $auction_product_table = Mage::getModel('core/resource')
                ->getTableName('auction_product');

        $order_table = Mage::getModel('core/resource')
                ->getTableName('sales/order');

        $collection = Mage::getModel('auction/transaction')->getCollection();
        $collection->getSelect()
                ->join($auction_product_table, "$auction_product_table.productauction_id = main_table.productauction_id", array(
                    'product_name' => 'product_name',
                        )
                )
                ->join(
                        $order_table, "$order_table.entity_id = main_table.order_id", array(
                    'order_number' => 'increment_id',
                    'created_date' => 'created_at',
                    'total_amount' => 'grand_total',
                        )
                )
                ->where("main_table.transaction_id = '$id'")
        ;
        return $collection;
    }

}
