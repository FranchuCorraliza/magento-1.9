<?php
/**
 * Magento Webshopapps Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.

 *
 * @category   Webshopapps
 * @package    Webshopapps_Tracker
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license   www.webshopapps.com/license/license.txt
 * @author    Karen Baker <sales@webshopapps.com>
 *
 */
class Webshopapps_Tracker_Model_Carrier_Tracker1
    extends Webshopapps_Tracker_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
	    protected $_code = 'tracker1';

		

   public function getAllowedMethods()
    {
    	return array('tracker1'=>$this->getConfigData('name'));
    	
    }
	

}
