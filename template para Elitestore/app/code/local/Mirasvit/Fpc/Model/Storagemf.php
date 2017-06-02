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



class Mirasvit_Fpc_Model_Storagemf extends Varien_Object
{
    /**
     * @return Mirasvit_Fpc_Model_Storagemf
     */
    public static function getInstance()
    {
        if (Mage::registry('current_storage')) {
            return Mage::registry('current_storage');
        }

        $instance = new self();

        Mage::register('current_storage', $instance);

        return $instance;
    }

    /**
     * @return void
     */
    public function save()
    {
        if (Mage::registry('current_category')) {
            $this->setCurrentCategory(Mage::registry('current_category')->getId());
        }
        if (Mage::registry('current_product')) {
            $this->setCurrentProduct(Mage::registry('current_product')->getId());
        }
        if (Mage::getSingleton('cms/page')->getId()) {
            $this->setCurrentCmsPage(Mage::getSingleton('cms/page')->getId());
        }

        // save design settings
        $design = Mage::getSingleton('core/design_package');
        $this->setThemeLayout($design->getTheme('layout'))
            ->setThemeTemplate($design->getTheme('template'))
            ->setThemeSkin($design->getTheme('skin'))
            ->setThemeLocale($design->getTheme('locale'))
            ->setCreatedAt(time());

        $cache = Mirasvit_Fpc_Model_Cache::getCacheInstance();
        $cache->save(serialize($this->getData()), $this->getCacheId(), $this->getCacheTags(), $this->getCacheLifetime());

        return $this;
    }

    /**
     * @return void
     */
    public function load()
    {
        $cache = Mirasvit_Fpc_Model_Cache::getCacheInstance();
        $data = $cache->load($this->getCacheId());

        if ($data) {
            $data = unserialize($data);
            $this->setData($data);

            return $this;
        }

        return false;
    }
}
