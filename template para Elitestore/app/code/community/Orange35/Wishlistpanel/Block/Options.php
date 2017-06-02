<?php
class Orange35_Wishlistpanel_Block_Options extends Mage_Core_Block_Template{
	
	

	public function getOptions(){
		$productId=$this->getRequest()->getParam('product');
		if (!$productId) {
            $data["success"] = false;
            $data["messages"] = array();
            $data["messages"][] = "Unexpected Error";
            return $data;
        }
		$product = Mage::getModel('catalog/product')->load($productId);
		if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $data["success"] = false;
            $data["messages"] = array();
            $data["messages"][] = "Cannot specify product.";
            return $data;
        }
		$simpleProducts = $product->getTypeInstance(true)->getUsedProducts(null, $product);
		
		$configurableAttributeCollection = $product->getTypeInstance()->getConfigurableAttributes();
		$result = array();
		foreach($configurableAttributeCollection as $attribute) {
			$attrCode = $attribute->getProductAttribute()->getId();	
			$html="<select name=super_attribute[$attrCode] id='attribute$attrCode' >";
			foreach ($simpleProducts as $simpleProduct) {
				if ($simpleProduct->isSaleable()) {
					$simpleProductsArray[] = $simpleProduct;
					$attrValue = $simpleProduct->getResource()->getAttribute($attribute->getProductAttribute()->getAttributeCode())->getFrontend();
					$opciones = $attrValue->getSelectOptions();
					$value = $attrValue->getValue($simpleProduct);
					$optionId=$simpleProduct->getData('talla');
					$html.="<option value='$optionId'>$value</option>";
				}
			}
		}
		$html.="</select>";
		return $html;
		
	}
	
	public function getNameProduct(){
			$id=$this->getRequest()->getParam('product');
			$product=Mage::getModel('catalog/product')->load($id);
			if ($product){
				return $product->getName();
			}
		}
		public function getBrandProduct(){
			$id=$this->getRequest()->getParam('product');
			$product=Mage::getModel('catalog/product')->load($id);
			if ($product){
				return $product->getAttributeText('manufacturer');
			}
		}
		public function getImageProduct(){
			$id=$this->getRequest()->getParam('product');
			$product=Mage::getModel('catalog/product')->load($id);
			if ($product){
				return (string) $this->helper('catalog/image')->init($product, 'image')->resize(150);
			}
		}
		
	public function getAction(){
		return $this->getRequest()->getParam('action');
	}
}