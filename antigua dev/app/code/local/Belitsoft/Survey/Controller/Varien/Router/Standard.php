<?php
/**
 * Survey for Magento
 *
 * @category   Belitsoft
 * @package	Belitsoft_Survey
 * @author	 Belitsoft <bits@belitsoft.com>
 */

class Belitsoft_Survey_Controller_Varien_Router_Standard extends Mage_Core_Controller_Varien_Router_Standard
{
	
	public function match(Zend_Controller_Request_Http $request)
	{
		$path = explode('/', trim($request->getPathInfo(), '/'));
		
		// If path doesn't match your module requirements
		$path_count = count($path);
//		echo "<pre>"; var_dump($path, $path_count); die;
		if($path_count < 1 || ($path[0] != Mage::helper('belitsoft_survey')->getUrlPrefix())) {
			return parent::match($request);
		}

		// Define initial values for controller initialization
		$module = Belitsoft_Survey_Helper_Data::URL_PREFIX;
		
		$request->setRouteName($module);
		$request->setModuleName($module);

		$realModule = 'Belitsoft_Survey';
		$nat = false;
		
		$action = $request->getActionName();
		if($path_count == 1) {
			$controller = 'index';
		} else if($path_count == 2) {
			if($path[1] == 'manage') {
				$controller = 'manage';
			} else {
				$controller = 'category';
			}
		} else {
			if(($path[1] == 'category') && ($path[2] == 'view')
				&& !empty($path[3]) && ($path[3] == 'cid')
				&& !empty($path[4]) && ($id = intval($path[4])))
			{
				$controller	= $path[1];
				$action		= $action ? $action : $path[2];
				$request->setParam($path[3], $id);
				
				$nat = true;
					
			} else if(($path[1] == 'survey')
				&& ($path[2] == 'view' || $path[2] == 'edit' || $path[2] == 'question' || $path[2] == 'finish')
				&& !empty($path[3]) && ($path[3] == 'aid' || $path[3] == 'sid')
				&& !empty($path[4]) && ($id = intval($path[4])))
			{
				$controller	= $path[1];
				$action		= $action ? $action : $path[2];
				$request->setParam($path[3], $id);
				
				for ($i = 5, $l = sizeof($path); $i < $l; $i += 2) {
					$request->setParam($path[$i], isset($path[$i+1]) ? intval($path[$i+1]) : '');
				}				
				
				$nat = true;
					
			} else if($path[1] == 'manage') {
				$controller	= $path[1];
				$action		= 'index';
				
				for ($i = 3, $l = sizeof($path); $i < $l; $i += 2) {
					$request->setParam($path[$i], isset($path[$i+1]) ? intval($path[$i+1]) : '');
				}
								
				$nat = true;
				
			} else {
				$controller = 'survey';
			}
			
			unset($id);
		}
		
		if(!$nat && !$action) {
			if($controller == 'index' || $controller == 'manage') {
				$action = 'index';
			} else if($controller == 'category') {
				$action = 'view';
			} else {
				$survey_id = Mage::getResourceModel('belitsoft_survey/survey')->getSurveyByUrlKey($path[2]);
				$request->setParam('sid', $survey_id);
				
				if(!$request->getActionName()) {
					$action = empty($path[3]) ? 'view' : $path[3];
				} else {
					$action = $request->getActionName();
				}

				if(!empty($path[4]) && ($id = intval($path[4]))) {
					if($action == 'question') {
						$request->setParam('sid', $id);
					} else {
						$request->setParam('aid', $id);
					}
				}
			}
		}

		$controllerClassName = $this->_validateControllerClassName(
			$realModule,
			$controller
		);

		// If controller was not found
		if(!$controllerClassName) {
			return parent::match($request);
		}
		// Instantiate controller class
		$controllerInstance = Mage::getControllerInstance(
			$controllerClassName,
			$request,
			$this->getFront()->getResponse()
		);
		// If action is not found
		if(!$controllerInstance->hasAction($action)) {
			return parent::match($request);
		}

		// Set request data
		$request->setControllerName($controller);
		$request->setActionName($action);
		$request->setControllerModule($realModule);
		
		if(!$nat && $controller != 'index') {
			$category_id = Mage::getResourceModel('belitsoft_survey/category')->getSurveyCategoryByUrlKey($path[1]);
			$request->setParam('cid', $category_id);
		}

		// dispatch action
		$request->setDispatched(true);
		$controllerInstance->dispatch($action);

		// Indicate that our route was dispatched
		return true;
	}
}
