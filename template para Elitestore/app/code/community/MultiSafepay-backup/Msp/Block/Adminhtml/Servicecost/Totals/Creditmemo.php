<?php

class MultiSafepay_Msp_Block_Adminhtml_Servicecost_Totals_Creditmemo extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals {
    /* protected function _initTotals() {
      parent::_initTotals();
      $order = $this->getSource()->getOrder();
      $amount =  $order->getServicecostRefunded();
      $tax = $order->getServicecostTax();

      $code = $order->getPayment()->getMethod();

      $method = $order->getPayment()->getMethodInstance();

      if ($amount) {
      $this->addTotalBefore(new Varien_Object(array(
      'code' => 'servicecost',
      'value' => $amount,
      'base_value' => $this->_convertFeeCurrency($amount, $order->getOrderCurrencyCode(), $order->getGlobalCurrencyCode()),
      'label' => Mage::helper('msp')->getFeeLabel($code).' (incl Tax)'
      ), array('tax'))
      );

      $creditmemo = $this->getCreditMemo();
      $creditmemo->setBaseTaxAmount($creditmemo->getBaseTaxAmount() + $tax);
      $creditmemo->setTaxAmount($creditmemo->getTaxAmount() + $tax);
      $creditmemo->setBaseGrandTotal($order->getBaseTotalRefunded());
      $creditmemo->setGrandTotal($order->getTotalRefunded());
      $creditmemo->save();
      }
      return $this;
      } */

    protected function _initTotals() {
        parent::_initTotals();
        $order = $this->getSource()->getOrder();
        $amount = $order->getServicecostPdf();
        $tax = $order->getServicecostTax();

        $code = $order->getPayment()->getMethod();

        $method = $order->getPayment()->getMethodInstance();

        if ($amount) {
            $this->addTotalBefore(new Varien_Object(array(
                'code' => 'servicecost',
                'value' => $amount,
                'base_value' => $this->_convertFeeCurrency($amount, $order->getOrderCurrencyCode(), $order->getGlobalCurrencyCode()),
                'label' => Mage::helper('msp')->getFeeLabel($code)
                    ), array('tax'))
            );


            $creditmemo = $this->getCreditMemo();
            $creditmemo->setBaseTaxAmount($creditmemo->getBaseTaxAmount() + $tax);
            $creditmemo->setTaxAmount($creditmemo->getTaxAmount() + $tax);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $amount + $tax);
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $amount + $tax);
        }
        return $this;
    }

    protected function _convertFeeCurrency($amount, $currentCurrencyCode, $targetCurrencyCode) {
        if ($currentCurrencyCode == $targetCurrencyCode) {
            return $amount;
        }

        $currentCurrency = Mage::getModel('directory/currency')->load($currentCurrencyCode);
        $rateCurrentToTarget = $currentCurrency->getAnyRate($targetCurrencyCode);

        if ($rateCurrentToTarget === false) {
            Mage::throwException(Mage::helper("msp")->__("Imposible convert %s to %s", $currentCurrencyCode, $targetCurrencyCode));
        }

        //Disabled check, fixes PLGMAG-10. Magento seems to not to update USD->EUR rate in db, resulting in wrong conversions. Now we calculate the rate manually and and don't trust Magento stored rate.
        // if (strlen((string) $rateCurrentToTarget) < 12) { 
        $revertCheckingCode = Mage::getModel('directory/currency')->load($targetCurrencyCode);
        $revertCheckingRate = $revertCheckingCode->getAnyRate($currentCurrencyCode);
        $rateCurrentToTarget = 1 / $revertCheckingRate;
        //}

        return round($amount * $rateCurrentToTarget, 2);
    }

}
