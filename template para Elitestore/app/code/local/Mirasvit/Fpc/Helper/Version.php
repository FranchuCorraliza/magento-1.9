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



class Mirasvit_Fpc_Helper_Version extends Mage_Core_Helper_Abstract
{
    public function getExtensionVersion($buildVersion = true) {
        if ($helper = $this->_getCodeHelper('Fpc')) {
            if (method_exists($helper, '_sku')
                && method_exists($helper, '_version')
                && method_exists($helper, '_build')
                && method_exists($helper, '_key')) {
                $extension = array(
                    'v' => $helper->_version(),
                );

                $build = array(
                    'r' => $helper->_build(),
                );

                if ($buildVersion) {
                    $extension = array_merge($extension, $build);
                }

                return implode('.',$extension);
            }
        }

        return false;
    }

    private function _getCodeHelper($moduleName)
    {
        $file = Mage::getBaseDir().'/app/code/local/Mirasvit/'.$moduleName.'/Helper/Code.php';

        if (file_exists($file)) {
            $helper = Mage::helper(strtolower($moduleName).'/code');
            return $helper;
        }

        return false;
    }

    public function getCurrentFpcVersion($buildVersion = true) {
        return ($this->getExtensionVersion(false)) ? $this->getExtensionVersion(false) : '1.0.13';
    }
}
