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
 * Notification admin grid block
 *
 * @category    Webspeaks
 * @package     Webspeaks_NotifyCustomer
 * @author      Ultimate Module Creator
 */
class Webspeaks_NotifyCustomer_Block_Adminhtml_Notification_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('notificationGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _customerNameCondition($collection, $column) {

        $filterValue = $column->getFilter()->getValue();
        if(!is_null($filterValue)){
            $filterValue = trim($filterValue);
            $filterValue = preg_replace('/[\s]+/', ' ', $filterValue);

            $whereArr = array();
            $whereArr[] = $collection->getConnection()->quoteInto("ce1.value = ?", $filterValue);
            $whereArr[] = $collection->getConnection()->quoteInto("ce2.value = ?", $filterValue);
            $where = implode(' OR ', $whereArr);
            $collection->getSelect()->where($where);
        }
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Webspeaks_NotifyCustomer_Block_Adminhtml_Notification_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('webspeaks_notifycustomer/notification')
            ->getCollection()
            ->addCustomerNameToSelect();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Webspeaks_NotifyCustomer_Block_Adminhtml_Notification_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('webspeaks_notifycustomer')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'title',
            array(
                'header'    => Mage::helper('webspeaks_notifycustomer')->__('Title'),
                'align'     => 'left',
                'index'     => 'title',
            )
        );
        $this->addColumn(
            'customer_name',
            array(
                'header' => Mage::helper('webspeaks_notifycustomer')->__('Customer'),
                'index'  => 'customer_name',
                'type'=> 'text',
                'filter_condition_callback' => array($this, '_customerNameCondition')
            )
        );
        $this->addColumn(
            'status',
            array(
                'header' => Mage::helper('webspeaks_notifycustomer')->__('Read By Customer'),
                'index'  => 'status',
                'type'    => 'options',
                    'options'    => array(
                    '1' => Mage::helper('webspeaks_notifycustomer')->__('Yes'),
                    '0' => Mage::helper('webspeaks_notifycustomer')->__('No'),
                )

            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('webspeaks_notifycustomer')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('webspeaks_notifycustomer')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('webspeaks_notifycustomer')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('webspeaks_notifycustomer')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('webspeaks_notifycustomer')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('webspeaks_notifycustomer')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return Webspeaks_NotifyCustomer_Block_Adminhtml_Notification_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('notification');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('webspeaks_notifycustomer')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('webspeaks_notifycustomer')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('webspeaks_notifycustomer')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('webspeaks_notifycustomer')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('webspeaks_notifycustomer')->__('Enabled'),
                            '0' => Mage::helper('webspeaks_notifycustomer')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'read',
            array(
                'label'      => Mage::helper('webspeaks_notifycustomer')->__('Change Read By Customer'),
                'url'        => $this->getUrl('*/*/massRead', array('_current'=>true)),
                'additional' => array(
                    'flag_read' => array(
                        'name'   => 'flag_read',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('webspeaks_notifycustomer')->__('Read By Customer'),
                        'values' => array(
                                '1' => Mage::helper('webspeaks_notifycustomer')->__('Yes'),
                                '0' => Mage::helper('webspeaks_notifycustomer')->__('No'),
                            )

                    )
                )
            )
        );
        return $this;
    }

    /**
     * get the row url
     *
     * @access public
     * @param Webspeaks_NotifyCustomer_Model_Notification
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * get the grid url
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * after collection load
     *
     * @access protected
     * @return Webspeaks_NotifyCustomer_Block_Adminhtml_Notification_Grid
     * @author Ultimate Module Creator
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
