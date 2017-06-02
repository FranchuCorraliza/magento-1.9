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


abstract class Mirasvit_AsyncIndex_Model_Validator_Abstract
{
    public abstract function validate();
    /**
     * ÐÐ¾Ð·Ð²ÑÐ°ÑÐ°ÐµÑ Ð¼Ð¾Ð´ÐµÐ»Ñ Ð°ÑÑÐ¸Ð±ÑÑÐ° Ð¿Ð¾ ÑÐ³Ð¾ ÐºÐ¾Ð´Ñ
     * 
     * @param  string $attributeCode ÐºÐ¾Ð´ Ð°ÑÑÐ¸Ð±ÑÑÐ°
     *
     * @return Mage_Model_Resource_Eav_Attribute Ð¼Ð¾Ð´ÐµÐ»Ñ Ð°ÑÑÐ¸Ð±ÑÑÐ°
     */
    protected function _getAttribute($attributeCode)
    {
        $attribute = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode(Mage::getResourceModel('catalog/config')->getEntityTypeId(), $attributeCode);
        if (!$attribute->getId()) {
            Mage::throwException(Mage::helper('catalog')->__('Invalid attribute %s', $attributeCode));
        }
        $entity = Mage::getSingleton('eav/config')
            ->getEntityType(Mage_Catalog_Model_Product::ENTITY)
            ->getEntity();
        $attribute->setEntity($entity);

        return $attribute;
    }

    protected function _getCheckSql($expression, $true, $false)
    {
        if ($expression instanceof Zend_Db_Expr || $expression instanceof Zend_Db_Select) {
            $expression = sprintf("IF((%s), %s, %s)", $expression, $true, $false);
        } else {
            $expression = sprintf("IF(%s, %s, %s)", $expression, $true, $false);
        }

        return new Zend_Db_Expr($expression);
    }
}