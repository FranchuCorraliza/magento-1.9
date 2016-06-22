<?php
/**
 * Survey for Magento
 *
 * @category	Belitsoft
 * @package		Belitsoft_Survey
 * @author		Belitsoft <bits@belitsoft.com>
 */

class Belitsoft_Survey_Block_Adminhtml_Survey_Grid_Column_Renderer_Date
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Date
{
	/**
	 * Retrieve date format
	 *
	 * @return string
	 */
	protected function _getFormat()
	{
		$format = $this->getColumn()->getFormat();
		if (!$format) {
			if (is_null(self::$_format)) {
				try {
					self::$_format = Mage::app()->getLocale()->getDateTimeFormat(
						Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
					);
				} catch (Exception $e) {
				}
			}
			$format = self::$_format;
		}
		return $format;
	}

	/**
	 * Renders grid column
	 *
	 * @param Varien_Object $row
	 * @return string
	 */
	public function render(Varien_Object $row)
	{
		if ($data = $row->getData($this->getColumn()->getIndex())) {
			$format = $this->_getFormat();
			try {
				$data = Mage::getSingleton('core/locale')->date($data, Zend_Date::ISO_8601, null, false)->toString($format);
			}
			catch (Exception $e) {
				$data = Mage::getSingleton('core/locale')->date($data, Varien_Date::DATETIME_INTERNAL_FORMAT, null, false)->toString($format);
			}
			return $data;
		}
		
		return $this->getColumn()->getDefault();
	}
}
