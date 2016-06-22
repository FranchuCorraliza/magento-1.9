<?php

class Wyomind_Ordersexporttool_Model_Checkout {

    public function export($observer) {

        foreach (explode(',', Mage::getStoreConfig("ordersexporttool/system/execute_on_checkout", $observer->getOrder()->getStoreId())) as $profileId) {
            $profile = Mage::getModel('ordersexporttool/profiles')->load($profileId);
            if ($profile->getFileId())
                $profile->generateFile();
        }
    }

}
