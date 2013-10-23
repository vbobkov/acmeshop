<?php
/**
 * M-Connect Solutions.
 *
 * NOTICE OF LICENSE
 *

 *
 * @category   Catalog
 * @package    Mconnect_Shippingperproduct
 * @author     M-Connect Solutions (http://www.mconnectsolutions.com, http://www.mconnectmedia.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mconnect_Shippingperproduct_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/shippingperproduct?id=15 
    	 *  or
    	 * http://site.com/shippingperproduct/id/15 	
    	 */
    	/* 
		$shippingperproduct_id = $this->getRequest()->getParam('id');

  		if($shippingperproduct_id != null && $shippingperproduct_id != '')	{
			$shippingperproduct = Mage::getModel('shippingperproduct/shippingperproduct')->load($shippingperproduct_id)->getData();
		} else {
			$shippingperproduct = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($shippingperproduct == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$shippingperproductTable = $resource->getTableName('shippingperproduct');
			
			$select = $read->select()
			   ->from($shippingperproductTable,array('shippingperproduct_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$shippingperproduct = $read->fetchRow($select);
		}
		Mage::register('shippingperproduct', $shippingperproduct);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}