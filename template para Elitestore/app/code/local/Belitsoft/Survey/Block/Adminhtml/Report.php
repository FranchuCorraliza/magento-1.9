<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Belitsoft <bits@belitsoft.com>
 */
class Belitsoft_Survey_Block_Adminhtml_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
	 * Constructor for Survey Adminhtml Block
	 */
	public function __construct()
	{
		$this->_blockGroup = 'belitsoft_survey';
		$this->_controller = 'adminhtml_report';
		$this->_headerText = $this->__('Reports');

		parent::__construct();

		$this->_removeButton('add');

		$js
			= "try{"
			. "var filter_value = $('filter_survey_id').getValue();"
			. "}catch(e){"
			. "var filter_value = $('survey_report_grid_filter_survey_id').getValue();"
			. "};"
			. "if(filter_value){"
			. "setLocation('%ssurvey_id/'+filter_value);"
			. "}else{"
			. "alert('" . str_replace("'", "\'", $this->__("Please apply 'Survey Name' filter")) . "')"
			. "}";

		$this->_addButton('view_results', array(
			'label' => $this->__('View Results'),
			'onclick' => sprintf($js, $this->getUrl('*/*/results/')),
			'class' => 'show-hide',
		));


		$this->_addButton('export_results_csv', array(
			'label' => $this->__('Export results to csv file'),
			'onclick' => sprintf($js, $this->getUrl('*/*/csvResults/')),
		));
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
