<?php
/**
 * Adminhtml Belitsoft Survey Config form block
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Mageplace
 */

class Belitsoft_Survey_Block_Adminhtml_Config extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		$this->_blockGroup = 'belitsoft_survey';
		$this->_controller = 'adminhtml';
		$this->_mode = 'config';
        
		parent::__construct();
        
		$this->_removeButton('back');
		$this->_removeButton('reset');
		$this->_updateButton('save', 'label', $this->__('Save settings'));
		$this->_updateButton('save', 'id', 'save_button');
	}

	public function getHeaderText()
	{
		return $this->__('Survey Settings');
	}

    public function getHeaderCssClass()
    {
		return 'icon-head head-backups-control';
    }
}