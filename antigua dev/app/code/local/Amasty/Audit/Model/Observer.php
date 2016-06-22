<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Model_Observer
{
    protected $_logData = array();
    protected $_isOrigData = false;
    protected $_isCustomer = false;
    protected $_isFirstLogout = true;

    //listen controller_action_predispatch event
    public function saveSomeEvent($observer)
    {
        if (Mage::app()->getStore()->isAdmin()) {
            $username = Mage::getSingleton('admin/session')->getUser() ? Mage::getSingleton('admin/session')->getUser()->getUsername() : '';
            $user = Mage::getModel('admin/user')->loadByUsername($username);
            if ($user && Mage::helper('amaudit')->isUserInLog($user->getId())) {//settings log or not user
                $path = $observer->getEvent()->getControllerAction()->getRequest()->getOriginalPathInfo();
                Mage::register('amaudit_log_path', $path, true);
                $arrPath = ($path) ? explode("/", $path) : array();
                $exportType = NULL;
                if ((in_array('exportCsv', $arrPath)) || in_array('exportXml', $arrPath) || in_array('exportPost', $arrPath)) {
                    $this->_saveExport($arrPath, $observer);
                }
                Mage::register('amaudit_admin_path', $arrPath[1], true);
                $this->_saveCompilation($path, $username);
                $this->_saveCache($path, $username);
                $this->_saveIndex($path, $username);
            }
        }
    }

    public function afterBlockCreate($observer)
    {
        $block = $observer->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Customer_Edit_Tabs) {
            $this->_addBlock($block, 'adminhtml_tabs_customer', 'tags');
        }

        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View_Tabs) {
            $this->_addBlock($block, 'adminhtml_tabs_order', 'order_transactions');
        }

        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs) {
            $this->_addBlock($block, 'adminhtml_tabs_product', 'super');
        }
    }

    //listen model_delete_after event
    public function modelDeleteAfter($observer)
    {
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }

        $object = $observer->getObject();
        if ($object instanceof Mage_Catalog_Model_Product) {
            $this->_deleteProduct($object);
        } else {
            if (!Mage::registry('amaudit_log_duplicate_save')) {
                $this->_saveLog();
                if (!Mage::registry('amaudit_log_duplicate_save')) {
                    Mage::register('amaudit_log_duplicate_save', 1);
                }
            }
            $this->modelSaveAfter($observer, "Delete");
        }

    }

    //listen model_save_after event
    public function modelSaveAfter($observer, $delete = null)
    {
        $class = get_class($observer->getObject());

        if (!Mage::app()->getStore()->isAdmin() ||
            !Mage::registry('amaudit_log_id') ||
            $class == "Amasty_Audit_Model_Log" ||
            $class == "Amasty_Audit_Model_Log_Details" ||
            $class == "Amasty_Audit_Model_Active" ||
            $class == "Mage_Index_Model_Event"
        ) {
            return;
        }
        $elementId = $observer->getObject()->getEntityId();
        if (!$elementId) {
            $elementId = $observer->getObject()->getId();
        }
        $name = "";

        if (strpos($class, 'Sales_Model_Order') > 0) {
            $object = $observer->getObject();
            if ($observer->getObject()->getOrderId()) {
                $name = "Order ID " . $object->getOrderId();
            } elseif ($object->getParentId() == $object->getEntityId()) {
                $name = "Order ID " . $object->getParentId();
            }
        }

        if (!$name) {
            $name = $observer->getObject()->getName();
        }
        if (!$name) {
            $name = $observer->getObject()->getTitle();
        }
        //Catalog->search terms
        if (!$name && $observer->getObject() instanceof Mage_CatalogSearch_Model_Query) {
            $name = $observer->getObject()->getQueryText();
        }
        //Attribute Set
        if (!$name && $observer->getObject() instanceof Mage_Eav_Model_Entity_Attribute_Set) {
            $name = $observer->getObject()->getAttributeSetName();
        }
        //Catalog Ratings
        if (!$name && $observer->getObject() instanceof Mage_Rating_Model_Rating) {
            $name = $observer->getObject()->getRatingCode();
        }
        //Customer Group
        if (!$name && $observer->getObject() instanceof Mage_Customer_Model_Group) {
            $name = $observer->getObject()->getCustomerGroupCode();
        }
        if (!Mage::registry('amaudit_log_duplicate') || $name) {
            Mage::unregister('amaudit_log_duplicate');
            Mage::register('amaudit_log_duplicate', 1);
            try {
                $logModel = Mage::getModel('amaudit/log')->load(Mage::registry('amaudit_log_id'));
                $this->_logData = $logModel->getData();
                if ($logModel) {
                    if ($name && !$this->_isCustomer) $this->_logData['info'] = $name;
                    if ($elementId && !$this->_isCustomer) $this->_logData['element_id'] = $elementId;
                    if ($observer->getObject()->hasDataChanges()) $this->_logData['type'] = "Edit";
                    if ($observer->getObject()->isObjectNew() || ($observer->getObject()->hasDataChanges() && !$observer->getObject()->getOrigData())) {
                        $this->_logData['type'] = "New";
                    }
                    if ($observer->getObject()->isDeleted() || $delete) {
                        $this->_logData['type'] = "Delete";
                    }
                    if ($logModel->getCategoryName() == "System Configuration") {
                        $this->_logData['type'] = "Edit";
                    }
                    if ($logModel->getCategory() == "amaudit/adminhtml_log") {
                        $this->_logData['type'] = "Restore";
                    }
                    $logModel->setData($this->_logData);
                    $logModel->save();
                    if ($observer->getObject() instanceof Mage_Customer_Model_Customer) {
                        $this->_isCustomer = true;
                    }
                }
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::log($e->getMessage());
            }
        }

        //save details
        $entity = Mage::getModel($class)->load($elementId);
        $logModel = Mage::getModel('amaudit/log')->load(Mage::registry('amaudit_log_id'));
        $logModelType = $logModel->getType() ? $logModel->getType() : 'New';
        $isNew = ($logModel && $logModelType == "New") ? true : false;
        if ($class == 'Mage_Sales_Model_Order_Invoice_Item') {
            $isNew = true;
        }
        $this->_isOrigData = false;
        if ($observer->getObject()->getOrigData()) {
            $this->_isOrigData = true;
            Mage::unregister('amaudit_details_before');
            $this->_saveDetails($observer->getObject()->getOrigData(), $observer->getObject()->getData(), Mage::registry('amaudit_log_id'), $isNew, $class);
        }
        if ($entity && !$this->_isOrigData) {
            $newMass = $entity->getData();
            if (array_key_exists('config_id', $newMass) && array_key_exists('path', $newMass) && array_key_exists('value', $newMass)) {
                $newMass = array($newMass['path'] => $newMass['value']);
            }
            $mass = Mage::registry('amaudit_details_before');
            Mage::unregister('amaudit_details_before');
            $this->_saveDetails($mass, $newMass, Mage::registry('amaudit_log_id'), $isNew, $class);
        }
        //for order comment
        if (($class == 'Mage_Sales_Model_Order_Status_History') && (!$observer->getObject()->getOrigData())) {
            $this->_saveDetails(array('comment' => ''), array('comment' => $observer->getObject()->getComment()), Mage::registry('amaudit_log_id'), $isNew, $class);
        }
        Mage::unregister('amaudit_details_before');
    }

    /**
     * Handles mass change of status in Manage Products
     * @param $observer
     */
    public function modelProductsSaveBefore($observer)
    {
        $class = 'Mage_Catalog_Model_Product';
        $username = Mage::getSingleton('admin/session')->getUser() ? Mage::getSingleton('admin/session')->getUser()->getUsername() : '';
        $productIds = $observer->getProductIds();
        $observerStatus = $observer->getAttributesData();
        $observerStatus = $observerStatus['status'];
        foreach ($productIds as $productId) {
            $product = Mage::getModel($class)->load($productId);
            $productStatus = $product->getStatus();
            if ($productStatus == $observerStatus) {
                continue;
            }
            try {
                $logModel = Mage::getModel('amaudit/log')->load(Mage::registry('amaudit_log_id'));
                $this->_logData = $logModel->getData();
                $name = $product->getName();
                if ($logModel) {
                    if ($name) $this->_logData['info'] = $name;
                    $this->_logData['element_id'] = $productId;
                    $this->_logData['type'] = "Edit";
                    $this->_logData['category'] = "admin/catalog_product";
                    $this->_logData['category_name'] = "Product";
                    $this->_logData['parametr_name'] = "back";
                    $this->_logData['store_id'] = $observer->getStoreId();
                    $this->_logData['username'] = $username;
                    $this->_logData['date_time'] = Mage::getModel('core/date')->gmtDate();
                    $logModel->setData($this->_logData);
                    $logModel->save();
                }

                $detailsModel = Mage::getModel('amaudit/log_details');
                $detailsData['log_id'] = $logModel->getEntityId();
                $detailsData['name'] = 'status';
                $newStatus = $observer->getAttributesData();
                if (isset($newStatus['status'])) {
                    $newStatus = $newStatus['status'];
                }
                $detailsData['new_value'] = $newStatus;
                $detailsData['old_value'] = $product->getStatus();
                $detailsData['model'] = 'Mage_Catalog_Model_Product';
                $detailsModel->setData($detailsData);
                $detailsModel->save();

            } catch (Exception $e) {
                Mage::logException($e);
                Mage::log($e->getMessage());
            }
        }

        return;
    }


    //listen model_save_before event
    public function modelSaveDeleteBefore($observer)
    {
        $class = get_class($observer->getObject());

        if (!Mage::app()->getStore()->isAdmin() ||
            $class == "Amasty_Audit_Model_Log" ||
            $class == "Mage_Core_Model_Config_Element" ||
            $class == "Amasty_Audit_Model_Log_Details" ||
            $class == "Amasty_Audit_Model_Active" ||
            $class == "Mage_Index_Model_Event"
        ) {
            return;
        }

        if (!Mage::registry('amaudit_log_duplicate_save')) {
            $this->_saveLog();
            Mage::register('amaudit_log_duplicate_save', 1);
        }

        $mass = Mage::registry('amaudit_details_before') ? Mage::registry('amaudit_details_before') : array();
        $id = $observer->getObject()->getId();
        $entity = Mage::getModel($class)->load($id);
        if ($entity) {
            $massNew = $entity->getData();
            foreach ($massNew as $mas) {
                if (!(gettype($mas) == "string" || gettype($mas) == "boolean" || is_array($mas))) {
                    unset($mas);
                }
            }

            if (array_key_exists('config_id', $massNew) && array_key_exists('path', $massNew) && array_key_exists('value', $massNew)) {
                $mass[$massNew['path']] = $massNew['value'];
            } else {
                $mass += $massNew;
            }

            Mage::register('amaudit_details_before', $mass, true);
        }

    }

    //run with cron
    public function deleteLogs($observer)
    {
        $collection = Mage::getModel('amaudit/log')->getCollection();
        $days = Mage::getStoreConfig('amaudit/general/delete_logs_afret_days');
        try {
            foreach ($collection as $item) {
                $date = strtotime($item->getDateTime());
                if (time() - $date > $days * 24 * 60 * 60) {
                    $entity = Mage::getModel('amaudit/log')->load($item->getId());
                    $entity->delete();
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::log($e->getMessage());
        }
    }

    public function implode_r($glue, $arr)
    {
        $ret_str = "";
        foreach ($arr as $a) {
            $ret_str .= (is_array($a)) ? $this->implode_r($glue, $a) : "," . $a;
        }

        return $ret_str;
    }

    protected function _addBlock($block, $createdBlock, $lastElement)
    {
        if (method_exists($block, 'addTabAfter')) {
            $block->addTabAfter('tabid', array(
                'label' => Mage::helper('amaudit')->__('History of Changes'),
                'content' => $block->getLayout()
                    ->createBlock('amaudit/' . $createdBlock)->toHtml(),
            ), $lastElement);
        } else {
            $block->addTab('tabid', array(
                'label' => Mage::helper('amaudit')->__('History of Changes'),
                'content' => $block->getLayout()
                    ->createBlock('amaudit/' . $createdBlock)->toHtml(),
            ));
        }

    }

    protected function _saveExport($arrPath, $observer)
    {
        $logModel = Mage::getModel('amaudit/log');
        $logData['date_time'] = Mage::getModel('core/date')->gmtDate();
        $username = Mage::getSingleton('admin/session')->getUser() ? Mage::getSingleton('admin/session')->getUser()->getUsername() : '';
        $logData['username'] = $username;
        if (in_array('exportPost', $arrPath)) {
            $logData['type'] = $arrPath[2];
        }
        $logData['type'] = $arrPath[3];
        $category = $arrPath[2];
        $logData['category'] = $category;
        $logData['category_name'] = Mage::helper('amaudit')->getCatNameFromArray($category);
        $logData['parametr_name'] = 'back';
        $logData['element_id'] = 0;
        $logData['info'] = 'Data was exported';
        $logData['store_id'] = $observer->getStoreId();
        $logModel->setData($logData);
        $logModel->save();
    }

    protected function _deleteProduct($object)
    {
        $logModel = Mage::getModel('amaudit/log')->load(Mage::registry('amaudit_log_id'));
        $username = Mage::getSingleton('admin/session')->getUser() ? Mage::getSingleton('admin/session')->getUser()->getUsername() : '';
        $this->_logData = $logModel->getData();
        $name = $object->getName();
        if ($logModel) {
            $logData['info'] = $name;
            $logData['element_id'] = $object->getEntityId();
            $logData['type'] = 'Delete';
            $ogData['category'] = "admin/catalog_product";
            $logData['category_name'] = "Product";
            $logData['parametr_name'] = "delete";
            $logData['store_id'] = 0;
            $logData['username'] = $username;
            $logData['date_time'] = Mage::getModel('core/date')->gmtDate();
            $logModel->setData($logData);
            $logModel->save();
        }
    }

    private function _saveLog()
    {
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        //save log start
        $username = Mage::getSingleton('admin/session')->getUser() ? Mage::getSingleton('admin/session')->getUser()->getUsername() : '';
        $path = Mage::registry('amaudit_log_path');
        $arrPath = ($path) ? explode("/", $path) : array();
        if (!array_key_exists(3, $arrPath)) {
            return false;
        }
        $logModel = Mage::getModel('amaudit/log');
        $this->_logData = array();
        $this->_logData['date_time'] = Mage::getModel('core/date')->gmtDate();
        $this->_logData['username'] = $username;
        if ("delete" == $arrPath[3]) {
            $this->_logData['type'] = "Delete";
        } else {
            $this->_logData['type'] = $arrPath[3];
        }
        $this->_logData['category'] = $arrPath[1] . '/' . $arrPath[2];
        $this->_logData['category_name'] = Mage::helper('amaudit')->getCatNameFromArray($this->_logData['category']);;

        if ($arrPath[4] == 'store') $arrPath[4] = $arrPath[6];
        $paramName = $arrPath[4] == "key" ? "underfined" : $arrPath[4];
        if ($paramName == 'section') {
            $paramName .= '/' . $arrPath[5];
        }
        $this->_logData['parametr_name'] = $paramName;

        $storeId = 0;
        if ($keyStore = array_search("store", $arrPath)) {
            $storeId = $arrPath[$keyStore + 1];
            if (!is_numeric($storeId)) {
                $storeId = Mage::getModel('core/store')->load($storeId, 'code')->getStoreId();
            }
        }
        $this->_logData['store_id'] = $storeId;

        if ($this->_logData['type'] != 'logout') {
            $logModel->setData($this->_logData);
            $logModel->save();
            Mage::register('amaudit_log_id', $logModel->getEntityId(), true);
            Mage::unregister('amaudit_details_before');
        } elseif ($this->_isFirstLogout) {
            $this->_isFirstLogout = false;
            $detailsModel = Mage::getModel('amaudit/data');
            $detailsModel->logout($this->_logData);
        }
        //save log end
    }

    private function _saveDetails($massOld, $massNew, $logId, $isNew = false, $model = null)
    {
        $notSaveModels = array(
            'Mage_SalesRule_Model_Coupon',
            'Mage_Eav_Model_Entity_Store'
        );
        if ($isNew) {
            $massOld = $massNew;
        }
        if (!in_array($model, $notSaveModels)) {
            try {
                $notRestore = array('entity_id', 'entity_type_id');
                if (is_array($massOld)) {
                    //for change the user's role
                    if (($model == 'Mage_Admin_Model_User') && isset($massNew['roles']['0'])) {
                        $newRoleId = $massNew['roles']['0'];
                        $oldRoleIdPrepare = explode('=', $massNew['user_roles']);
                        $oldRoleId = $oldRoleIdPrepare[0];
                        $rolesModel = Mage::getModel('admin/role');
                        $oldRole = $rolesModel->load($oldRoleId)->getRoleName();
                        $newRole = $rolesModel->load($newRoleId)->getRoleName();
                        $massNew['roles'] = $newRole;
                        $massOld['roles'] = $oldRole;
                    }
                    foreach ($massOld as $key => $value) {
                        if (in_array($key, $notRestore)) {
                            continue;
                        }
                        if ($key == 'image') {
                            if (is_array($massNew[$key])) {
                                $massNew[$key] = array_shift($massNew[$key]);
                            }
                        }
                        if (array_key_exists($key, $massNew) && $key != 'updated_at' && $key != 'created_at' && $key != 'category_name') {
                            if (($value != $massNew[$key] && !(!$value && !$massNew[$key])) || $isNew) {
                                $datailsModel = Mage::getModel('amaudit/log_details');
                                if ($datailsModel->isInCollection($logId, $key, $model)) {
                                    continue;
                                }
                                if (!$isNew) $datailsModel->setData('old_value', $value);
                                $datailsModel->setData('new_value', $massNew[$key]);
                                $datailsModel->setData('name', $key);
                                $datailsModel->setData('log_id', $logId);
                                $datailsModel->setData('model', $model);
                                if (!is_array($key) && ($key !== "media_gallery")) $datailsModel->save();
                            } else if (is_array($value) && is_array($massNew[$key])) {
                                if ($key == 'media_gallery') {
                                    foreach ($value['images'] as $image) {
                                        $value[] = $image['file'];
                                    }
                                    foreach ($massNew[$key]['images'] as $newImage) {
                                        $massNew[$key][] = $newImage['file'];
                                    }
                                    unset($value['images']);
                                    unset($value['values']);
                                    unset($massNew[$key]['images']);
                                    unset($massNew[$key]['values']);
                                }
                                $old = $this->implode_r(',', $value);
                                $new = $this->implode_r(',', $massNew[$key]);
                                if ($old != $new || $isNew) {
                                    $datailsModel = Mage::getModel('amaudit/log_details');
                                    if (!$isNew) $datailsModel->setData('old_value', $old);
                                    $datailsModel->setData('new_value', $new);
                                    $datailsModel->setData('name', $key);
                                    $datailsModel->setData('log_id', $logId);
                                    $datailsModel->setData('model', $model);
                                    $datailsModel->save();
                                }
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                Mage::log($e->getMessage());
                Mage::logException($e);
            }
        }

    }

    private function _saveCompilation($path, $username)
    {
        if (strpos($path, "compiler/process") !== false) {
            $arrPath = explode("/", $path);
            if ($keyStore = array_search("process", $arrPath)) {
                $type = $arrPath[$keyStore + 1];
                if ($type != "index") {
                    try {
                        $logModel = Mage::getModel('amaudit/log');
                        $this->_logData = array();
                        $this->_logData['date_time'] = Mage::getModel('core/date')->gmtDate();
                        $this->_logData['username'] = $username;
                        $this->_logData['type'] = ucfirst($type);
                        $this->_logData['category'] = "compiler/process";
                        $this->_logData['category_name'] = "Compilation";
                        $this->_logData['parametr_name'] = 'index';
                        $this->_logData['info'] = "Compilation";
                        $storeId = 0;
                        if ($keyStore = array_search("store", $arrPath)) {
                            $storeId = $arrPath[$keyStore + 1];
                        }
                        $this->_logData['store_id'] = $storeId;
                        $logModel->setData($this->_logData);
                        $logModel->save();
                    } catch (Exception $e) {
                        Mage::logException($e);
                        Mage::log($e->getMessage());
                    }
                }
            }
        }
    }

    private function _saveCache($path, $username)
    {
        $params = Mage::app()->getRequest()->getParams();
        $adminPath = Mage::registry('amaudit_admin_path') ? Mage::registry('amaudit_admin_path') : 'admin';
        if (strpos($path, $adminPath . "/cache") !== false) {
            $arrPath = explode("/", $path);
            if ($keyStore = array_search("cache", $arrPath)) {
                $type = $arrPath[$keyStore + 1];
                if ($type != "index") {
                    try {
                        $logModel = Mage::getModel('amaudit/log');
                        $this->_logData = array();
                        $this->_logData['date_time'] = Mage::getModel('core/date')->gmtDate();
                        $this->_logData['username'] = $username;
                        $this->_logData['type'] = ucfirst($type);
                        $this->_logData['category'] = "admin/cache";
                        $this->_logData['category_name'] = "Cache";
                        $this->_logData['parametr_name'] = 'index';
                        $this->_logData['info'] = "Cache";
                        $storeId = 0;
                        if ($keyStore = array_search("store", $arrPath)) {
                            $storeId = $arrPath[$keyStore + 1];
                        }
                        $this->_logData['store_id'] = $storeId;

                        $logModel->setData($this->_logData);
                        $logModel->save();
                        if (array_key_exists('types', $params)) {
                            $params = Mage::helper('amaudit')->getCacheParams($params['types']);
                            $this->_saveDetails($params, array(), $logModel->getEntityId(), true);
                        }
                    } catch (Exception $e) {
                        Mage::logException($e);
                        Mage::log($e->getMessage());
                    }
                }
            }
        }
    }

    private function _saveIndex($path, $username)
    {
        $params = Mage::app()->getRequest()->getParams();
        $adminPath = Mage::registry('amaudit_admin_path') ? Mage::registry('amaudit_admin_path') : 'admin';
        if (strpos($path, $adminPath . "/process") !== false) {
            $arrPath = explode("/", $path);
            if ($keyStore = array_search("process", $arrPath)) {   //settings log or not user
                $type = $arrPath[$keyStore + 1];
                if ($type != "list") {
                    try {
                        $logModel = Mage::getModel('amaudit/log');
                        $this->_logData = array();
                        $this->_logData['date_time'] = Mage::getModel('core/date')->gmtDate();
                        $this->_logData['username'] = $username;
                        $this->_logData['type'] = ucfirst($type);
                        $this->_logData['category'] = "admin/process";
                        $this->_logData['category_name'] = "Index Management";
                        $this->_logData['parametr_name'] = 'list';
                        $this->_logData['info'] = "Index Management";
                        $storeId = 0;
                        if ($keyStore = array_search("store", $arrPath)) {
                            $storeId = $arrPath[$keyStore + 1];
                        }
                        $this->_logData['store_id'] = $storeId;

                        $logModel->setData($this->_logData);
                        $logModel->save();
                        if (array_key_exists('process', $params)) {
                            $params = Mage::helper('amaudit')->getIndexParams($params['process']);
                            $this->_saveDetails($params, array(), $logModel->getEntityId(), true);
                        }
                    } catch (Exception $e) {
                        Mage::logException($e);
                        Mage::log($e->getMessage());
                    }
                }
            }
        }
    }
}