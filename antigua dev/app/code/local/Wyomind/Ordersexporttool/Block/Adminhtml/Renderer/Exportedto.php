<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Renderer_Exportedto extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {

        $profiles = Mage::getModel('ordersexporttool/profiles')->getCollection();
        $not_exported = Mage::helper('ordersexporttool')->__('Not exported');
        foreach ($profiles as $p) {
            if (in_array($p->getFileId(), explode(',', $row->getExportFlag()))) {
                $options[] = "<span id='orderexported-".$row->getId() . "-" . $p->getFileId()."'><span class='ckeckmark'>✔</span>&nbsp;" . $p->getFileName() . " <a href='#' onclick='javascript:ordersexporttool._delete(" . $row->getId() . "," . $p->getFileId() . ",\"" . $this->getUrl('ordersexporttool/adminhtml_profiles/change') . "\")'>(✘)</a></span>";
            }
        }

        $html.=implode('<br>', $options);
        if(count($options)<1) $html=Mage::helper('ordersexporttool')->__('Not exported');

        return $html;
    }

}
