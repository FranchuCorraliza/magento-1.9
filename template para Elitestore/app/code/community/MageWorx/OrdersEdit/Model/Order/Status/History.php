<?php

/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_OrdersEdit_Model_Order_Status_History extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->_init('mageworx_ordersedit/order_status_history');
    }

    /**
     * Update mageworx extended history on regular history object save
     *
     * @param $history
     * @return $this
     * @throws Exception
     */
    public function updateHistory($history)
    {
        if ($history->getOrigData()) {
            return $this;
        }

        $user = Mage::getSingleton('admin/session')->getUser();
        if (!is_object($user)) {
            return $this;
        }

        $adminUserId = $user->getUserId();
        if (!$adminUserId) {
            return $this;
        }

        $creator = $user;
        $this->setData('history_id', $history->getEntityId());
        $this->setData('creator_admin_user_id', $adminUserId);
        $this->setData('creator_firstname', $creator->getFirstname());
        $this->setData('creator_lastname', $creator->getLastname());
        $this->setData('creator_username', $creator->getUsername());
        $this->save();

        return $this;
    }
}