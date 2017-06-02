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



class Mirasvit_Fpc_Model_Requestmf_Processormf
{
    /**
     * @var string|null
     */
    protected $_sessionName = null;

    /**
     * @var Mirasvit_Fpc_Model_Configmf|null
     */
    protected $_config = null;

    /**
     * @var string|null
     */
    protected static $_cacheId = null;

    /**
     * @var Mirasvit_Fpc_Helper_Fpcmf_Debugmf
     */
    protected $_debug;

    public function __construct()
    {
        if (!isset($_SERVER['FPC_TIME'])) {
            $_SERVER['FPC_TIME'] = microtime(true);
        }

        $this->_config = new Mirasvit_Fpc_Model_Configmf();
        $this->_debug = new Mirasvit_Fpc_Helper_Fpcmf_Debugmf();

        if (isset($_SESSION)) {
            $this->_sessionName = session_name();
        } else {
            $this->_sessionName = 'frontend';
        }

        return $this;
    }

    /**
     * Entry point.
     * @return bool
     */
    public function extractContent()
    {
        if (!$this->_canServeRequest()) {
            return false;
        }

        $this->_startSession();

        $customDependence = new Mirasvit_Fpc_Helper_CustomDependence();
        if(!$customDependence->isMfCache(true)) {
            return false;
        }

        $cacheId = $this->getRequestCacheId();

        $storage = Mirasvit_Fpc_Model_Storagemf::getInstance();
        $storage->setCacheId($cacheId);

        if ($storage->load()) {
            $content = $storage->getContent();

            $blocks = $storage->getBlocks();

            $blocksInApp = array();
            $blocksNotApplied = array();
            $blocksWithoutApp = array();

            $headerKey = false;
            foreach ($blocks as $key => $block) {
                if ($block->getBlockType() == 'page/html_header') {
                    $headerKey = $key;
                }
            }

            if ($headerKey) { // header have to be first because inside can be one more block
                array_unshift($blocks, $blocks[$headerKey]);
                unset($blocks[$headerKey]);
            }

            foreach ($blocks as $block) {
                // if cache for current block not exists, we render whole page (and save updated block to cache)
                if ($block->applyToContentWithoutApp($content)) {
                    $blocksWithoutApp[] = $block;
                } elseif ($block->getBehavior() == 'holepunch') {
                    $blocksInApp[] = $block;
                } else {
                    $blocksNotApplied[] = $block;
                }
            }

            $storage->setBlocksInApp($blocksInApp)
                ->setBlocksWithoutApp($blocksWithoutApp)
                ->setBlocksNotApplied($blocksNotApplied)
                ->setContent($content)
                ;

            $contentHelper = new Mirasvit_Fpc_Helper_Fpcmf_Contentmf();
            $contentHelper->clearWrappers($content);

            // $sessionHelper = new Mirasvit_Fpc_Helper_Session();
            // if($sessionHelper->getIsCustomerLoggedIn() && isset($_SESSION['FPC_customer_name_data'])) {
            //      $content = preg_replace('/\\<span class="welcome-msg"\\>\\<span\\>(.*?)\\<\\/span\\>/ims', '<span class="welcome-msg"><span>'.$_SESSION['FPC_customer_name_data'].'</span>', $content);
            // }

            if (isset($_SESSION['FPC_cart_changed']) //update cart
                && $_SESSION['FPC_cart_changed']) {
                    $_SESSION['FPC_cart_changed'] = false;
                    return false;
            }

            if (count($blocksNotApplied)) {
                return false;
            }

            if (count($blocksInApp)) {
                Mage::app()->getRequest()
                    ->setModuleName('fpc')
                    ->setControllerName('fpcmf_actionmf')
                    ->setActionName('process')
                    ->isStraight(true);

                // restore original routing info
                $routingInfo = array(
                    'aliases' => $storage->getRequestAliases(),
                    'requested_route' => $storage->getRequestRouteName(),
                    'requested_controller' => $storage->getRequestControllerName(),
                    'requested_action' => $storage->getRequestActionName(),
                );

                Mage::app()->getRequest()->setRoutingInfo($routingInfo);


                return false;
            }

            $this->_debug->appendDebugInformation($content, $storage, Mirasvit_Fpc_Model_Configmf::HIT);
            Mirasvit_Fpc_Model_Logmf::log($storage, Mirasvit_Fpc_Model_Configmf::HIT);

            try {
                header('Fpc-Cache-Id:' . $storage->getCacheId());
            } catch (Exception $e) {}

            echo $content;

            exit;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getRequestCacheId()
    {
        if (self::$_cacheId == null) {
            $sessionHelper = new Mirasvit_Fpc_Helper_Fpcmf_Sessionmf();

            $requestDependencies = $this->_config->getRequestDependencies();
            $requestId = array();

            foreach ($requestDependencies as $key) {
                $requestId[] = $sessionHelper->get($key, true);
            }

            //different cache for logged in customers
            $sessionHelper = new Mirasvit_Fpc_Helper_Fpcmf_Sessionmf();
            if($sessionHelper->getIsCustomerLoggedIn()) {
                $requestId[] = 'logged';
            }

            self::$_cacheId = Mirasvit_Fpc_Model_Config::REQUEST_ID_PREFIX.md5(implode('_', $requestId));
        }

        // echo self::$_cacheId.'<br>';

        return self::$_cacheId;
    }

    /**
     * @return void
     */
    protected function _startSession()
    {
        if (!isset($_SESSION)) {
            $request = Mage::app()->getRequest();
            $host = $request->getHttpHost();

            session_save_path($this->_config->getSessionSavePath());
            session_module_name($this->_config->getSessionSaveMethod());
            session_set_cookie_params(0, '/', '.'.$host);
            session_name($this->_sessionName);
            session_start();
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function _canServeRequest()
    {
        $node = Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
        if ($node && is_object($node) && ($adminName = $node->__toString())) {
            if (strpos(Mage::helper('core/url')->getCurrentUrl(), "/".$adminName) !== false) {
                return false;
            }
        }

        if (!Mage::app()->useCache('fpc')) {
            return false;
        }

        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            return false;
        }

        if (isset($_GET['fpc_blocks'])) {
            return false;
        }

        $config = Mage::app()->getConfig();
        $adminName = $config->getNode('admin/routers/adminhtml/args/frontName');

        if (strpos($_SERVER['REQUEST_URI'], 'adminhtml') !== false
            || strpos($_SERVER['REQUEST_URI'], '/key/') !== false
            || strpos($_SERVER['REQUEST_URI'], $adminName) !== false) {
            return false;
        }

        return true;
    }
}
