<?php 
	class Mango_Ajaxlist_Model_Layer extends Mage_Catalog_Model_Layer{
		
		public function isDesignerFiltered(){
			$designerFiltered=false;
			foreach ($this->getState()->getFilters() as $item){
				if($item->getFilter()->getRequestVar()=="manufacturer"){
					$designerFiltered=true;
				}
			}
			
			return $designerFiltered;
		}
	}