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
        $mail->setBodyHtml("Se ha recibido solicitud de suscripción para el sorteo de la talla ". $params['talla'] . "<br/>" . 
		"Nombre: " . $params['name'] . "<br/>" .
		"Apellido: " . $params['surname'] . "<br/>" .
		"Email: " . $params['email'] . "<br/>" .
		"Telef: " . $params['phone'] . "<br/>" );
		
        $mail->setFrom($params['email'], $params['name']);
        $mail->addTo('jesus.it@elitespain.es', 'Recibido');
        $mail->setSubject('concurso Yeezy Talla '. $params['talla']);
		
		
		$mail2 = new Zend_Mail();
        $mail2->setBodyHtml("Thank you for subscribing! This is to confirm your participation in our YEEZY BOOOST 350 TAN RAFFLE.<br/> 

Wednesday is the day! In case you&acute;re one of the lucky winners, we&acute;ll call you and drop you a line with the private link to finish your purchase. <br/>

Until then: FINGERS (AND TOES) CROSSED!<br/>

We wish you good luck,<br/>

ELITE Store<br/>
<br/>

P.S.<br/>
On Wednesday, the winners will also be published on <a href='http://www.frivolidays.com'>Frivolidays.com</a> and our social media.
");
        $mail2->setFrom("info@elitestore.es", "Elite Store");
        $mail2->addTo($params['email'], 'Recibido');
        $mail2->setSubject('CONFIRMATION: YEEZY BOOOST 350 TAN RAFFLE, SIZE '. $params['talla']);
		
		
        try {
            $mail->send();
			$mail2->send();
        }        
        catch(Exception $ex) {
            Mage::getSingleton('core/session')->addError('Unable to send email. Sample of a custom notification error from Inchoo_SimpleContact.');
 
        }
 
        //Redirect back to index action of (this) inchoo-simplecontact controller
        $this->_redirect('yeezy-raffle/?mensa=exit');
		

    }
}
 ?>