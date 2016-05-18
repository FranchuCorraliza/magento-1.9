<?php
class Echidna_Wordpress_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }
	
	public function getHeaderAction()
	{
		$this->loadLayout();     
		$blog_name =  $this->getRequest()->getParam('blog');
		
		$blog_dir = Mage::getBaseDir(). DS .$blog_name;
		if(file_exists($blog_dir))
		{
			$headerHtml = $this->getLayout()->getBlock('header')->toHtml();
			echo $headerHtml;
		}else{
			$exist_error = 'The dir name,'.$blog_name.'is not exist';
			echo $exist_error;
		}
		
	}
	
	public function getHeadAction()
	{
		$this->loadLayout();     
		$blog_name =  $this->getRequest()->getParam('blog');
		
		$blog_dir = Mage::getBaseDir(). DS .$blog_name;
		if(file_exists($blog_dir))
		{
			$headHtml = $this->getLayout()->getBlock('head')->toHtml();
			echo $headHtml;
		}else{
			$exist_error = 'The dir name,'.$blog_name.'is not exist';
			echo $exist_error;
		}
		
	}
	
	public function getFooterAction()
	{
		$this->loadLayout();     
		$blog_name = $this->getRequest()->getParam('blog');
		
		$blog_dir = Mage::getBaseDir(). DS .$blog_name;
		if(file_exists($blog_dir))
		{
			$footerHtml = $this->getLayout()->getBlock('footer')->toHtml();
			echo $footerHtml;
		}else{
			$exist_error = 'The dir name,'.$blog_name.'is not exist';
			echo $exist_error;
		}
	}
	
	public function writeHeaderAction()
	{
		$header_html = $this->getRequest()->getParam('html');
		$dir_name = $this->getRequest()->getParam('dir');
		
		if(!empty($header_html)){
			Mage::helper('wordpress')->writeHeaderFile($header_html,$dir_name);
			//echo 'successfully write header file';
		}else{
			$writeHeaderHtmlError = 'Html is not deefiend';
			echo $writeHeaderHtmlError;
		}
	}
	
	
	public function writeFooterAction()
	{
		$footer_html = $this->getRequest()->getParam('html');
		$dir_name = $this->getRequest()->getParam('dir');
		
		if(!empty($footer_html)){
			Mage::helper('wordpress')->writeFooterFile($footer_html,$dir_name);
			//echo 'successfully write footer file';
		}else{
			$writeFooterHtmlError = 'Html is not defiend';
			echo $writeFooterHtmlError;
		}
	}
	
	public function writeHeadAction()
	{
		$head_html = $this->getRequest()->getParam('html');
		$dir_name = $this->getRequest()->getParam('dir');
		
		if(!empty($head_html)){
			Mage::helper('wordpress')->writeHeadFile($head_html,$dir_name);
			//echo 'successfully write Head file';
		}else{
			$writeHeadHtmlError = 'Html is not defiend';
			echo $writeHeadHtmlError;
		}
	}
	
}