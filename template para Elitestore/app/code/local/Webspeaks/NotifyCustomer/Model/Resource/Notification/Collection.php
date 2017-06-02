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
 * Notification collection resource model
 *
 * @category    Webspeaks
 * @package     Webspeaks_NotifyCustomer
 * @author      Webspeaks
 */
class Webspeaks_NotifyCustomer_Model_Resource_Notification_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_joinedFields = array();

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Webspeaks
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('webspeaks_notifycustomer/notification');
    }

    /**
     * get notifications as array
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     * @author Webspeaks
     */
    protected function _toOptionArray($valueField='entity_id', $labelField='title', $additional=array())
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * get options hash
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @return array
     * @author Webspeaks
     */
    protected function _toOptionHash($valueField='entity_id', $labelField='title')
    {
        return parent::_toOptionHash($valueField, $labelField);
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @access public
     * @return Varien_Db_Select
     * @author Webspeaks
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }

    /**
     * Add customer name in grid
     *
     * @access public
     * @return Varien_Db_Select
     * @author Webspeaks
     */
    public function addCustomerNameToSelect()
    {
        $tbl = Mage::getSingleton('core/resource')->getTableName('customer_entity_varchar');
        $firstnameAttr = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname');
        $lastnameAttr = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname');
        $this->getSelect()
            ->join(array('ce1' => $tbl), 'ce1.entity_id=main_table.customer_id', array('firstname' => 'value'))
            ->where('ce1.attribute_id='.$firstnameAttr->getAttributeId()) // Attribute code for firstname.
            ->join(array('ce2' => $tbl), 'ce2.entity_id=main_table.customer_id', array('lastname' => 'value'))
            ->where('ce2.attribute_id='.$lastnameAttr->getAttributeId()) // Attribute code for lastname.
            ->columns(new Zend_Db_Expr("CONCAT(`ce1`.`value`, ' ',`ce2`.`value`) AS customer_name"));
        return $this;
    }
}
