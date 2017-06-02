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
 * @package   Full Page Cache
 * @version   1.0.32
 * @build     662
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


/**
 * Simple_Forum extension compatibility
 */

class Mirasvit_Fpc_Helper_Simpleforum extends Mage_Core_Helper_Abstract
{
    private function getAction() {
        $request = Mage::app()->getRequest();
        $action = $request->getModuleName() . '/' . $request->getControllerName() . '_' . $request->getActionName();

        return $action;
    }

    public function getSimpleForumCacheId()
    {
        $topicCacheId = '';
        if ($this->getAction() == 'forum/topic_view'
            && ($postTopicId = Mage::app()->getRequest()->getParam('id')) ) {
                $topicCollection = Mage::getModel('forum/topic')->getCollection()->addFieldToFilter('topic_id', $postTopicId);
                if ($topicCollection->getSize()) {
                    $topic = $topicCollection->getFirstItem();
                    $topicUrlText = $topic->getUrlText();
                    $currentUrl = Mage::helper('core/url')->getCurrentUrl();
                    if (strpos($currentUrl, $topicUrlText) !== false && $topic->getParentId() != 0) {
                        $postCollection = Mage::getModel('forum/post')->getCollection()
                            ->addFieldToFilter('parent_id', $topic->getTopicId())
                            ->addFieldToFilter('status', 1);

                        foreach ($postCollection as $postItem) {
                            $topicCacheId .= $postItem->getPostId() . '_' . $postItem->getParentId() . '_' . $postItem->getLikes();
                        }
                    } elseif (strpos($currentUrl, $topicUrlText) !== false) {
                        $topicDataCollection = Mage::getModel('forum/topic')->getCollection()
                            ->addFieldToFilter('parent_id', $topic->getTopicId())
                            ->addFieldToFilter('status', 1);

                        foreach ($topicDataCollection as $topicItem) {
                            $topicCacheId .= $topicItem->getTopicId() . '_' . $topicItem->getTotalPosts();
                        }
                    }
                }
        }

        return $topicCacheId;
    }

    public function prepareContent($content) {
        if ($this->getAction() == '') {
            $content = preg_replace('/<ul class="messages">(.*?)\\<\\/ul\\>\\<\\/li\\>\\<\\/ul\\>/ims', '', $content, 1);
        }

        return $content;
    }

}