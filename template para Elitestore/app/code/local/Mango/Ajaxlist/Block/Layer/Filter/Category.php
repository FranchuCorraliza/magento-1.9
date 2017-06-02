<?php	
	class Mango_Ajaxlist_Block_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Category{
		public function isFilterOfDesignerPage(){
			return false;
		}
		public function isDesignerFilter(){
			return false;
		}
	}