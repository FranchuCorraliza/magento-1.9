<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
	 * Constructor for Adminhtml Category Block
	 */
	public function __construct()
	{
		$this->_blockGroup = 'belitsoft_survey';
		$this->_controller = 'adminhtml_category';

		$this->_addButtonLabel = $this->__('Add New Category');
		
		parent::__construct();
	}

	public function getHeaderText()
	{
		return $this->__('Manage Categories');
	}
	
    /**
     * Returns the CSS class for the header
     * 
     * Usually 'icon-head' and a more precise class is returned. We return
     * only an empty string to avoid spacing on the left of the header as we
     * don't have an icon.
     * 
     * @return string
     */
	public function getHeaderCssClass()
	{
		return '';
	}
}
