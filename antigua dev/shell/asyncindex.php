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
 * @package   Fast Asynchronous Re-indexing
 * @version   1.1.6
 * @build     285
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


require_once 'abstract.php';

/**
 * @category Mirasvit
 * @package  Mirasvit_AsyncIndex
 */
class Mirasvit_Shell_Asyncindex extends Mage_Shell_Abstract
{
    public function run()
    {
        error_reporting(E_ALL);
        ini_set('error_reporting', E_ALL);
        ini_set('max_execution_time', 360000);
        set_time_limit(360000);

        $control = Mage::getSingleton('asyncindex/control');

        if ($this->getArg('control')) {
            $class  = $this->getArg('class');
            $method = $this->getArg('method');
            $args   = explode(',', $this->getArg('args'));

            $this->_execute($class, $method, $args, $this->getArg('async'));
        } elseif ($this->getArg('fill-queue')) {
            $this->_fillQueue();
        } elseif ($this->getArg('ping')) {
            echo Mirasvit_AsyncIndex_Model_Config::STATUS_OK;
        } elseif ($this->getArg('help')) {
            $this->usageHelp();
        } else {
            $control->run();
        }
    }

    protected function _execute($class, $method, $args, $async = false)
    {
        $object = new $class();

        if ($async) {
            $result = $object->execute($method, $args, false);
            if ($result) {
                Mage::helper('asyncindex')->error($args[0], implode("\n", $result));
            }
        } else {
            echo call_user_func_array(array($object, $method), $args);
        }

    }

    /**
     * ÑÐ¸Ð¼ÑÐ»Ð¸ÑÑÐµÐ¼ Ð¾ÑÐµÑÐµÐ´Ñ
     * ÑÐ¾ÑÑÐ°Ð½ÑÐµÐ¼ Ð½ÐµÑÐºÐ¾Ð»ÑÐºÐ¾ Ð¿ÑÐ¾Ð´ÑÐºÑÐ¾Ð²
     * ÑÐ¾ÑÑÐ°Ð½ÑÐµÐ¼ Ð½ÐµÑÐºÐ¾Ð»ÑÐºÐ¾ ÐºÐ°ÑÐµÐ³Ð¾ÑÐ¸Ð¹
     *
     * @return object
     */
    protected function _fillQueue()
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->getSelect()->order('rand()');

        foreach ($collection as $product) {
            echo '.';
            $product = Mage::getModel('catalog/product')->load($product->getId());
            $product->setName($product->getName())->setPrice($product->getPrice())->save();
        }

        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->getSelect()->order('rand()');

        foreach ($collection as $category) {
            echo '*';
            $category = $category->load($category->getId());
            $category->setName($category->getName())->save();
        }
    }


    protected function _validate()
    {
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f asyncindex.php -- [options]
                      
                      Run (reindex queue, index validation (if enabled))
  --fill-queue        Generate random queue
  --help              Help

USAGE;
    }
}

$shell = new Mirasvit_Shell_Asyncindex();
$shell->run();
