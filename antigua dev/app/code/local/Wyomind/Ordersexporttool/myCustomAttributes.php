<?php

/* ---------------------------------------------------------------------------------------------------------- */
/* FOR DEVELOPERS ONLY                                                                                        */
/* ---------------------------------------------------------------------------------------------------------- */
/* ---------------------------------------------------------------------------------------------------------- */
/* * ************ DO NOT CHANGE THESE LINES **************                                        */
/* ---------------------------------------------------------------------------------------------------------- */

class Wyomind_Ordersexporttool_Model_MyCustomAttributes extends Wyomind_Ordersexporttool_Model_Profiles {
    
    public function __construct(){
         $this->attributes = Mage::getModel('ordersexporttool/attributes')->getCollection();
    }
    
    /* --------------------------------------------------------------------------------------------------------- */
    /* this method retrieves the available custom attributes into the library                                    */
    /* --------------------------------------------------------------------------------------------------------- */
   
    public function _getAll() {

       
        $attr = array();
        foreach ($this->attributes as $attribute) {
            $attr['Custom Attributes'][] = $attribute->getAttributeName();
        }
        return $attr;
    }

   

    /* ---------------------------------------------------------------------------------------------------------- */
    /* this method transforms the custom attributes to a computed value                                           */
    /* ---------------------------------------------------------------------------------------------------------- */

    public function _eval($order, $item, $data=array('products' => array(), 'payments' => array(), 'invoices' => array(), 'shipments' => array(), 'creditmemos' => array()), $exp, $value) {

       

        foreach ($this->attributes as $attribute) {

            if ($exp['pattern'] == "{" . $attribute->getAttributeName() . "}") {

                eval(str_replace('return', '$value =', $attribute->getAttributeScript()));
            }
            
        }
        return $value;
    }

}