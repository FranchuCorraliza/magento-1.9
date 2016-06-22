<?php
/**
 * Mageplace Survey
 *
 * @category	Belitsoft
 * @package		Belitsoft_Survey
 * @copyright	Copyright (c) 2012 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Belitsoft_Survey_ManageController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Action predispatch
	 *
	 * Check customer authentication for some actions
	 */
	public function preDispatch()
	{
		parent::preDispatch();
		
		if (!Mage::getSingleton('customer/session')->authenticate($this) || !Mage::helper('belitsoft_survey')->isCustomerCanView()) {
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		}
	}

	/**
	 * Displays the Survey categories list.
	 */
	public function indexAction()
	{
		$this->loadLayout();
		$this->_initLayoutMessages('catalog/session');

		if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
			$navigationBlock->setActive(Mage::helper('belitsoft_survey')->getSurveyManagePage());
		}
		if ($block = $this->getLayout()->getBlock('customer_surveys')) {
			$block->setRefererUrl($this->_getRefererUrl());
		}

		$this->renderLayout();
	}
}