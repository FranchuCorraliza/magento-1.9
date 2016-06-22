<?php
class Mage_Servired_StandardController extends Mage_Core_Controller_Front_Action
{
    protected $_callbackAction = true;

    protected function _expireAjax()
    {
        if(!Mage::getSingleton('checkout/session')->getQuote()->hasItems())
        {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton with servired standard order transaction information
     *
     * @return Mage_Servired_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('servired/standard');
    }

    public function visaAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('root')->setTemplate('page/3columns.phtml');
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('servired/standard_visa'));
        $this->renderLayout();
    }

    public function mastercardAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('root')->setTemplate('page/3columns.phtml');
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('servired/standard_mastercard'));
        $this->renderLayout();
    }

    /**
     * When a customer chooses Servired on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setServiredStandardQuoteId($session->getQuoteId());
        $order = Mage::getModel('sales/order')->load($session->getLastOrderId());
        if($session->getLastOrderId())
        {
            /**
             * Magento se encarga de restar stock al hacerse el pedido,
             * nosotros en este momento lo reponemos, y sólo lo restaremos
             * realmente si el usuario termina haciendo el pago en el TPV.
             * De esta forma, si el usuario en el TPV no paga por el motivo
             * que sea, el stock no se verá rebajado.
             * PROBLEMA: Si el usuario recarga la página de "redirect" varias
             * veces, el stock se irá incrementando... Por ello, sólo
             * incrementaremos stock si para el pedido no existe todavía un
             * comentario de "Entra en TPV". Si dicho comentario existe,
             * significa que ya se ha ejecutado redirectAction, y por tanto
             * el stock ya se incrementó y no hay que volver a hacerlo.
             */
            /**
             * Obtenemos todos los comentarios del pedido,
             * y buscamos si existe uno de "Entra en TPV"
             */
            $ya_en_TPV = false;
            $estados = $order->getStatusHistoryCollection();
            if($estados)
            {
                foreach($estados as $est)
                {
                    if($est->getComment() == Mage::helper('servired')->__('Entra en TPV'))
                    {
                        $ya_en_TPV = true;
                    }
                }
            }

            /**
             * Si no ha estado previamente en redirect, incrementamos stock.
             */
            if(!$ya_en_TPV)
            {
                /**
                 * Items de la orden
                 */
                $items = $order->getAllItems();
                if($items)
                {
                    foreach($items as $item)
                    {
                        /**
                         * Cantidad pedida
                         */
                        $quantity = $item->getQtyOrdered();

                        /**
                         * Id del producto
                         */
                        $product_id = $item->getProductId();

                        /**
                         * Stock del producto
                         */
                        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id);

                        /**
                         * Incrementamos el stock
                         */
                        $stock->setQty($stock->getQty() + $quantity);
                        $stock->setIsInStock(true);

                        /**
                         * Guardamos
                         */
                        $stock->save();
                    }
                }

                $state = Mage::getModel('servired/standard')->getConfigData('redirect_status');
                $order->setState($state, $state, Mage::helper('servired')->__('Entra en TPV'), false);
                $order->save();
            }

            $this->loadLayout();
            $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('servired/standard_redirect'));
            $this->renderLayout();
        }
    }

    /**
     * Esta función se ejecuta cuando el TPV redirecciona al usuario
     * a la URLKO. Es decir, el proceso de pago ha fallado,
     * y simplemente se redirecciona al usuario de vuelta a la tienda,
     * pudiéndosele mostrar un mensaje sobre el fallo de la transacción.
     * Aquí no se debe actualizar el pedido, el stock, ni nada por el estilo,
     * eso debe hacerse en callbackAction.
     */
    public function cancelAction()
    {
        $params = $this->getRequest()->getParams();
        $session = Mage::getSingleton('checkout/session');

        /**
         * Si en la URLOK se han recibido parametros (en la administracion del
         * TPV se puede poner que se reciban o no, por defecto es NO)
         * Es MUY RECOMENDABLE configurar el TPV para que envie parametros a
         * las URLOK y URLKO de otra forma no se podra certificar que la
         * comunicacion viene del TPV y no de otra fuente.
         */
        if(count($params) > 0)
        {
            $Ds_Response = $params['Ds_Response'];
            if($this->firmaValida($params))
            {
                $message = Mage::helper('servired')->__($this->comentarioReponse($Ds_Response, $params['Ds_PayMethod']));
                $comment = Mage::helper('servired')->__('Pedido cancelado desde Servired con error cod. %s - %s', $Ds_Response, $message);

                /**
                 * Redireccionamos al carrito avisando del error.
                 */
                $session->addError($comment);
                $this->_redirect('checkout/cart');
            } else
            {
                /**
                 * Si la firma no es valida redireccionar a Página Inicio de la
                 * tienda.
                 */
                $this->_redirect('');
            }
        } else
        {
            /**
             * Si no se han recibido parametros
             */
            $session->addError(Mage::helper('servired')->__('Transaccion denegada desde Servired.'));
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * Esta funcion se ejecuta cuando el TPV redirecciona al usuario
     * a la URLOK. Es decir, el proceso de pago ha ido bien,
     * y simplemente se redirecciona al usuario de vuelta a la tienda,
     * pudiendosele mostrar un mensaje sobre el exito de la transaccion.
     * Aqui no se debe actualizar el pedido, el stock, ni nada por el estilo,
     * eso debe hacerse en callbackAction. Hay que tener en cuenta que
     * el usuario puede haber pagado y cerrado la ventana del TPV sin
     * pulsar "continuar", en ese caso no se redireccionaría al usuario
     * a URLOK (successAction).
     */
    public function successAction()
    {
        $params = $this->getRequest()->getParams();

        /**
         * Si en la URLOK se han recibido parametros (en la administracion del
         * TPV se puede poner que se reciban o no, por defecto es NO)
         * Es MUY RECOMENDABLE configurar el TPV para que envie parametros a
         * las URLOK y URLKO de otra forma no se podra certificar que la
         * comunicacion viene del TPV y no de otra fuente.
         */
        if(count($params) > 0)
        {
            $Ds_Response = $params['Ds_Response'];
            if($this->firmaValida($params))
            {
                $comment = '';
                if($Ds_Response == '0930')
                {
                    if($params['Ds_PayMethod'] == 'R')
                    {
                        $comment = 'Realizado por Transferencia bancaria';
                    } else
                    {
                        $comment = 'Realizado por Domiciliacion bancaria';
                    }
                } elseif($Ds_Response >= '0000' && $Ds_Response <= '0099')
                {
                    $comment = 'Transaccion autorizada para pagos y preautorizaciones (codigo: %s)';
                } elseif($params['Ds_Response'] == '0900')
                {
                    $comment = 'Transaccion autorizada para devoluciones y confirmaciones (codigo: %s)';
                }
                $session = Mage::getSingleton('checkout/session');
                $session->addsuccess(Mage::helper('servired')->__($comment, $Ds_Response));
                $this->_redirect('checkout/onepage/success');

                //Si la firma no es valida
            } else
            {
                /**
                 * Redireccionar a Pagina Inicio de la tienda.
                 */
                $this->_redirect('');
            }
        } else
        {
            /**
             * Si no se enviaron parametros
             * Si esta configurado el TPV para no mandar parametros por GET
             * a las URLOK y URLKO, no nos queda mas remedio que mostrar
             * el mensaje de "transacción correcta", aunque puede que
             * la llamada a la URLOK o URLKO no se haya hecho por el TPV.
             * A nivel de seguridad no es importante, puesto que aunque
             * se muestre el mensaje de que se ha hecho correctamente
             * la transaccion, en realidad el estado de la transaccion
             * se controla mediante el callbackAction (que obligatoriamente
             * recibe parametros via POST, entre ellos la firma)
             */
            $session = Mage::getSingleton('checkout/session');
            $session->addsuccess(Mage::helper('servired')->__('Transaccion autorizada'));
            $this->_redirect('checkout/onepage/success');
        }
    }

    /**
     * Aqui se recoge la respuesta del TPV informando acerca
     * de la transaccion. Esta funcion es la que maneja la
     * notificacion online por parte del TPV.
     * En la configuracion de la extension se debe poner
     * como URL del comercio (Ds_Merchant_MerchantURL) algo asi:
     * http://www.example.com/servired/standard/callback/
     * @mac75a CREO QUE HABRÍA QUE MEJORAR LA EXTENSIÓN HACIENDO QUE
     * LA URL DEL COMERCIO NO FUERA CONFIGURABLE POR EL
     * USUARIO (COMO SE HACE CON URLOK y URLKO), YA QUE SI EL
     * USUARIO NO PONE LA RUTA MENCIONADA MAS ARRIBA NO FUNCIONARA
     *
     * @error403 Hay ya un intento de algo asi configurando en el config.xml
     * por defecto: {{secure_base_url}}/servired/standard/callback
     */
    public function callbackAction()
    {

        /**
         * Indicador para determinar si la transacción fue autorizada o no.
         */
        $autorizada = false;

        $params = $this->getRequest()->getPost();
        if(count($params) > 0)
        {

            $Ds_Order = $params['Ds_Order'];
            $Ds_Response = $params['Ds_Response'];

            if($this->firmaValida($params))
            {
                $comment = null;

                /**
                 * Hay que analizar DS_RESPONSE para saber el resultado de la
                 * transaccion
                 */
                if($Ds_Response >= '0000' && $Ds_Response <= '0099')
                {
                    $comment = 'Transaccion autorizada para pagos y preautorizaciones (codigo: %s)';
                    $autorizada = true;
                } elseif($Ds_Response == '0900')
                {
                    $comment = 'Transaccion autorizada para devoluciones y confirmaciones (codigo: %s)';
                    $autorizada = true;
                } elseif($Ds_Response == '0930')
                {
                    /**
                     * El codigo 0930 no aparece en la documentacion que tengo de la caixa.
                     * No obstante, lo dejo como en la extension original.
                     */
                    $autorizada = true;
                    if($params['Ds_PayMethod'] == 'R')
                    {
                        $comment = 'Pago realizado por Transferencia bancaria';
                    } else
                    {
                        $comment = 'Pago realizado por Domiciliacion bancaria';
                    }
                } else
                {
                    $comment = $this->comentarioReponse($Ds_Response, $params['Ds_PayMethod']);
                }

                /**
                 * Si la transaccion fue autorizada
                 */
                if($autorizada == true)
                {
                    $order = Mage::getModel('sales/order');

                    /**
                     * Cargamos el pedido
                     */
                    $order->loadByIncrementId($Ds_Order);

                    /**
                     * Si el pedido existe (que es lo logico, puesto
                     * que estamos recibiendo confirmacion de pago)
                     */
                    if($order->getId())
                    {
                        $orderStatus = Mage::getModel('servired/standard')->getConfigData('order_status');

                        /**
                         * Actualizamos al nuevo estado del pedido (el nuevo estado
                         * se configura en el backend de la extension servired)
                         */
                        $order->setState($orderStatus, $orderStatus, Mage::helper('servired')->__($comment, $Ds_Response), true);
                        $order->save();
                        /**
                         * Bajamos el stock (tener en cuenta que se ha autorizado
                         * la orden de pago)
                         */
                        /**
                         * Obtenemos todos los articulos del pedido
                         */
                        $items = $order->getAllItems();
                        if($items)
                        {
                            foreach($items as $item)
                            {
                                /**
                                 * Cantidad pedida
                                 */
                                $quantity = $item->getQtyOrdered();

                                /**
                                 * Id del producto
                                 */
                                $product_id = $item->getProductId();

                                /**
                                 * Stock del producto
                                 */
                                $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id);

                                /**
                                 * Incrementamos el stock
                                 */
                                $cantidad_anterior = $stock->getQty();

                                /**
                                 * Restamos del stock la cantidad del pedido
                                 */
                                $stock->setQty($cantidad_anterior - $quantity);

                                /**
                                 * Si se han agotado las existencias
                                 */
                                if($cantidad_anterior - $quantity <= 0)
                                {
                                    /**
                                     * Marcamos como fuera de existencia
                                     */
                                    $stock->setIsInStock(false);
                                }

                                /**
                                 * Guardamos
                                 */
                                $stock->save();
                            }
                        }

                        /**
                         * Si esta configurado asi en el backend, mandamos al
                         * cliente email avisando de su pedido.
                         */
                        if(((int) Mage::getModel('servired/standard')->getConfigData('sendmailorderconfirmation')) == 1)
                        {
                            $order->sendNewOrderEmail();
                        }

                        /**
                         * Si esta configurado asi en el backend, hacemos factura.
                         */
                        if((bool) Mage::getModel('servired/standard')->getConfigData('autoinvoice'))
                        {
                            $invoice = $order->prepareInvoice();
                            $invoice->register()->capture();
                            Mage::getModel('core/resource_transaction')
                                ->addObject($invoice)
                                ->addObject($invoice->getOrder())
                                ->save();
                            $order->setState($orderStatus, $orderStatus, Mage::helper('servired')->__('<br />Factura %s creada', $invoice->getIncrementId()), true);
                            $order->save();

                            /**
                             * Si esta asi configurado, mandamos email al cliente
                             * con la factura.
                             */
                            if(((int) Mage::getModel('servired/standard')->getConfigData('sendmailorderconfirmation')) == 1)
                            {
                                $invoice->sendEmail();
                            }
                        }
                    }
                } else
                {
                    /**
                     * Si la transaccion fue denegada
                     */
                    $order = Mage::getModel('sales/order');

                    /**
                     * Cargamos el pedido
                     */
                    $order->loadByIncrementId($Ds_Order);
                    $state = Mage::getModel('servired/standard')->getConfigData('error_status');
                    $order->setState($state, $state, $comment, true);
                    $order->save();
                    if(((int) Mage::getModel('servired/standard')->getConfigData('sendmailorderconfirmation')) == 1)
                    {
                        $order->sendOrderUpdateEmail(true, $message);
                    }
                }
            } else
            {
                /**
                 * Si las firmas no coinciden
                 */
                /**
                 * De momento, si las firmas no coinciden
                 * se redirecciona a home
                 */
                $this->_redirect('');
            }
        } else
        {
            /**
             * Si no hay parametros
             */
            /**
             * De momento si no nos mandan parametros
             * redireccionamos a la pagina inicio.
             */
            $this->_redirect('');
        }
    }

    public function firmaValida($params)
    {
        $Ds_Amount = $params['Ds_Amount'];
        $Ds_Order = $params['Ds_Order'];
        $Ds_Currency = $params['Ds_Currency'];
        $Ds_Signature = $params['Ds_Signature'];
        $Ds_Response = $params['Ds_Response'];

        /**
         * Para ver si la comunicacion recibida procede realmente del TPV
         * hay que verificar la firma.
         * Digest=SHA-1(Ds_ Amount + Ds_ Order + Ds_MerchantCode + Ds_ Currency + Ds _Response + CLAVE SECRETA)
         */
        $clave = Mage::getModel('servired/standard')->getConfigData('merchantpassword');
        $dsmerchantcode = Mage::getModel('servired/standard')->getConfigData('merchantnumber');
        $cadenafirma = "$Ds_Amount$Ds_Order$dsmerchantcode$Ds_Currency$Ds_Response$clave";
        $firmagenerada = sha1($cadenafirma);

        /**
         * Si la firma generada coincide con la que manda el TPV
         * (se comparan en minúsculas)
         */
        return (strtolower($firmagenerada) == strtolower($Ds_Signature));
    }

    /**
     * Retorna el comentario en base al codigo de response
     */
    public function comentarioReponse($Ds_Response, $Ds_pay_method='')
    {
        switch($Ds_Response)
        {
            case '101':
                return 'Tarjeta caducada';
                break;
            case '102':
                return 'Tarjeta en excepcion transitoria o bajo sospecha de fraude';
                break;
            case '104':
                return 'Operacion no permitida para esa tarjeta o terminal';
                break;
            case '116':
                return 'Disponible insuficiente';
                break;
            case '118':
                return 'Tarjeta no registrada';
                break;
            case '129':
                return 'Codigo de seguridad (CVV2/CVC2) incorrecto';
                break;
            case '180':
                return 'Tarjeta ajena al servicio';
                break;
            case '184':
                return 'Error en la autenticacion del titular';
                break;
            case '190':
                return 'Denegacion sin especificar Motivo';
                break;
            case '191':
                return 'Fecha de caducidad erronea';
                break;
            case '202':
                return 'Tarjeta en excepcion transitoria o bajo sospecha de fraude con retirada de tarjeta';
                break;
            case '0930':
                if($Ds_pay_method == 'R')
                {
                    return 'Realizado por Transferencia bancaria';
                } else
                {
                    return 'Realizado por Domiciliacion bancaria';
                }
                break;
            case '912':
            case '9912':
                return 'Emisor no disponible';
                break;
            default:
                return 'Transaccion denegada';
                break;
        }
    }
}
