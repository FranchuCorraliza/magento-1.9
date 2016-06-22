<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Renderer_Startingwith extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        if ($row->getFile_last_exported_id())
            return $row->getFile_last_exported_id();
        else
            return "no set";
    }

}
