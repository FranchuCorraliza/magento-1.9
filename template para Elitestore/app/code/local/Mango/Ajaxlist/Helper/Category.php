<?php

class Mango_Ajaxlist_Helper_Category extends Mage_Core_Helper_Abstract {

    /**
     * Template for filter items block
     *
     * @see Mage_Catalog_Block_Layer_Filter
     */
    /* first: get category tree based on current category... */
    function renderCategoryMenuItemHtml($category, $_layer_items, $level = 0, $isLast = false, $isFirst = false, $include_parent = false) {
		if (!$category->getIsActive()) {
            return '';
        }
        $html = array();
		$children = $category->getChildrenCategories();
        // select active children
        $activeChildren = array();
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);
        // prepare list item html classes
        $classes = array();
		$ulClass = "";
        $classes[] = 'level' . $level;

        $linkClass = '';
		$linkClass2= '';
        if ($isFirst) {
            $classes[] = 'first';
        }
        if ($isLast) {
            $classes[] = 'last';
        }
        if ($hasActiveChildren) {
            $classes[] = 'parent';
        } else {
            $classes[] = 'child';
        }
		if ($level==3){
			$linkClass2="category-ppal";
		}
       
	   
	   
		
	    if ($this->isItemActive($category->getId())) {
            $classes[] = 'active-filter-option';			
			//Desplegamos subcategorias si la categoría tiene hijos	
			if ($hasActiveChildren):
				$linkClass="category-collapse";
				$ulClass ="style='display:block'";
			endif;	
        }else{
			if ($hasActiveChildren || ($level==1)) :
				$linkClass="category-expand";
			endif;
		}
		//Mage::log($_layer_items,null,"category_helper.log");
		if ($_layer_items && isset($_layer_items[$category->getId()])) {
                $_count = $_layer_items[$category->getId()]['count'];
            } else {
                $_count = 0;//$this->categoryProductCount($category);
                
            }
			if ($_count==0){
				$classes[]="disabled";
			}
		
