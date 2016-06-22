<?php

/**
 * Optimiseweb CookieNotice Model System Config Source Cmspages
 *
 * @package     Optimiseweb_CookieNotice
 * @author      Sid Vel (sid@optimiseweb.co.uk)
 * @copyright   Copyright (c) 2013 Optimiseweb Ltd
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Optimiseweb_CookieNotice_Model_System_Config_Source_Cmspages
{

    /**
     *
     * @return type
     */
    public function toOptionArray()
    {
        $cmsPagesArray = array();

        $pages = Mage::getModel('cms/page')->getCollection();

        foreach ($pages as $page) {
            $cmsPagesArray[$page->getIdentifier()] = $page->getTitle();
        }

        return $cmsPagesArray;
    }

}