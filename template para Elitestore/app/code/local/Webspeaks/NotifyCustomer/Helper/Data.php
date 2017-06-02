<?php
/**
 * Webspeaks_NotifyCustomer extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Webspeaks
 * @package        Webspeaks_NotifyCustomer
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * NotifyCustomer default helper
 *
 * @category    Webspeaks
 * @package     Webspeaks_NotifyCustomer
 * @author      Ultimate Module Creator
 */
class Webspeaks_NotifyCustomer_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array
     * @author Ultimate Module Creator
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }

    public function getCustomerArray($q = false, $customerId = false, $addressId = false)
    {
        $model = Mage::getSingleton('customer/customer');
        $result = $model->getCollection()
                ->addAttributeToSelect(array('entity_id', 'firstname', 'email'))
                ->addAttributeToFilter(
                    array(
                        array('attribute'=>'firstname', 'like' => "%$q%"),
                        array('attribute'=>'lastname', 'like' => "%$q%"),
                        array('attribute'=>'email', 'like' => "%$q%"),
                    )
                )
                ;

        $data = [];
        foreach($result as $r) {       
            $data[] = [
                'name' => $r->getData('firstname') . ' ' . $r->getData('lastname') . ' (' . $r->getData('email') . ')',
                'email' => $r->getData('email'),
                'id' => $r->getData('entity_id'),
            ];
        }

        return $data;
    }

    public function findCustomer($q)
    {
        $customers = $this->getCustomerArray($q);
        return $customers;
    }

    public function findCustomerByEmail($email)
    {
        $model = Mage::getSingleton('customer/customer');
        $result = $model->getCollection()
                ->addAttributeToFilter('email', $email)
                ->getFirstItem();
        echo $result->getSelect();
        return $result;
    }

    public function sendEmailToCustomer($data)
    {
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('ws_notify_customer_eml');
        $from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email Admin
        $from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name Admin

        $emailTemplateVariables = array();
        $emailTemplateVariables['body'] = $data['message'];

        //Appending the Custom Variables to Template.
        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);

        //Sending E-Mail to Customers.
        $mail = Mage::getModel('core/email')
            ->setToName($data['customer_email'])
            ->setToEmail($data['customer_email'])
            ->setBody($processedTemplate)
            ->setSubject($data['title'])
            ->setFromEmail($from_email)
            ->setFromName($from_name)
            ->setType('html');

        try {
            //E-Mail Send
            $mail->send();
        }
        catch(Exception $error)
        {
            Mage::getSingleton('core/session')->addError($error->getMessage());
            return false;
        }
    }
}
