<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Full Page Cache
 * @version   1.0.32
 * @build     662
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Fpc_Helper_Custom extends Mage_Core_Helper_Abstract
{
    protected static $_customSettings = null;

    /**
     * Check if exist configuration only for current store
     * @return bool|array
     */
    public function getCustomSettings()
    {
        if(self::$_customSettings !== null) {
            return self::$_customSettings;
        }

        $filePath = Mage::getModuleDir('Helper', 'Mirasvit_Fpc') . DS . 'Helper' .  DS . 'CustomDependence.php';

        if (file_exists($filePath)) {
            $customDependenceHelper = Mage::helper('fpc/customDependence');
            self::$_customSettings = get_class_methods($customDependenceHelper);
        } else {
            self::$_customSettings = false;
        }

        return self::$_customSettings;
    }
}
