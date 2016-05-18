<?php
/*
 * Developer: Rene Voorberg
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 *
*/
class Glace_Ajaxcart_Block_Checkout_Cart_Item_Renderer_Configurable extends Mage_Checkout_Block_Cart_Item_Renderer_Configurable
{

    /**
     * Get item configurable child product
     * Rewritten to use associated product image when adding product to the cart or updating configurable options on shopping cart page
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getChildProduct()
    {
        if ($option = $this->getItem()->getOptionByCode('simple_product')) {
        	return Mage::getModel('catalog/product')->load($option->getProductId());
            //return $option->getProduct();
        }
        return $this->getProduct();
    }

    public function getFormatedOptionValue($optionValue)
    {
    	if(Mage::helper('ajaxcart')->isEnabled()) {
			return $optionValue;
		} else {
	        /* @var $helper Mage_Catalog_Helper_Product_Configuration */
	        $helper = Mage::helper('catalog/product_configuration');
	        $params = array(
	            'max_length' => 55,
	            'cut_replacer' => ' <a href="#" class="dots" onclick="return false">...</a>'
	        );
	        return $helper->getFormattedOptionValue($optionValue, $params);
	    }
    }	
	
    //Get list of all options for product 
    public function getOptionList()
    {
    	$browserInfo = Mage::helper('ajaxcart')->getBrowserInfo();
		if (Mage::getSingleton('customer/session')->getAjaxCartAction() == 'cart' && ($browserInfo['name']!='msie' || ($browserInfo['name']=='msie' && $browserInfo['version']>7))) {
			//load configurable data
			$_product = $this->getProduct();
			Mage::getBlockSingleton('catalog/product_view_type_configurable')->unsetData();
			$_configurable = Mage::getBlockSingleton('catalog/product_view_type_configurable')->setData('product', $_product);			
			
			//add containerId to config
			$jsonConfigDecoded = json_decode($_configurable->getJsonConfig());
			$jsonConfig = Mage::helper('core')->jsonDecode($_configurable->getJsonConfig());
			$jsonConfig['containerId'] = 'itemSelectContainer'.$this->getItem()->getId();
			$jsonConfig = Mage::helper('core')->jsonEncode($jsonConfig);
		
			//generate select for each attribute
			$attributeLabels = array();
			$attributes = array();
			$i = 0;		
			$itemSerial = $this->_randString(5);
			foreach($jsonConfigDecoded->attributes as $attribute) {
				$attributeLabels[] = $attribute->label;
				$attributes[$i] = array();
				
				$onChange = (count($_configurable->getAllowAttributes())==($i+1)) ? 'onchange="ajaxcart.updateItemOptions(this);"' : '';				
				$selectHtml = '';
				$selectHtml .= '<div id="itemSelectContainer'.$this->getItem()->getId().'"><select '.$onChange.' style="width:150px;" name="cart['.$this->getItem()->getId().'][option]['.$attribute->id.']" id="item'.$itemSerial.'attribute'.$attribute->id.'" class="required-entry super-attribute-select">
			                    	<option>'.$this->__('Choose an Option...').'</option>
			                    </select></div>';
			                    
				$attributes[$i]['id'] = $attribute->id;
				$attributes[$i]['label'] = $attribute->label;
				$attributes[$i]['value'] = $selectHtml;				
				$i++;
			}
			
			$selectOptionsJs = '';
			$j = 0;
			$helper = Mage::helper('catalog/product_configuration');
			$options = $helper->getConfigurableOptions($this->getItem());
 			foreach((array)$options as $_option) {
 				//add custom options
 				if (!in_array($_option['label'], $attributeLabels)) {
					$attributes[$j]['label'] = $_option['label'];
					$attributes[$j]['value'] = $_option['value'];
 				} else {
 				//set selected configurable options
					$selectOptionsJs .= '$$(\'select#item'.$itemSerial.'attribute'.$attributes[$j]['id'].' option\').each(function(opt) {
											 var optHtml = opt.innerHTML;
										     opt.selected = optHtml.substring(0, '.strlen($_option['value']).') == \''.str_replace("'","\'",$_option['value']).'\' && ( optHtml.substring('.strlen($_option['value']).', '.(strlen($_option['value'])+2).') == " -" || optHtml.substring('.strlen($_option['value']).', '.(strlen($_option['value'])+2).') == " +" || optHtml.substring('.strlen($_option['value']).', '.(strlen($_option['value'])+2).') == "");
										 });										 
										 item'.$itemSerial.'Config.configureElement($(\'item'.$itemSerial.'attribute'.$attributes[$j]['id'].'\'));';
				}
				$j++;
			}		
			
			$attributes[$j-1]['value'] .= '<script type="text/javascript">
					                    	   var item'.$itemSerial.'Config = new Product.Config('.$jsonConfig.');
					                    	   '.$selectOptionsJs.'
					                       </script>';
			return $attributes;	
		} else {
			return parent::getOptionList();
        }
    }    
    
    //Get quote item qty
    //Add qty input with increase/decrease buttons if enabled
    public function getQty()
    {
    	return Mage::helper('ajaxcart')->getQty($this, parent::getQty()); 
    }  
    
    //generate random lowercase letters string only
    private function _randString($length, $charset='abcdefghijklmnopqrstuvwxyz')
	{
	    $str = '';
	    $count = strlen($charset);
	    while ($length--) {
	        $str .= $charset[mt_rand(0, $count-1)];
	    }
	    return $str;
	}
}
