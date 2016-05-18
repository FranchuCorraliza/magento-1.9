<?php
/**
 * 
 *
 *
 * Author@ Nimila Jose
 * Company@ Echidna Software Pvt Ltd
 * Purpose@ Extended Pricing Sheet
 * 
 *
 */
 
class Echidna_Extendedpricing_Adminhtml_ExtendedpricingController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
	{
			$this->loadLayout()->_setActiveMenu("extendedpricing/extendedpricing")->_addBreadcrumb(Mage::helper("adminhtml")->__("Extendedpricing  Manager"),Mage::helper("adminhtml")->__("Extendedpricing Manager"));
			return $this;
	}
	
	public function indexAction() 
	{
			$this->_title($this->__("Extendedpricing"));
			$this->_title($this->__("Manager Extendedpricing"));

			$this->_initAction();
			$this->renderLayout();
	}
	
	public function importAction()
	{        
		$row = 1;
		
		try 
		{
			if (($handle = fopen("var/import/pricebook.csv", "r")) !== FALSE) 
			{   
				while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) 
				{
					$num = count($data);  
                                 
					if($row > 1)
					{             
                                            $pricebookOptionId = Mage::getResourceModel('customer/customer')
                                                                                ->getAttribute('pricebook')
                                                                                ->getSource()
                                                                                ->getOptionId($data[2]);
					    $priceregionOptionId = Mage::getResourceModel('customer/customer')
                                                                                ->getAttribute('priceregion')
                                                                                ->getSource()
                                                                                ->getOptionId($data[1]); 
                                            if(!empty($pricebookOptionId)&&!empty($priceregionOptionId))
                                            {
						$model = Mage::getModel('extendedpricing/extendedpricing')->getCollection()						
						->addFieldToFilter(array('sku'),array(array('like'=>$data[0])))
						->addFieldToFilter(array('priceregion'),array(array('like'=>$priceregionOptionId)))
						->addFieldToFilter(array('subpriceregion'),array(array('like'=>$pricebookOptionId)));
						
						                                            						 
					     $item = $model->getData();	
                                             
						if($item)
						{            

							foreach($item as $v)
							{ 
								$id = $v['id']; 
                                                                $data = array('sku'=>$data[0],'priceregion'=>$priceregionOptionId,'subpriceregion'=>$pricebookOptionId,'price'=>$data[3]);  
                                                                
                                                                $model = Mage::getModel('extendedpricing/extendedpricing')->load($id)->addData($data); 
								try 
								{
									$model->setId($id)->save(); 
								} 
								catch (Exception $e)
								{
									echo $e->getMessage(); 
								}
							}
						}
						else
						{   
                                                        $data = array('sku'=>$data[0],'priceregion'=>$priceregionOptionId,'subpriceregion'=>$pricebookOptionId,'price'=>$data[3]); 
						
                                                        $model = Mage::getModel('extendedpricing/extendedpricing')->setData($data);
							try 
							{        
								
                                                            $insertId = $model->save()->getId();
							} 
							catch (Exception $e)
							{
								echo $e->getMessage(); 
							}
						}
                                            }  else  {
                                                          Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__("Csv data is invalid for sku ".$data[0]));
                                                     }					
					}
					$row++; 
				}
                                if(Mage::getSingleton('adminhtml/session')->getMessages()->count() == 0){
                                
				   Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully inserted"));	
                                }

                                }
			else
			{
				Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__("Please upload the file for mass item(s) insertion"));	
			}
			
		} 
		catch (Exception $e)
		{
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
		}
		$this->_redirect('*/*/');
	}
	
	public function editAction()
	{			
			$this->_title($this->__("Extended Pricing Sheet"));
			$this->_title($this->__("Extended Pricing Sheet"));
			$this->_title($this->__("Edit Item"));
			
			$id = $this->getRequest()->getParam("id");
			$model = Mage::getModel("extendedpricing/extendedpricing")->load($id);
			if ($model->getId()) 
			{
				Mage::register("extendedpricing_data", $model);
				$this->loadLayout();
				$this->_setActiveMenu("extendedpricing/extendedpricing");
				$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Retailer Manager"), Mage::helper("adminhtml")->__("Retailer Manager"));
				$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Retailer Description"), Mage::helper("adminhtml")->__("Retailer Description"));
				$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
				$this->_addContent($this->getLayout()->createBlock("extendedpricing/adminhtml_extendedpricing_edit"))->_addLeft($this->getLayout()->createBlock("extendedpricing/adminhtml_extendedpricing_edit_tabs"));
				$this->renderLayout();
				
			} 
			else 
			{
				Mage::getSingleton("adminhtml/session")->addError(Mage::helper("retailers")->__("Item does not exist."));
				$this->_redirect("*/*/");
			}
	}
	
	
	public function saveAction()
	{

		$post_data=$this->getRequest()->getPost();


			if ($post_data) 
			{
				try 
				{
				       $sku = $post_data['sku'];
                                       $id = Mage::getModel('catalog/product')->getIdBySku($sku);

                                           //checking sku is present or not in database.
                                           if(!empty($id))
                                           {  
                                                $model = Mage::getModel("extendedpricing/extendedpricing")
					            ->addData($post_data)
					            ->setId($this->getRequest()->getParam("id"))
					            ->save();
                                                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Extendedpricing Sheet was successfully saved"));
					        Mage::getSingleton("adminhtml/session")->setRetailerData(false);
                                             
                                           }  else {
                                                Mage::getSingleton("adminhtml/session")->addError("product with sku ".$sku." not present"); 
                                           }
                                       
                                        
					

					if ($this->getRequest()->getParam("back")) 
					{
						$this->_redirect("*/*/edit", array("id" => $model->getId()));
						return;
					}
					
					$this->_redirect("*/*/");
					return;
				} 
				catch (Exception $e) {
					Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
					Mage::getSingleton("adminhtml/session")->setRetailerData($this->getRequest()->getPost());
					$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
				return;
				}

			}			
			
				
			
			$this->_redirect("*/*/");
	}
	
	public function deleteAction()
	{
		if( $this->getRequest()->getParam("id") > 0 ) 
		{
			try 
			{
				$model = Mage::getModel("extendedpricing/extendedpricing");
				$model->setId($this->getRequest()->getParam("id"))->delete();				
				
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
				$this->_redirect("*/*/");
			} 
			catch (Exception $e) 
			{
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
				$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
			}
		}
		$this->_redirect("*/*/");
	}

	
	public function massRemoveAction()
	{
		try 
		{
			$ids = $this->getRequest()->getPost('ids', array());
			foreach ($ids as $id) 
			{
				  $model = Mage::getModel("extendedpricing/extendedpricing");
				  $model->setId($id)->delete();
			}
			Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
		}
		catch (Exception $e) 
		{
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
		}
		$this->_redirect('*/*/');
	}
		
	/**
	 * Export order grid to CSV format
	 */
	public function exportCsvAction()
	{
		$fileName   = 'extendedpricing.csv';
		$grid       = $this->getLayout()->createBlock('extendedpricing/adminhtml_extendedpricing_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
	} 
	/**
	 *  Export order grid to Excel XML format
	 */
	public function exportExcelAction()
	{
		$fileName   = 'extendedpricing.xml';
		$grid       = $this->getLayout()->createBlock('extendedpricing/adminhtml_extendedpricing_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
	}
}
