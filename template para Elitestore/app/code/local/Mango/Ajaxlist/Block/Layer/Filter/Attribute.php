<?php 
	class Mango_Ajaxlist_Block_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Attribute{
		public function isFilterOfDesignerPage(){ //Devuelve true si se trata de un filtro que solo se muestra en los listados de diseñadores
			$designerFilter=false;
			if(($this->_getAttributeFilterCode()=="designer_line") || ($this->_getAttributeFilterCode()=="runway")){
				$designerFilter=true;
			}
			return $designerFilter;
		}
		
		protected function isDesignerFilter(){
			if ($this->_filter->getRequestVar()=='manufacturer'){
				return true;
			}else{
				return false;
			}
		}
		
		protected function _getAttributeFilterCode()
		{
			return $this->_filter->getRequestVar();
		}
		
		
	}