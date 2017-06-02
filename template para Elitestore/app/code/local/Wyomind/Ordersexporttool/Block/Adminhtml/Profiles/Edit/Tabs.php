<?php

class Wyomind_Ordersexporttool_Block_Adminhtml_Profiles_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('ordersexporttool_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('Orders Export Tool');
    }

    protected function _beforeToHtml() {
        $this->addTab('form_configuration', array(
            'label' => $this->__('Configuration'),
            'title' => $this->__('Configuration'),
            'content' => $this->getLayout()
                    ->createBlock('ordersexporttool/adminhtml_profiles_edit_tab_configuration')
                    ->toHtml()
        ));
        $this->addTab('form_template', array(
            'label' => $this->__('Template'),
            'title' => $this->__('File Template'),
            'content' => $this->getLayout()
                    ->createBlock('ordersexporttool/adminhtml_profiles_edit_tab_template')
                    ->toHtml()
        ));

        $this->addTab('form_filter', array(
            'label' => $this->__('Filters'),
            'title' => $this->__('Filters'),
            'content' => $this->getLayout()
                    ->createBlock('ordersexporttool/adminhtml_profiles_edit_tab_filters')
                    ->toHtml()
        ));

        $this->addTab('ftp_upload', array(
            'label' => $this->__('Ftp settings'),
            'title' => $this->__('Ftp settings'),
            'content' => $this->getLayout()
                    ->createBlock('ordersexporttool/adminhtml_profiles_edit_tab_ftp')
                    ->toHtml()
        ));
        $this->addTab('form_cron', array(
            'label' => $this->__('Scheduled tasks'),
            'title' => $this->__('Scheduled tasks'),
            'content' => $this->getLayout()
                    ->createBlock('ordersexporttool/adminhtml_profiles_edit_tab_cron')
                    ->toHtml()
        ));
        /* $this->addTab('form_advanced', array(
          'label' => $this->__('Advanced options'),
          'title' => $this->__('Advanced options'),
          'content' => $this->getLayout()
          ->createBlock('ordersexporttool/adminhtml_profiles_edit_tab_advanced')
          ->toHtml()
          )); */

        return parent::_beforeToHtml();
    }

}
