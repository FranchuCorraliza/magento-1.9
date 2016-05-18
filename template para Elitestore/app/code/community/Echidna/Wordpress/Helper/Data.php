<?php

class Echidna_Wordpress_Helper_Data extends Mage_Core_Helper_Abstract
{
	/* Craete & Copy Wordpress folder to magento base directory with name 'blog' */
	public function copyWordpress($src,$dst,$dirname)
	{
		$filename = $dst. DS . $dirname . DS ;
		if (file_exists($filename))
		{
			$msg = "The directory $dirname exists. So The directory was overwrite.";
		}else{
			$msg = "The directory $dirname was successfully created.";
		}
		
		$zip = new ZipArchive;
		if ($zip->open($src) === TRUE) {
			$zip->extractTo($dst);
			$zip->close();
			rename( $dst. DS .'Wordpress',$dirname); // rename blog directory
		} else {
			$msg = 'failed to unzip!!! ';
		}
		echo $msg;
		return;
	}
	
	protected function recurse_copy($src,$dst) { 
		$dir = opendir($src); 
		@mkdir($dst); 
		while(false !== ( $file = readdir($dir)) ) { 
			if (( $file != '.' ) && ( $file != '..' )) { 
				if ( is_dir($src . '/' . $file) ) { 
					$this->recurse_copy($src . '/' . $file,$dst . '/' . $file); // Recursive function 
				} 
				else { 
					copy($src . '/' . $file,$dst . '/' . $file); 
				} 
			} 
		} 
		closedir($dir); 
	}
	
	/* Craete directory , Header html file and copy the html content to that file */
	public function writeHeaderFile($html,$dir_name)
	{
		$dirname = 'chf'; // directory name chf, inside in blog directory 
		$chf_dir = Mage::getBaseDir(). DS .$dir_name. DS . $dirname . DS ;
		$blog_dir = Mage::getBaseDir(). DS .$dir_name. DS ;
		if (!file_exists($chf_dir)) 
		{
			mkdir("$blog_dir/" . $dirname, 0777); // craete chf directory
			$msg = "The directory $dirname was successfully created.";
			Mage::getSingleton('adminhtml/session')->addSuccess($msg); 
		} else {
			$msg = "The directory $dirname exists.";
			Mage::getSingleton('adminhtml/session')->addWarning($msg); 
		}
		
		$headerFileName = $chf_dir. DS .'custom_header.html'; // header file name 'custom_header.html'
		
		$myfile = fopen($headerFileName, "w") or die("Unable to open file!"); // check if file is exist or not, if it is not then it will craete 
		
		$txt = $html;
		fwrite($myfile, $txt);
		fclose($myfile);
		
		return;
	}
	
	/* Craete directory , Footer html file and copy the html content to that file */
	public function writeFooterFile($html,$dir_name)
	{
		$dirname = 'chf'; // directory name chf, inside in blog directory 
		$chf_dir = Mage::getBaseDir(). DS .$dir_name. DS . $dirname . DS ;
		$blog_dir = Mage::getBaseDir(). DS .$dir_name. DS ;
		if (!file_exists($chf_dir)) 
		{
			mkdir("$blog_dir/" . $dirname, 0777); // craete chf directory
			$msg = "The directory $dirname was successfully created.";
			Mage::getSingleton('adminhtml/session')->addSuccess($msg); 
		}
		
		$footerFileName = $chf_dir. DS .'custom_footer.html'; // footer file name 'custom_footer.html'
		
		$myfile = fopen($footerFileName, "w") or die("Unable to open file!"); // check if file is exist or not, if it is not then it will craete 
		
		$txt = $html;
		fwrite($myfile, $txt);
		fclose($myfile);
		
		return;
	}
	
	/* Craete directory , Head html file and copy the html content to that file */
	public function writeHeadFile($html,$dir_name)
	{
		$dirname = 'chf'; // directory name chf, inside in blog directory 
		$chf_dir = Mage::getBaseDir(). DS .$dir_name. DS . $dirname . DS ;
		$blog_dir = Mage::getBaseDir(). DS .$dir_name. DS ;
		
		if (!file_exists($chf_dir)) 
		{
			mkdir("$blog_dir/" . $dirname, 0777); // craete chf directory
			$msg = "The directory $dirname was successfully created.";
			Mage::getSingleton('adminhtml/session')->addSuccess($msg); 
		}
		
		$headFileName = $chf_dir. DS .'custom_head.html'; // footer file name 'custom_footer.html'
		
		$myfile = fopen($headFileName, "w") or die("Unable to open file!"); // check if file is exist or not, if it is not then it will craete 
		
		$txt = $html;
		fwrite($myfile, $txt);
		fclose($myfile);
		
		return;
	}
}