<?php

class Wyomind_Ordersexporttool_OrdersexporttoolController extends Mage_Core_Controller_Front_Action {

    public function generateAction() {
        // http://www.example.com/index.php/ordersexporttool/ordersexporttool/generate/id/{file_id}/ak/{YOUR_ACTIVATION_KEY}
        
        $id = $this->getRequest()->getParam('id');
        $ak=$this->getRequest()->getParam('ak');
        
        $activation_key=Mage::getStoreConfig("ordersexporttool/license/activation_key");
        
        if($activation_key==$ak) {


            $ordersexporttool = Mage::getModel('ordersexporttool/profiles');
            $ordersexporttool->setId($id);
            if ($ordersexporttool->load($id)) {
                try {
                    $ordersexporttool->generateFile();
                    die(Mage::helper('ordersexporttool')->__('The export file "%s" has been generated.', $ordersexporttool->getFileName()));
                } catch (Mage_Core_Exception $e) {
                    die($e->getMessage());
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                die(Mage::helper('ordersexporttool')->__('Unable to find an export file to generate.'));
            }
        } else die('Invalid activation key');
    }

}

