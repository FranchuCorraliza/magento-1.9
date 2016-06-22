<?php

class Magestore_Auction_Helper_Email extends Mage_Core_Helper_Abstract {

    const XML_PATH_SALES_EMAIL_IDENTITY = "trans_email/ident_sales";
    const XML_PATH_NOTICE_FAILDER = "auction/emails/notice_failder_email_template";

    public function sendAuctionCompleteEmail($auction) {
        $winnerBids = $auction->getWinnerBids();
        if (count($winnerBids))
            foreach ($winnerBids as $winnerBid) {
                $winnerBid->emailToWinner();
            }
        $auction->emailNoticeCompleted();
        $auction->emailNoticeCompletedToWatcher();
        $this->noticeOutBid($auction);
    }

    public function noticeOutBid($auction) {
        if (!Mage::registry('notice_outbid')) {
            Mage::register('notice_outbid', '1');
            $storeID = 0;
            $failedbiddes['email'] = array();
            $failedbiddes['name'] = array();
            $winnerCustomerEmails = array();

            $winnerBids = $auction->getWinnerBids();
            if (count($winnerBids))
                foreach ($winnerBids as $winnerBid) {
                    $winnerCustomerEmails[] = $winnerBid->getCustomerEmail();
                }

            $bids = $auction->getListBid()
                    ->addFieldToFilter('status', array('neq' => 2))
            ;

            if (count($bids))
                foreach ($bids as $bid) {
                    $storeID = $bid->getStoreId();
                    if (!in_array($bid->getCustomerEmail(), $winnerCustomerEmails) && !in_array($bid->getCustomerEmail(), $failedbiddes['email'])) {
                        $failedbiddes['email'][] = $bid->getCustomerEmail();
                        $failedbiddes['name'][] = $bid->getCustomerName();
                    }
                }

            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);

            $template = Mage::getStoreConfig(self::XML_PATH_NOTICE_FAILDER, $storeID);

            $sendTo = array();
            if (count($failedbiddes['email']))
                foreach ($failedbiddes['email'] as $key => $recipient) {
                    $sendTo[] = array(
                                'name' => $failedbiddes['name'][$key],
                                'email' => $failedbiddes['email'][$key],
                    );
                }
            $mailTemplate = Mage::getModel('core/email_template');

            if (count($sendTo)) {
                foreach ($sendTo as $recipient) {
                    $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeID))
                            ->sendTransactional(
                                    $template, Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $storeID), $recipient['email'], $recipient['name'], array(
                                'auction' => $auction->setCustomerName($recipient['name'])
                                    )
                    );
                }
            }
            $translate->setTranslateInline(true);

            return $this;
        }
    }

}
