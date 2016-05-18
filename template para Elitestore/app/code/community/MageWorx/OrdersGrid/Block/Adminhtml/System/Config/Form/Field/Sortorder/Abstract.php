<?php

/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_OrdersGrid_Block_Adminhtml_System_Config_Form_Field_Sortorder_Abstract extends
    Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * Get columns sort order
     *
     * @return array
     */
    abstract protected function getPositions();

    /**
     * Get Java Script for field.
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    abstract protected function getJs(Varien_Data_Form_Element_Abstract $element);

    /**
     * Get Cascading Style Sheets for field.
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    abstract protected function getCss(Varien_Data_Form_Element_Abstract $element);

    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setType('select');
        $this->setExtType('multiple');
        $this->setSize(10);
    }

    /**
     * Render element
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $id = $element->getHtmlId();

        $html = '<td class="label"><label for="' . $id . '">' . $element->getLabel() . '</label></td>';

        //$isDefault = !$this->getRequest()->getParam('website') && !$this->getRequest()->getParam('store');
        $isMultiple = $element->getExtType() === 'multiple';

        // replace [value] with [inherit]
        $namePrefix = preg_replace('#\[value\](\[\])?$#', '', $element->getName());

        $options = $element->getValues();

        if (isset($options[0]['value']) && is_array($options[0]['value'])) {
            $optionsWithoutGroups = array();
            foreach ($options as $option) {
                if (is_array($option['value'])) {
                    $optionsWithoutGroups = array_merge($optionsWithoutGroups, $option['value']);
                } else {
                    $optionsWithoutGroups[] = $option;
                }
            }
            $options = $optionsWithoutGroups;
        }

        $addInheritCheckbox = false;
        if ($element->getCanUseWebsiteValue()) {
            $addInheritCheckbox = true;
            $checkboxLabel = $this->__('Use Website');
        } elseif ($element->getCanUseDefaultValue()) {
            $addInheritCheckbox = true;
            $checkboxLabel = $this->__('Use Default');
        }

        if ($addInheritCheckbox) {
            $inherit = $element->getInherit() == 1 ? 'checked="checked"' : '';
            if ($inherit) {
                $element->setDisabled(true);
            }
        }

        if ($element->getTooltip()) {
            $html .= '<td class="value with-tooltip">';
            $html .= $this->_getElementHtml($element);
            $html .= '<div class="field-tooltip"><div>' . $element->getTooltip() . '</div></div>';
        } else {
            $html .= '<td class="value">';
            $html .= $this->_getElementHtml($element);
        };
        if ($element->getComment()) {
            $html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }
        $html .= '</td>';

        if ($addInheritCheckbox) {

            $defText = $element->getDefaultValue();
            if ($options) {
                $defTextArr = array();
                foreach ($options as $k => $v) {
                    if ($isMultiple) {
                        if (is_array($v['value']) && in_array($k, $v['value'])) {
                            $defTextArr[] = $v['label'];
                        }
                    } elseif (isset($v['value'])) {
                        if ($v['value'] == $defText) {
                            $defTextArr[] = $v['label'];
                            break;
                        }
                    } elseif (!is_array($v)) {
                        if ($k == $defText) {
                            $defTextArr[] = $v;
                            break;
                        }
                    }
                }
                $defText = join(', ', $defTextArr);
            }

            // default value
            $html .= '<td class="use-default">';
            $html .= '<input id="' . $id . '_inherit" name="'
                . $namePrefix . '[inherit]" type="checkbox" value="1" class="checkbox config-inherit" '
                . $inherit . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" /> ';
            $html .= '<label for="' . $id . '_inherit" class="inherit" title="'
                . htmlspecialchars($defText) . '">' . $checkboxLabel . '</label>';
            $html .= '</td>';
        }

        $html .= '<td class="scope-label">';
        if ($element->getScope()) {
            $html .= $element->getScopeLabel();
        }
        $html .= '</td>';

        $html .= '<td class="">';
        if ($element->getHint()) {
            $html .= '<div class="hint" >';
            $html .= '<div style="display: none;">' . $element->getHint() . '</div>';
            $html .= '</div>';
        }
        $html .= '</td>';
        $html .= $this->helper('adminhtml/js')->includeScript('mageworx/jquery.js');
        $html .= $this->helper('adminhtml/js')->includeScript('mageworx/jquery-ui.js');
        $html .= $this->helper('adminhtml/js')->includeScript('mageworx/jquery.multisortable.js');
        $html .= $this->helper('adminhtml/js')->includeScript('mageworx/jquery.noconflict.js');

        $script = $this->getJs($element);
        $html .= $this->helper('adminhtml/js')->getScript($script);

        $css = $this->getCss($element);
        $html .= $css;

        return $this->_decorateRowHtml($element, $html);
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $element->addClass('select multiselect');
        $html = '';
        if ($element->getCanBeEmpty()) {
            $html .= '<input type="hidden" name="' . $element->getName() . '" value="" />';
        }
        $html .= '<div class="mw-container" style="height: 200px;overflow: auto;width: 280px;"><ul id="' . $element->getHtmlId() . '" name="' . $element->getName() . '" ' .
            $this->serialize($element->getHtmlAttributes()) . ' >' . "\n";

        $value = $element->getValue();
        if (!is_array($value)) {
            $value = explode(',', $value);
        }

        if ($values = $element->getValues()) {
            if (isset($values[0]['value']) && is_array($values[0]['value'])) {
                $valuesWithoutGroups = array();
                foreach ($values as $val) {
                    if (is_array($val['value'])) {
                        $valuesWithoutGroups = array_merge($valuesWithoutGroups, $val['value']);
                    } else {
                        $valuesWithoutGroups[] = $val;
                    }
                }
                $values = $valuesWithoutGroups;
            }

            // Get positions of columns in grid
            $positions = $this->getPositions();

            // Sort values by current position
            $sortValues = array();
            $notExistingValues = array();
            foreach ($values as $unsortedValue) {
                if (isset($unsortedValue['value']) && isset($positions[$unsortedValue['value']])) {
                    $sortValues[$positions[$unsortedValue['value']]] = $unsortedValue;
                } else {
                    $notExistingValues[] = $unsortedValue;
                }
            }

            ksort($sortValues);
            $values = array_merge($sortValues, $notExistingValues);

            foreach ($values as $position => $option) {
                if (is_array($option['value'])) {
                    $html .= '<li label="' . $option['label'] . '">' . "\n";
                    foreach ($option['value'] as $groupItem) {
                        $html .= $this->_optionToHtml($groupItem, $value, $position);
                    }
                    $html .= '</li>' . "\n";
                } else {
                    $html .= $this->_optionToHtml($option, $value, $position);
                }
            }
        }

        $html .= '</ul></div>' . "\n";
        $html .= $element->getAfterElementHtml();

        return $html;
    }

    public function getHtmlAttributes()
    {
        return array('title', 'class', 'style', 'onclick', 'onchange', 'disabled', 'size', 'tabindex');
    }

    /**
     * @return MageWorx_OrdersGrid_Helper_Data
     */
    public function getMwHelper()
    {
        return Mage::helper('mageworx_ordersgrid');
    }

    protected function _optionToHtml($option, $selected, $position)
    {
        $html = '<li class="mw-option"><input class="mw-data-position" type="hidden" name="' . $this->_escape($option['value']) . '" value="' . $position . '"';
        $html .= isset($option['title']) ? 'title="' . $this->_escape($option['title']) . '"' : '';
        $html .= isset($option['style']) ? 'style="' . $option['style'] . '"' : '';
        if (in_array((string)$option['value'], $selected)) {
            $html .= ' selected="selected"';
        }
        $html .= '/>' . $this->_escape($option['label']) . '<span class="mw-position">' . $position . "</span></li>\n";
        return $html;
    }

    protected function _escape($string)
    {
        return htmlspecialchars($string, ENT_COMPAT);
    }
}