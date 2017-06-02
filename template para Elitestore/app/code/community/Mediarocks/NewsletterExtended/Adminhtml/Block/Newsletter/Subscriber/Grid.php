<?php
/**
 * Media Rocks GbR
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled with 
 * this package in the file MEDIAROCKS-LICENSE-COMMUNITY.txt.
 * It is also available through the world-wide-web at this URL:
 * http://solutions.mediarocks.de/MEDIAROCKS-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package is designed for Magento COMMUNITY edition. 
 * Media Rocks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Media Rocks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please send an email to support@mediarocks.de
 *
 */

/**
 * Adminhtml newsletter subscribers grid block
 *
 * @category   Mediarocks
 * @package    Mediarocks_NewsletterExtended
 * @author     Media Rocks Developer
 */

class Mediarocks_NewsletterExtended_Adminhtml_Block_Newsletter_Subscriber_Grid extends Mage_Adminhtml_Block_Newsletter_Subscriber_Grid
{
	protected function _prepareCollection()
	{
        $collection = Mage::getResourceSingleton('newsletter/subscriber_collection');
        $collection->showCustomerInfo();
        
        // add custom fields
        if (Mage::getStoreConfig('newsletterextended/fields/show_gender'))
            $collection->showCustomerGender();
        if (Mage::getStoreConfig('newsletterextended/fields/show_prefix'))
            $collection->showCustomerPrefix();
        if (Mage::getStoreConfig('newsletterextended/fields/show_suffix'))
            $collection->showCustomerSuffix();
        if (Mage::getStoreConfig('newsletterextended/fields/show_dob'))
            $collection->showCustomerDob();
        // <- end add custom fields
        
        $collection->addSubscriberTypeField()
            ->showStoreInfo();

        if($this->getRequest()->getParam('queue', false)) {
            $collection->useQueue(Mage::getModel('newsletter/queue')
                ->load($this->getRequest()->getParam('queue')));
        }

        $this->setCollection($collection);
		
		/* 	we have to copy the following lines from Mage_Adminhtml_Block_Widget_Grid because we need  
			a new collection but Mage_Adminhtml_Block_Newsletter_Subscriber_Grid would overwrite it */
        if ($this->getCollection()) {

            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter   = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter)) {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter)) {
                $data = $this->helper('adminhtml')->prepareFilterString($filter);
                $this->_setFilterValues($data);
            }
            else if ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            }
            else if(0 !== sizeof($this->_defaultFilter)) {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $this->_setCollectionOrder($this->_columns[$columnId]);
            }

            if (!$this->_isExport) {
                $this->getCollection()->load();
                $this->_afterLoadCollection();
            }
        }
    }

	protected function _prepareColumns()
	{
		// prepare columns and sort them by order (see Mage_Adminhtml_Block_Widget_Grid)
		parent::_prepareColumns();
		
		// remove old columns
        $this->mrRemoveColumn('gender'); // futureproof
        $this->mrRemoveColumn('prefix'); // futureproof
        $this->mrRemoveColumn('firstname');
        $this->mrRemoveColumn('lastname');
        $this->mrRemoveColumn('suffix'); // futureproof
        $this->mrRemoveColumn('dob'); // futureproof
		
		// add new columns
        if (Mage::getStoreConfig('newsletterextended/fields/show_gender')) {
    		$this->addColumnAfter('gender', array(
    			'header'    => Mage::helper('newsletter')->__('Gender'),
                'index'     => 'customer_gender',
                'type'      => 'options',
                'options'   => array(
                    1  => Mage::helper('newsletter')->__('Mr'),
                    2  => Mage::helper('newsletter')->__('Ms/Mrs')
                ),
    			'renderer'	=> 'Mediarocks_NewsletterExtended_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Gender'
    		), 'type');
        }
        
        if (Mage::getStoreConfig('newsletterextended/fields/show_prefix')) {
            $this->addColumnAfter('prefix', array(
                'header'    => Mage::helper('newsletter')->__('Prefix'),
                'index'     => 'customer_prefix',
                'renderer'  => 'Mediarocks_NewsletterExtended_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Prefix'
            ), Mage::getStoreConfig('newsletterextended/fields/show_gender') ? 'gender' : 'type');
        }
		
		$this->addColumnAfter('firstname', array(
			'header'    => Mage::helper('newsletter')->__('First Name'),
            'index'     => 'customer_firstname',
			'renderer'	=> 'Mediarocks_NewsletterExtended_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Firstname'
		), Mage::getStoreConfig('newsletterextended/fields/show_prefix') ? 'prefix' : (Mage::getStoreConfig('newsletterextended/fields/show_gender') ? 'gender' : 'type'));
		
		$this->addColumnAfter('lastname', array(
			'header'    => Mage::helper('newsletter')->__('Last Name'),
            'index'     => 'customer_lastname',
			'renderer'	=> 'Mediarocks_NewsletterExtended_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Lastname'
		), 'firstname');
        
        if (Mage::getStoreConfig('newsletterextended/fields/show_suffix')) {
            $this->addColumnAfter('suffix', array(
                'header'    => Mage::helper('newsletter')->__('Country'),
                'index'     => 'customer_suffix',
                'renderer'  => 'Mediarocks_NewsletterExtended_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Suffix'
            ), 'lastname');
        }
        
        if (Mage::getStoreConfig('newsletterextended/fields/show_dob')) {
            $this->addColumnAfter('dob', array(
                'header'    => Mage::helper('newsletter')->__('Date of Birth'),
                'index'     => 'customer_dob',
                'renderer'  => 'Mediarocks_NewsletterExtended_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Dob'
            ), Mage::getStoreConfig('newsletterextended/fields/show_suffix') ? 'suffix' : 'lastname');
        }
        
        if (Mage::getStoreConfig('newsletterextended/fields/show_channels')) {
            $this->addColumnAfter('channels', array(
                'header'    => Mage::helper('newsletter')->__('Channels'),
                'index'     => 'customer_channels',
                'renderer'  => 'Mediarocks_NewsletterExtended_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Channels'
            ), Mage::getStoreConfig('newsletterextended/fields/show_dob') ? 'dob' : (Mage::getStoreConfig('newsletterextended/fields/show_suffix') ? 'suffix' : 'lastname'));
        }

		// manually sort again, that our custom order works
		$this->sortColumnsByOrder();
		
        return $this;
    }

    /**
     * Wrapper for removeColumn()
     * removeColumn is missing in Magento Professional so we add a fallback;
     *
     * @param string $columnId
     * @return Mediarocks_NewsletterExtended_Adminhtml_Block_Newsletter_Subscriber_Grid
     */
    public function mrRemoveColumn($columnId)
    {
        if (method_exists($this, "removeColumn")) {
            return $this->removeColumn($columnId);
        }
        else if (isset($this->_columns[$columnId])) {
            unset($this->_columns[$columnId]);
            if ($this->_lastColumnId == $columnId) {
                $this->_lastColumnId = key($this->_columns);
            }
        }
        return $this;
    }

}
