<?php
class Echidna_Wordpress_Adminhtml_InstallController extends Mage_Adminhtml_Controller_Action
{
	protected function _getSourcePath()
	{
		return Mage::getModuleDir('wordpress', 'Echidna_Wordpress').'\wordpress.zip';
	}
	
	protected function _getDestinationPath()
	{
		return Mage::getBaseDir();
	}
	
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('wordpress/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }   
   
    public function indexAction()
	{
		$this->loadLayout();
		$this->_setActiveMenu('wordpress/items');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			
        $this->_initAction();       
        $this->_addContent($this->getLayout()->createBlock('wordpress/adminhtml_install')->setTemplate('wordpress/install.phtml'));
        $this->renderLayout();
       }
	
	/* If directory is exist or not */
	public function checkDirAction()
	{
		
		$dir_name = $this->getRequest()->getParam('blog');
		$dst = Mage::getBaseDir();
		$filename = $dst. DS .$dir_name . DS ;
		
		if(file_exists($filename)){
			echo "Directory, $dir_name already exits
                              click 'Ok' to overwrite and proceed.
                              click 'Cancel' to stop."
                              ;
		}
	 }
	
	
	/* */
	public function copyAction()
	{
		$dir_name = $this->getRequest()->getParam('blog'); 
		
		/* Get Source and destination directory */
		$src = $this->_getSourcePath();
		$dst = $this->_getDestinationPath();
		
		/* Call copy function from helper */
		$result = Mage::helper('wordpress')->copyWordpress($src,$dst,$dir_name);
	}
	
}