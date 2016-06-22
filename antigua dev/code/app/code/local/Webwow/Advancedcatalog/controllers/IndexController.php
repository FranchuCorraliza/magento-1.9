<?php 
class Webwow_Advancedcatalog_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
        public function sendemailAction()
    {
        //Fetch submited params
        $params = $this->getRequest()->getParams();
 
        $mail = new Zend_Mail();
        $mail->setBodyText("El cliente " . $params['name'] . ' ha pedido la Talla ' . $params['talla'] . ' de yeezy ' . "con el email " . $params['email']);
        $mail->setFrom($params['email'], $params['name']);
        $mail->addTo('francisco.it@elitespain.es', 'Recibido');
        $mail->setSubject('concurso Yeezy Talla '. $params['talla']);
        try {
            $mail->send();
        }        
        catch(Exception $ex) {
            Mage::getSingleton('core/session')->addError('Unable to send email. Sample of a custom notification error from Inchoo_SimpleContact.');
 
        }
 
        //Redirect back to index action of (this) inchoo-simplecontact controller
        $this->_redirect('yeezy-raffle/');
    }
}
 ?>