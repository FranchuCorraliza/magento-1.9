<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup
{
    /** @var array paths used in module config */
    protected $_fields = array(
        'enabled', 'enable_shipping_price_edition', 'show_thumbnails', 'thumbnail_height', 'send_update_email'
    );

    /**
     * Rename old paths and save changes to core config data
     *
     * @param Mage_Core_Model_Config_Data $conf
     * @param string $realPath
     */
    protected function updatePathFor18verAndOlder(Mage_Core_Model_Config_Data $conf, $realPath)
    {
        $realPath = str_replace('orderspro', 'ordersedit', $realPath);
        $realPath = str_replace('mageworx_sales', 'mageworx_ordersmanagement', $realPath);
        $conf->setPath($realPath)->save();
    }

    /**
     * Rename old paths and save changes to core config data
     *
     * @param Mage_Core_Model_Config_Data $conf
     * @param string $realPath
     */
    protected function updatePathFor19ver(Mage_Core_Model_Config_Data $conf, $realPath)
    {
        $realPath = str_replace('general', 'ordersedit', $realPath);
        $realPath = str_replace('mageworx_orderspro', 'mageworx_ordersmanagement', $realPath);
        $conf->setPath($realPath)->save();
    }

    /**
     * Return all fields used in module config
     * @return array
     */
    public function getModuleConfigFields()
    {
        return $this->_fields;
    }

    /**
     * Update old config data. Rename & save.
     * void
     */
    public function updateConfig()
    {
        // Change old config paths (ver < 1.19.0)
        $pathLike = 'mageworx_sales/orderspro/%';
        /** @var Mage_Core_Model_Resource_Config_Data_Collection $configCollection */
        $configCollection = Mage::getModel('core/config_data')->getCollection();
        $configCollection->getSelect()->where('path like ?', $pathLike);

        /** @var array $fields */
        $fields = $this->getModuleConfigFields();
        /** @var Mage_Core_Model_Config_Data $conf */
        foreach ($configCollection as $conf) {
            $realPath = $conf->getPath();
            $path = explode('/', $realPath);
            $lastElement = array_pop($path);

            if (in_array($lastElement, $fields)) {
                $this->updatePathFor18verAndOlder($conf, $realPath);
            }
        }

        // Change old config paths (ver >= 1.19.0)
        $pathLike = 'mageworx_orderspro/general/%';
        /** @var Mage_Core_Model_Resource_Config_Data_Collection $configCollection */
        $configCollection = Mage::getModel('core/config_data')->getCollection();
        $configCollection->getSelect()->where('path like ?', $pathLike);

        /** @var array $fields */
        $fields = $this->getModuleConfigFields();
        /** @var Mage_Core_Model_Config_Data $conf */
        foreach ($configCollection as $conf) {
            $realPath = $conf->getPath();
            $path = explode('/', $realPath);
            $lastElement = array_pop($path);

            if (in_array($lastElement, $fields)) {
                $this->updatePathFor19ver($conf, $realPath);
            }
        }
    }
}