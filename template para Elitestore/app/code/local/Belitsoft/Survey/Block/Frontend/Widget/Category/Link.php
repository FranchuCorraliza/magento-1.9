<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Mageplace
 */

/**
 * Widget to display link to survey category page
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Mageplace
 */

class Belitsoft_Survey_Block_Frontend_Widget_Category_Link
	extends Mage_Core_Block_Html_Link
	implements Mage_Widget_Block_Interface
{
	/**
	 * Prepared href attribute
	 *
	 * @var string
	 */
	protected $_href;

	/**
	 * Prepared title attribute
	 *
	 * @var string
	 */
	protected $_title;

	/**
	 * Prepared anchor text
	 *
	 * @var string
	 */
	protected $_anchorText;

	/**
	 * Prepare page url. Use passed identifier
	 * or retrieve such using passed page id.
	 *
	 * @return string
	 */
	public function getHref()
	{
		if(!$this->_href) {
			$this->_href = '';
			if ($this->getData('href')) {
				$this->_href = $this->getData('href');
			} else if($this->getData('id_path')) {
				$this->_href = Mage::helper('belitsoft_survey')->getCategoryUrl($this->getData('id_path'));
			}
		}

		return $this->_href;
	}

	/**
	 * Prepare anchor title attribute using passed title
	 * as parameter or retrieve category title from DB using passed identifier or category id.
	 *
	 * @return string
	 */
	public function getTitle()
	{
		if (!$this->_title) {
			$this->_title = '';
			if ($this->getData('title') !== null) {
				// compare to null used here bc user can specify blank title
				$this->_title = $this->getData('title');
			} else if ($this->getData('id_path')) {
				$this->_title = Mage::getResourceSingleton('belitsoft_survey/category')->getSurveyCategoryTitleById($this->getData('id_path'));
			}
		}

		return $this->_title;
	}

	/**
	 * Prepare anchor text using passed text as parameter.
	 * If anchor text was not specified use title instead and
	 * if title will be blank string, page identifier will be used.
	 *
	 * @return string
	 */
	public function getAnchorText()
	{
		if ($this->getData('anchor_text')) {
			$this->_anchorText = $this->getData('anchor_text');
		} else if ($this->getData('id_path')) {
			$this->_anchorText = Mage::getResourceSingleton('belitsoft_survey/category')->getSurveyCategoryTitleById($this->getData('id_path'));
		} else if ($this->getTitle()) {
			$this->_anchorText = $this->getTitle();
		} else {
			$this->_anchorText = $this->getData('href');
		}

		return $this->_anchorText;
	}
}
