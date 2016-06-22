<?php
/**
 * Servired  Checkout Module
 */
class Mage_Servired_Model_Standard extends Mage_Payment_Model_Method_Abstract
/**
 * @todo implements Mage_Payment_Model_Recurring_Profile_MethodInterface
 */
{
    const PAYMENT_TYPE_AUTH = 'AUTHORIZATION';
    const PAYMENT_TYPE_SALE = 'SALE';

    protected $_code = 'servired_standard';
    protected $_formBlockType = 'servired/standard_form';
    protected $_allowCurrencyCode = array(
        'ADP', 'AED', 'AFA', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'AUD', 'AWG', 'AZM',
        'BAM', 'BBD', 'BDT', 'BGL', 'BGN', 'BHD', 'BIF', 'BMD', 'BND', 'BOB', 'BOV',
        'BRL', 'BSD', 'BTN', 'BWP', 'BYR', 'BZD', 'CAD', 'CDF', 'CHF', 'CLF', 'CLP',
        'CNY', 'COP', 'CRC', 'CUP', 'CVE', 'CYP', 'CZK', 'DJF', 'DKK', 'DOP', 'DZD',
        'ECS', 'ECV', 'EEK', 'EGP', 'ERN', 'ETB', 'EUR', 'FJD', 'FKP', 'GBP', 'GEL',
        'GHC', 'GIP', 'GMD', 'GNF', 'GTQ', 'GWP', 'GYD', 'HKD', 'HNL', 'HRK', 'HTG',
        'HUF', 'IDR', 'ILS', 'INR', 'IQD', 'IRR', 'ISK', 'JMD', 'JOD', 'JPY', 'KES',
        'KGS', 'KHR', 'KMF', 'KPW', 'KRW', 'KWD', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR',
        'LRD', 'LSL', 'LTL', 'LVL', 'LYD', 'MAD', 'MDL', 'MGF', 'MKD', 'MMK', 'MNT',
        'MOP', 'MRO', 'MTL', 'MUR', 'MVR', 'MWK', 'MXN', 'MXV', 'MYR', 'MZM', 'NAD',
        'NGN', 'NIO', 'NOK', 'NPR', 'NZD', 'OMR', 'PAB', 'PEN', 'PGK', 'PHP', 'PKR',
        'PLN', 'PYG', 'QAR', 'ROL', 'RUB', 'RUR', 'RWF', 'SAR', 'SBD', 'SCR', 'SDD',
        'SEK', 'SGD', 'SHP', 'SIT', 'SKK', 'SLL', 'SOS', 'SRG', 'STD', 'SVC', 'SYP',
        'SZL', 'THB', 'TJS', 'TMM', 'TND', 'TOP', 'TPE', 'TRL', 'TRY', 'TTD', 'TWD',
        'TZS', 'UAH', 'UGX', 'USD', 'UYU', 'UZS', 'VEB', 'VND', 'VUV', 'XAF', 'XCD',
        'XOF', 'XPF', 'YER', 'YUM', 'ZAR', 'ZMK', 'ZWD'
    );

    /**
     * Get Servired session namespace
     *
     * @return Mage_Servired_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('servired/session');
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Using internal pages for input payment data
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return false;
    }

    /**
     * Using for multiple shipping address
     *
     * @return bool
     */
    public function canUseForMultishipping()
    {
        return false;
    }

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('servired/standard_form', $name)
                ->setMethod('servired_standard')
                ->setPayment($this->getPayment())
                ->setTemplate('servired/form.phtml');

        return $block;
    }

    /**
     * Valida si el codigo de la moneda esta disponible
     */
    public function validate()
    {
        parent::validate();
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        if(!in_array($currency_code, $this->_allowCurrencyCode))
        {
            Mage::throwException(Mage::helper('servired')->__('El codigo de moneda seleccionado (%s) no es compatible con Servired', $currency_code));
        }
        return $this;
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
        return $this;
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {

    }

    public function canCapture()
    {
        return true;
    }

    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('servired/standard/redirect');
    }

    /**
     * Convierte la moneda de magento al codigo de Servired
     */
    public function convertToServiredCurrency($cur)
    {
        $monedas = array(
            'ADP' => '020', 'AED' => '784', 'AFA' => '004', 'ALL' => '008',
            'AMD' => '051', 'ANG' => '532', 'AOA' => '973', 'ARS' => '032',
            'AUD' => '036', 'AWG' => '533', 'AZM' => '031', 'BAM' => '977',
            'BBD' => '052', 'BDT' => '050', 'BGL' => '100', 'BGN' => '975',
            'BHD' => '048', 'BIF' => '108', 'BMD' => '060', 'BND' => '096',
            'BOB' => '068', 'BOV' => '984', 'BRL' => '986', 'BSD' => '044',
            'BTN' => '064', 'BWP' => '072', 'BYR' => '974', 'BZD' => '084',
            'CAD' => '124', 'CDF' => '976', 'CHF' => '756', 'CLF' => '990',
            'CLP' => '152', 'CNY' => '156', 'COP' => '170', 'CRC' => '188',
            'CUP' => '192', 'CVE' => '132', 'CYP' => '196', 'CZK' => '203',
            'DJF' => '262', 'DKK' => '208', 'DOP' => '214', 'DZD' => '012',
            'ECS' => '218', 'ECV' => '983', 'EEK' => '233', 'EGP' => '818',
            'ERN' => '232', 'ETB' => '230', 'EUR' => '978', 'FJD' => '242',
            'FKP' => '238', 'GBP' => '826', 'GEL' => '981', 'GHC' => '288',
            'GIP' => '292', 'GMD' => '270', 'GNF' => '324', 'GTQ' => '320',
            'GWP' => '624', 'GYD' => '328', 'HKD' => '344', 'HNL' => '340',
            'HRK' => '191', 'HTG' => '332', 'HUF' => '348', 'IDR' => '360',
            'ILS' => '376', 'INR' => '356', 'IQD' => '368', 'IRR' => '364',
            'ISK' => '352', 'JMD' => '388', 'JOD' => '400', 'JPY' => '392',
            'KES' => '404', 'KGS' => '417', 'KHR' => '116', 'KMF' => '174',
            'KPW' => '408', 'KRW' => '410', 'KWD' => '414', 'KYD' => '136',
            'KZT' => '398', 'LAK' => '418', 'LBP' => '422', 'LKR' => '144',
            'LRD' => '430', 'LSL' => '426', 'LTL' => '440', 'LVL' => '428',
            'LYD' => '434', 'MAD' => '504', 'MDL' => '498', 'MGF' => '450',
            'MKD' => '807', 'MMK' => '104', 'MNT' => '496', 'MOP' => '446',
            'MRO' => '478', 'MTL' => '470', 'MUR' => '480', 'MVR' => '462',
            'MWK' => '454', 'MXN' => '484', 'MXV' => '979', 'MYR' => '458',
            'MZM' => '508', 'NAD' => '516', 'NGN' => '566', 'NIO' => '558',
            'NOK' => '578', 'NPR' => '524', 'NZD' => '554', 'OMR' => '512',
            'PAB' => '590', 'PEN' => '604', 'PGK' => '598', 'PHP' => '608',
            'PKR' => '586', 'PLN' => '985', 'PYG' => '600', 'QAR' => '634',
            'ROL' => '642', 'RUB' => '643', 'RUR' => '810', 'RWF' => '646',
            'SAR' => '682', 'SBD' => '090', 'SCR' => '690', 'SDD' => '736',
            'SEK' => '752', 'SGD' => '702', 'SHP' => '654', 'SIT' => '705',
            'SKK' => '703', 'SLL' => '694', 'SOS' => '706', 'SRG' => '740',
            'STD' => '678', 'SVC' => '222', 'SYP' => '760', 'SZL' => '748',
            'THB' => '764', 'TJS' => '972', 'TMM' => '795', 'TND' => '788',
            'TOP' => '776', 'TPE' => '626', 'TRL' => '792', 'TRY' => '949',
            'TTD' => '780', 'TWD' => '901', 'TZS' => '834', 'UAH' => '980',
            'UGX' => '800', 'USD' => '840', 'UYU' => '858', 'UZS' => '860',
            'VEB' => '862', 'VND' => '704', 'VUV' => '548', 'XAF' => '950',
            'XCD' => '951', 'XOF' => '952', 'XPF' => '953', 'YER' => '886',
            'YUM' => '891', 'ZAR' => '710', 'ZMK' => '894', 'ZWD' => '716',
        );
        if(isset($monedas[$cur]))
        {
            return $monedas[$cur];
        }
        return '';
    }

    /**
     * El Valor 0, indicara que no se ha determinado el idioma del
     * cliente (opcional). Otros valores posibles son:
     * Castellano-001, Ingles-002, Catalan-003,
     * Frances-004, Aleman-005, Portugues-009.
     * 3 se considera su longitud maxima
     */
    function calcLanguage($lan)
    {
        /**
          $langs = array(
          'da_DK' => '1','de_CH' => '7','de_DE' => '7','en_AU' => '2',
          'en_GB' => '2','en_US' => '2','sv_SE' => '3','nn_NO' => '4',
          );
         *
         */
        $langs = array(
            'es_ES' => '001',
            'en_US' => '002', 'en_GB' => '002', 'en_AU' => '002',
            'ca_ES' => '003',
            'fr_FR' => '004',
            'de_DE' => '005',
        );
        if(isset($langs[$lan]))
        {
            return $langs[$lan];
        }
        return '0';
    }

    public function getStandardCheckoutFormFields()
    {
        $a = $this->getQuote()->getShippingAddress();

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($this->getCheckout()->getLastRealOrderId());

        $convertor = Mage::getModel('sales/convert_order');
        $invoice = $convertor->toInvoice($order);

        $amount = $order->getTotalDue() * 100;
        $ord = $this->getCheckout()->getLastRealOrderId();

        $code = $this->getConfigData('merchantnumber');
        $currency = $this->convertToServiredCurrency($order->getOrderCurrencyCode());
        $clave = $this->getConfigData('merchantpassword');

        $transtype = $this->getTransactionType();
        if($this->getConfigData('signaturetype') == 2)
        {
            $merchurl = $this->getConfigData('merchanturl') ? $this->getConfigData('merchanturl') : Mage::getUrl('servired/standard/callback');
            /**
             * formula sha-1 completo ampliado
             */
            $message = "$amount$ord$code$currency$transtype$merchurl$clave";
        } else
        {
            /**
             * sha-1 completo
             */
            $message = "$amount$ord$code$currency$clave";
        }
        $signature = sha1($message);

        $sArr = array(
            /**
             * Obligatorio. Para Euros las dos ultimas posiciones se consideran decimales.
             */
            'Ds_Merchant_Amount' => $order->getTotalDue() * 100,
            /**
             * Obligatorio. Valor 978 para Euros, 840 para Dolares y 826 para
             * libras esterlinas. 4 se considera su longitud maxima
             */
            'Ds_Merchant_Currency' => $currency,
            /**
             * Obligatorio. Los 4 primeros digitos deben ser numericos, para
             * los digitos restantes solo utilizar los siguientes caracteres ASCII
             * Del 30 = 0 al 39 = 9
             * Del 65 = A al 90 = Z
             * Del 97 = a al 122 = z
             */
            'Ds_Merchant_Order' => $ord,
            /**
             * Obligatorio. 125 se considera su longitud maxima. Este campo
             * se mostrara al titular en la pantalla de confirmacion de la compra.
             */
            'Ds_Merchant_ProductDescription' => $this->getConfigData('mensagen'),
            /**
             * Obligatorio. Su longitud maxima es de 60 caracteres. Este
             * campo se mostrara al titular en la pantalla de confirmacion
             * de la compra.
             */
            'Ds_Merchant_Titular' => $this->getConfigData('merchanttitular'),
            /**
             * Obligatorio. Codigo FUC asignado al comercio
             */
            'Ds_Merchant_MerchantCode' => $this->getConfigData('merchantnumber'),
            /**
             * Obligatorio si el comercio tiene notificacion on line. URL
             * del comercio que recibira un post con los datos de la transaccion.
             */
            'Ds_Merchant_MerchantUrl' => $this->getConfigData('merchanturl') ? $this->getConfigData('merchanturl') : Mage::getUrl('servired/standard/callback'),
            /**
             * Obligatorio: si se envia sera utilizado como URLOK.
             */
            'Ds_Merchant_UrlOK' => Mage::getUrl('servired/standard/success'),
            /**
             * Obligatorio: si se envia sera utilizado como URLKO.
             */
            'Ds_Merchant_UrlKO' => Mage::getUrl('servired/standard/cancel'),
            /**
             * OPCIONAL Sera el nombre del comercio que aparecera en el ticket del cliente
             */
            'Ds_Merchant_MerchantName' => $this->getConfigData('merchanttitular'),
            'Ds_Merchant_ConsumerLanguage' => $this->calcLanguage(Mage::app()->getLocale()->getLocaleCode()),
            /**
             * Obligatorio. Es para completar la firma del comercio
             */
            'Ds_Merchant_MerchantSignature' => $signature,
            /**
             * Obligatorio. Numero de terminal que le asignara su banco. Por
             * defecto valor 001. 3 se considera su longitud maxima
             */
            'Ds_Merchant_Terminal' => $this->getConfigData('merchantterminal'),
            /**
             * OPCIONAL Representa la suma total de los importes de las cuotas.
             * Las dos ultimas posiciones se consideran decimales.
             */
            'Ds_Merchant_SumTotal' => '',
            'Ds_Merchant_TransactionType' => $transtype,
            /**
             * Campo opcional para el comercio para ser incluidos en los
             * datos enviados por la respuesta on-line al comercio si se ha
             * elegido esta opcion.
             */
            'Ds_Merchant_MerchantData' => '',
            /**
             * Frecuencia en dias para las transacciones recurrentes
             * (obligatorio para recurrentes)
             */
            'Ds_Merchant_DateFrecuency' => '',
            /**
             * Formato yyyy-MM-dd fecha limite para las transacciones
             * Recurrentes (Obligatorio para recurrentes)
             */
            'Ds_Merchant_ChargeExpiryDate' => '',
            /**
             * Opcional. Representa el codigo de autorizacion necesario para
             * identificar una transaccion recurrente sucesiva en las
             * devoluciones de operaciones recurrentes sucesivas.
             * Obligatorio en devoluciones de operaciones recurrentes.
             */
            'Ds_Merchant_AuthorisationCode' => $this->getConfigData('authsms'),
            /**
             * Opcional. Formato yyyy-MM-dd. Representa la fecha de la
             * operacion recurrente sucesiva, necesaria para identificar la
             * transaccion en las devoluciones de operaciones recurrentes
             * sucesivas.
             * Obligatorio para las devoluciones de operaciones recurrentes.
             */
            'Ds_Merchant_TransactionDate' => '',
            'callbackurl' => Mage::getUrl('servired/standard/callback'),
            'windowstate' => $this->getConfigData('windowstate'),
        );
        return $sArr;
    }

    /**
     * Valor opcional para el comercio para indicar que tipo de
     * transaccion es. Los posibles valores son:
     *  0 - Autorizacion
     *  1 - Preautorizacion
     *  2 - Confirmacion
     *  3 - Devolucion Automatica
     *  4 - Pago Referencia
     *  5 - Transaccion Recurrente
     *  6 - Transaccion Sucesiva
     *  7 - Autenticacion
     *  8 - Confirmacion de Autenticacion
     *  9 - Anulacion de Preautorizacion
     * @todo Retornar 5 en caso de producto recurrente
     * @return int
     */
    public function getTransactionType()
    {
        return 0;
    }

    /**
     * Retorna la url segun la configuracion
     */
    public function getServiredUrl()
    {
        if($this->getConfigData('specificurl') != '')
        {
            return $this->getConfigData('specificurl');
        }

        if($this->getConfigData('urlservired') == '1')
        {
            /**
             * Entorno Real
             */
            return "https://sis.sermepa.es/sis/realizarPago";
        } else
        {
            /**
             * Entorno de pruebas
             */
            return "https://sis-t.sermepa.es:25443/sis/realizarPago";
        }
    }
    /**
     * Pagos recurrentes
     * @todo En cuanto funcione bien en magento programar los metodos para
     * pagos recurrentes
     */
    /**
      public function validateRecurringProfile(Mage_Payment_Model_Recurring_Profile $profile)
      {
      }

      public function submitRecurringProfile(Mage_Payment_Model_Recurring_Profile $profile, Mage_Payment_Model_Info $paymentInfo)
      {
      }

      public function getRecurringProfileDetails($referenceId, Varien_Object $result)
      {
      }

      public function canGetRecurringProfileDetails()
      {
      }

      public function updateRecurringProfile(Mage_Payment_Model_Recurring_Profile $profile)
      {
      }

      public function updateRecurringProfileStatus(Mage_Payment_Model_Recurring_Profile $profile)
      {
      }
     *
     */
}