<?php
/**
 * Survey config model
 *
 * @category   Belitsoft
 * @package    Belitsoft_Survey
 * @author     Mageplace
 */
class Belitsoft_Survey_Model_Config extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		parent::_construct();

		$this->_init('belitsoft_survey/config');  	  		
	}

	/**
	 * Set config item
	 *
	 * @return Belitsoft_Sugarcrm_Model_Config
	 */
	public function setConfigData($name, $value)
	{
		$this->setData('name', $name);
		$this->setData('value', $value);

		return $this;
	}
	
	/**
	 *  Return config var
	 *
	 *  @param	string $key Var path key
	 *  @param	int $storeId Store View Id
	 *  @return	  mixed
	 */
	public function getConfigData($key, $section=null, $storeId = null)
	{
		if (!is_null($section) && !$this->hasData($key)) {
			$value = Mage::getStoreConfig('survey/' . $section . '/' . $key, $storeId);
			$this->setData($key, $value);
		} else {
			$read = $this->_getResource()->getReadConnection();
			$select = $read->select();
			$select->from($this->_getResource()->getMainTable(), array('value'))
				->where('`name` = ?', $key);
			$value = $read->fetchOne($select);
			
			$this->setData($key, $value);
		}
		
		return $this->getData($key);
	}
}