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
        //codigo nuevo
        $mailTemplate        = Mage::getModel('core/email_template');
        $translate           = Mage::getSingleton('core/translate');
        $titulo="Yeezy Boost 750";
        $fecha = "Monday, June, 13 th";
        $sender      = array('name'=> 'Elitestore',
                                 'email' => 'info@elitestore.es');
        $vars        = array('talla' => $params['talla'], 'titulo' => $titulo, 'fecha' => $fecha, 'nombre'=>$params['name'], 'apellidos' => $params['surname'], 'email'=>$params['email'], 'telefono'=>$params['phone']);
        $storeId     = Mage::app()->getStore()->getStoreId();

        //email que manda al cliente

            if(($storeId==2) || ($storeId ==6)){
                $templateId = 69;
            }
            else{
                $templateId = 71;
            }

            $template_collection = $mailTemplate->load($templateId);
            $template_data       = $template_collection->getData();
            $email               = $params['email'];
            $name                = $params['name'];

            try {
                $mailTemplate->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
            if(!$mailTemplate->getSentSuccess()) {
                echo "Something went wrong...<br>";
            } else {
                echo "Message sent to ".$email."!!!<br>";
            }
            $translate->setTranslateInline(true);
            } catch(Exception $e) {
               Mage::logException($e)  ;
            }
        //email que nos manda a nosotros

            $templateId          = 70;
            $template_collection = $mailTemplate->load($templateId);
            $template_data       = $template_collection->getData();
            $email               = "yeezy.raffle@elitestore.es";
            $name                = "sorteo yeezy";

            try {
                $mailTemplate->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
            if(!$mailTemplate->getSentSuccess()) {
                echo "Something went wrong...<br>";
            } else {
                echo "Message sent to ".$email."!!!<br>";
            }
            $translate->setTranslateInline(true);
            } catch(Exception $e) {
               Mage::logException($e)  ;
            }

        //Redirect back to index action of (this) inchoo-simplecontact controller
        $this->_redirect('yeezy-raffle/?mensa=exit');
		

    }
}
 ?>