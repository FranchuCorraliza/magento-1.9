<?php
	class Elite_ProductsDetails_Block_Details extends Mage_Catalog_Block_Product_View
	{
		public function getShippingInfo(){
			return Mage::getStoreConfig('product_info/default/shipping_info');
		}

		public function getReturnsInfo(){
			return Mage::getStoreConfig('product_info/default/returns');
		}
		
		public function getShippingRestrictions(){
			
			$categorias= $this->getProduct()->getCategoryIds();
			$categoriasZapatos=array(1037,1028,1033,1033,1034,1030,1033,1033,1099,1093,1095,1095,1092,1148,1148,1491,1453,1458,1458,1459,18455,1458,1458,1248,1242,1244,1244,1244,18450,18450,1334,1325,1330,1330,1331,1327,1330,1330,1396,1390,1392,1392,1392,1445,1445);
			$categoriasPerfumes=array(1067,1134,1067,1134,1067,1134,1283,1468,1468,1283,1283,1468,1431,1364,1364,1431,1431,1364);
			$categoriasGafas=array(1059,1124,18310,1273,1356,1421);
			$paisUS = array ('US'); 
            $paisCustomer = Mage::getSingleton('geoip/country')->getCountry();
			$result="";
			if (count(array_intersect($categoriasGafas,$categorias))>0 && in_array($paisCustomer,$paisUS)){  //Si pertenece a la categoria Gafas y est� en el listado de Marcas restringidas.
				$result=$this->__("US Customs authorities does not allow the sale of this item in the <strong>United States</strong>, so Elite decided <strong>not send this article to any destination within the US territory</strong>. Sorry for the inconvenience.");
            }
			$paiseMX = array ('MX'); 
            $paisCustomer = Mage::getSingleton('geoip/country')->getCountry();
			if (array_intersect($categorias,$categoriasZapatos) && in_array($paisCustomer,$paiseMX)){  //Si pertenece a la categoria zapatos en mujer, hombre, ni�o y a su vez el cliente es de mexico
				$result=$this->__('Mexico does not permit foreign importers of shoes to enter therefore Elite is not allowed to send this kind of product to the country.');
            }
			if (array_intersect($categorias,$categoriasPerfumes)){  //Si pertenece a la categoria Perfumes
                $result=$this->__('Customs authorities in some countries can find problems with composition of this item, so Elite decided not send this kind of items outside of European Union. If you want we ship this item to another country please contact previously with us. ');
			}
			return $result;
			
		}
	}