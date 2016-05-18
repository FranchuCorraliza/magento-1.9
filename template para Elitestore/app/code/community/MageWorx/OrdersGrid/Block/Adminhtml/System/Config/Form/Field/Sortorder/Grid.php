<?php

/**
 * MageWorx
 * Admin Order Grid  extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersGrid
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_OrdersGrid_Block_Adminhtml_System_Config_Form_Field_Sortorder_Grid extends
    MageWorx_OrdersGrid_Block_Adminhtml_System_Config_Form_Field_Sortorder_Abstract
{

    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
    }

    /**
     * Get Cascading Style Sheets for field.
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function getCss(Varien_Data_Form_Element_Abstract $element)
    {
        $css = <<<STYLESHEET
        <style type="text/css">
            li { margin: 2px 0; cursor: pointer; padding: 3px; border-radius: 3px; }
			li.selected { background-color: rgba(155,155,155,0.3); }
			li.child { margin-left: 20px; }
			span.mw-position { padding-left: 5px; font-size: 0.7em; vertical-align: top;}
		</style>
STYLESHEET;

        return $css;
    }

    /**
     * Get Java Script for field.
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function getJs(Varien_Data_Form_Element_Abstract $element)
    {
        $id = $element->getHtmlId();
        $ajaxUrl = $this->getAjaxUrl();
        $script = <<<SCRIPT
jQuery("#$id").multisortable({
    items: "li",
    selectedClass: "selected",
    click: function(e){
        },
    stop: function(e){
            var data = {};
            jQuery(e.target).find('.mw-option').each(function(){
                var index = jQuery(this).index();
                var input = jQuery(this).find('input.mw-data-position');
                jQuery(this).find('.mw-position').text(index);
                input.val(index);
                data[input.attr("name")] = input.val();
            });
            setColumnsData(data);
        }

});

function setColumnsData(data) {
    new Ajax.Request("$ajaxUrl", {
            parameters:  data,
            onComplete: function () {
            },
            onSuccess: function(transport) {
            }
        });
}
SCRIPT;

        return $script;
    }

    /**
     * Get url for ajax request
     *
     * @return string
     */
    protected function getAjaxUrl()
    {
        return Mage::getUrl('adminhtml/mageworx_ordersgrid/savesortorder');
    }

    /**
     * Get columns sort order
     *
     * @return array
     */
    protected function getPositions()
    {
        $helper = $this->getMwHelper();
        $positions = $helper->getGridColumnsSortOrder();

        return $positions;
    }
}