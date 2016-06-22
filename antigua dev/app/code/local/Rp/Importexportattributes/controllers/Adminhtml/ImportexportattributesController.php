<?php
/**
 * Author: Magerips
 * Website: www.magerips.com
 * Suport Email: support@magerips.com
 *
**/
class Rp_Importexportattributes_Adminhtml_ImportexportattributesController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {  
        // Load layout.
        $this->loadLayout();
         
        // Set block and template to use for admin.
        $this->_addContent( $this->getLayout()
        ->createBlock( 'importexportattributes/adminhtml_adminimportexportattributes' )
        ->setTemplate( 'importexportattributes/adminimportexportattributes.phtml' ) );
 
        // Render layout.
        $this->renderLayout();
    }   
}
?>