		// prepare list item attributes
        $attributes = array();
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }

        if ($include_parent) {

            // assemble list item with attributes
            $htmlLi = '<li';
            foreach ($attributes as $attrName => $attrValue) {
                $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
            }
            
			
			$htmlLi .= '>';
            $html[] = $htmlLi;
			$html[] ='<a href="' . $category->getUrl() . '" title="'.$category->getName().'" url="' . $this->getUrl($category->getId(),$level,$category) . '" data-attribute-value="' . $category->getId() . '" class="' . $linkClass2 . ' ' . $linkClass . '">';
//            $html[] = '<a href="' . $this->getUrl($category->getId()) . '" data-attribute-value="' . $category->getId() . '" class="' . $linkClass . '">';
            if ($level==1){
				$html[] = Mage::helper('core')->escapeHtml($category->getName());
			}else{
				$html[] = Mage::helper('core')->escapeHtml($category->getName());
			}

            

            //
            //}
            //$html[] = '<span class="item-count">(' . (int) $_count . ')</span>';
            $html[] = '</a>';
        }


        // render children
        $htmlChildren = '';
        $j = 0;
        foreach ($activeChildren as $child) {
            
            $htmlChildren .= $this->renderCategoryMenuItemHtml(
                    $child, $_layer_items, ($level + 1), ($j == $activeChildrenCount - 1), ($j == 0), true);
            $j++;
        }
        if (!empty($htmlChildren)) {
			
            if ($include_parent)
                $html[] = '<ul class="level' . $level . '" '.$ulClass.'>';
            $html[] = $htmlChildren;
            if ($include_parent)
                $html[] = '</ul>';
        }
        if ($include_parent)
            $html[] = '</li>';

        $html = implode("\n", $html);
		return $html;
    }

    function getUrl($_value, $level,$category) {
        $_query = Mage::app()->getRequest()->getQuery();

        $_url_param = Mage::app()->getRequest()->getParam('cat');
        
        $_filter = array();
        if (preg_match('/^[0-9,]+$/', $_url_param)) {
            $_filter = explode(',', $_url_param);
        } elseif ((int) $_url_param > 0) {
            $_filter[] = $_url_param;
        }
        //$_value = $this->getValue();
        if (in_array($_value, $_filter)) {
            array_splice($_filter, array_search($_value, $_filter), 1);
        } else {
			//Si el nivel es 1 eliminaremos todas las categorias seleccionadas
			
			if ($level==1):
				$_filter=array();	
			else:
			//Si es nivel 2 o superior eliminamos el padre si existe y los hijos y nietos si los hubiese
				$padre=$category->getParentCategory()->getId();
				$hijos=$category->getChildrenCategories();
				$hijosynietosId = array();
				foreach ($hijos as $hijo) {
					if ($hijo->getIsActive()) {
						$hijosynietosId[] = $hijo->getId();
						$nietos=$hijo->getChildrenCategories();
						foreach ($nietos as $nieto){
							if ($nieto->getIsActive()){
								$hijosynietosId[] =$nieto->getId();
							}
						}
					}
				}
				
				//Eliminamos a los hijos
				$_filter=array_diff($_filter,$hijosynietosId);
				//Eliminamos al padre
				 if (in_array($padre, $_filter)) {
		            array_splice($_filter, array_search($padre, $_filter), 1);
        		}

			endif;
			$_filter[] = $_value;
        }
		
        $_filter = array_unique($_filter);
        $_filter = join(",", $_filter);
        $_query['cat'] = $_filter;

        Mage::helper("ajaxlist")->removeAjaxParameters($_query);
        
        $_query[Mage::getBlockSingleton('page/html_pager')->getPageVarName()] = null;

        if (Mage::app()->getRequest()->getControllerName() == "result" && Mage::app()->getRequest()->getModuleName() == "catalogsearch") {
            $_url = Mage::getUrl('*/*', array('_current' => true, '_use_rewrite' => true, '_query' => $_query));
        } else {
            $_url = Mage::getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true, '_query' => $_query));
        }
        return $_url; // Mage::helper('core')->escapeUrl($_url);
    }

 function isItemActive($_value) {
        $_url_param = Mage::app()->getRequest()->getParam('cat');

        $_filter = array();
        if (preg_match('/^[0-9,]+$/', $_url_param)) {
            $_filter = explode(',', $_url_param);
        } elseif ((int) $_url_param > 0) {
            $_filter[] = $_url_param;
        }
        if (in_array($_value, $_filter)) {
            return true;
        } else {
            return false;
        }
    }

	function categoryProductCount($category){
		$count=$category->getProductCount();
		foreach ($category->getChildrenCategories() as $item){
			$childCategory=Mage::getModel("catalog/category")->load($item->getId());
			$count+= $this->categoryProductCount($childCategory);
		}
		return $count;
	}
/*
    function isItemActive($_value,$category) {
        $_url_param = Mage::app()->getRequest()->getParam('cat');
		
		$_filter = array();
			if (preg_match('/^[0-9,]+$/', $_url_param)) {
				$_filter = explode(',', $_url_param);
			} elseif ((int) $_url_param > 0) {
				$_filter[] = $_url_param;
			}
		
		if (in_array($_value, $_filter)) {
			 return true;
		}else{
			$hijos=$category->getChildrenCategory();
			foreach ($hijos as $hijo):
				if ($hijo->getIsActive()):
					if (in_array($hijo->getId(),$_filter)):
						return true;
					else:	
						$nietos=$hijo->getChildrenCategories();
						foreach ($nietos as $nieto):
							if ($nieto->getIsActive()):
								if (in_array($nieto->getId(),$_filter)):
									return true;
								else:
									$biznietos=$nieto->getChildrenCategories();
									foreach ($biznietos as $biznieto):
										if($biznieto->getIsActive()):
											if (in_array($nieto->getId(),$_filter)):
												return true;
											endif;
										endif;
									endforeach;
								endif;
							endif;
						endforeach;
					endif;
				endif;
			endforeach;
		}
			
         return false;
    }
*/
}
