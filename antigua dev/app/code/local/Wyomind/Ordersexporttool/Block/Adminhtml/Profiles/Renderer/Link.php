<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Renderer_Link extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $types = array('none', 'xml', 'txt', 'csv','tsv','din');
        $ext = $types[$row->getFile_type()];

        $date = Mage::getSingleton('core/date')->date($row->getFileDateFormat(), strtotime($row->getFileUpdatedAt()));
        
        $fileName = preg_replace('/^\//', '', $row->getFile_path() . str_replace('{f}',$row->getFile_name(),$date) . '.' . $ext);
       
        $url = $this->htmlEscape(Mage::app()->getStore($row->getStoreId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $fileName);


        if (file_exists(BP . DS . $fileName)) {
            return sprintf('<a href="%1$s?r=' . time() . '" target="_blank">%1$s</a>', $url);
        }
        return $url;
    }

}
