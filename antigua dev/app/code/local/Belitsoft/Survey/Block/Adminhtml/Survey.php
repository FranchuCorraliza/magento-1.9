<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Survey extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor for Survey Adminhtml Block
     */
    public function __construct()
    {
        $this->_blockGroup = 'belitsoft_survey';
        $this->_controller = 'adminhtml_survey';
        $this->_headerText = $this->__('Manage Surveys');
        $this->_addButtonLabel = $this->__('Add New Survey');
        
        parent::__construct();
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
