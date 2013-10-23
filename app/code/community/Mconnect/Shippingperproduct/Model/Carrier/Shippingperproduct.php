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
class Mconnect_Shippingperproduct_Model_Carrier_Shippingperproduct 
extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'shippingperproduct';
    
    public function collectRates(Mage_Shipping_Model_Rate_Request $request){
       //echo Mage::getStoreConfig('carriers/mconnectshippingmethod/active');
        if(Mage::getStoreConfig('carriers/shippingperproduct/active')){
         
         $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
         
         if(count($items)>0){
         $_quoteitem_qty_rate = 0;
         $multiplication_qty = 0;
         $_qouteitem_main_qty = 1;
         $_qouteitemCnt = 1;
        //echo "<pre>";         print_r(get_class_methods(Mage::getSingleton('checkout/session')->getQuote()));
         foreach($items as $item) {
             $type_id = Mage::getModel('catalog/product')->load($item->getProductId())->getTypeId();
         
             if($item->getParentItemId() == NULL && $type_id != 'downloadable'){
                $multiplication_qty += $_qouteitem_main_qty * $_quoteitem_qty_rate;
             }
             if($item->getIsVirtual() || $type_id == 'virtual' || $type_id == 'downloadable')
             {
                 $_qouteitemCnt++;
                 continue;
                 
             }
            $Sipping_Rate_Attr = Mage::getModel('catalog/product')
                    ->load($item->getProductId())
                    ->getShippingRate();
             
             if(($Sipping_Rate_Attr == '' || $Sipping_Rate_Attr == NULL) && Mage::getStoreConfig('carriers/shippingperproduct/shipping_default_value_enable')){
                 
                    $Sipping_Rate_Attr = Mage::getStoreConfig('carriers/shippingperproduct/default_shipping_cost');
             } 
 
            if($item->getParentItemId() == NULL && $item->getHasChildren()){
                $_quoteitem_qty_rate = 0;
                $_qouteitem_main_qty = $item->getQty();
            }
            else if($item->getParentItemId() == NULL && !$item->getHasChildren()){
                //$_quoteitem_qty_rate = $item->getQty() * $Sipping_Rate_Attr;
                $_qouteitem_main_qty = $item->getQty();
                $_quoteitem_qty_rate = $Sipping_Rate_Attr;                
            }
            else{
                $_quoteitem_qty_rate += $item->getQty() * $Sipping_Rate_Attr;
                //echo "harfdfl <br />".$item->getQty() .'* '.$Sipping_Rate_Attr.'<br />';
            }
            
            if($_qouteitemCnt == count($items)){
                $multiplication_qty += $_qouteitem_main_qty * $_quoteitem_qty_rate;  
            }
            $_qouteitemCnt++;
        }
        $result = Mage::getModel('shipping/rate_result');
        
        if ($this->getConfigData('showmethod')) {
		$error = Mage::getModel('shipping/rate_result_error');
		$error->setCarrier('shippingperproduct');
		$error->setCarrierTitle($this->getConfigData('title'));
		//$error->setErrorMessage($errorTitle);
		$error->setErrorMessage($this->getConfigData('specificerrmsg'));
		$result->append($error);
                return $result;
	}else{
        
        $method = Mage::getModel('shipping/rate_result_method');
			
	$method->setCarrier('shippingperproduct');
	$method->setCarrierTitle($this->getConfigData('name'));
			
	$method->setMethod('shippingperproduct');
	$method->setMethodTitle($this->getConfigData('title'));
        
        $method->setPrice($multiplication_qty);
	$method->setCost($multiplication_qty);
			
	$result->append($method);
        return $result;
         } }
       }
    }
    
    public function getAllowedMethods()
    {
        return array('shippingperproduct'=>$this->getConfigData('name'));
    }

}
