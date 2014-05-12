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
        if(Mage::getStoreConfig('carriers/shippingperproduct/active')) {
			$items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();

			if(count($items)>0) {
				$_quoteitem_qty_rate = 0;
				$multiplication_qty = 0;
				$_qouteitem_main_qty = 1;
				$_qouteitemCnt = 1;
				$_total_weight = 0;
				//echo "<pre>";
				// print_r(get_class_methods(Mage::getSingleton('checkout/session')->getQuote()));
				//echo "</pre>";
				foreach($items as $item) {
					if($item->getWeight() > 0) {
						// $_total_weight += $item->getWeight();
						$_total_weight += $item->getWeight() * $item->getQty();
					}

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
						// $multiplication_qty += $_qouteitem_main_qty * $_quoteitem_qty_rate;
						$_quoteitem_discount = 1;
						if($_qouteitem_main_qty > 4) {
							$_quoteitem_discount = 0.75;
						}
						else if($_qouteitem_main_qty > 3) {
							$_quoteitem_discount = 0.8;
						}
						else if($_qouteitem_main_qty > 2) {
							$_quoteitem_discount = 0.85;
						}
						else if($_qouteitem_main_qty > 1) {
							$_quoteitem_discount = 0.9;
						}
						$multiplication_qty += $_qouteitem_main_qty * $_quoteitem_qty_rate * $_quoteitem_discount;
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
				}
				else {
					/*
					$method = Mage::getModel('shipping/rate_result_method');
					$method->setCarrier($this->_code);
					$method->setCarrierTitle($this->getConfigData('name'));
					$method->setMethod($this->_code);
					$method->setMethodTitle($this->getConfigData('title'));
					$method->setPrice($multiplication_qty);
					$method->setCost($multiplication_qty);
					$result->append($method);
					*/
					$result->append($this->createMethod(
						'shippingperproduct',
						$multiplication_qty,
						'Standard (7-10 business days)'
					));
					$result->append($this->createMethod(
						'shippingperproduct2',
						// $multiplication_qty + ($_qouteitem_main_qty * ($_total_weight * 3.14)) + 19.95,
						$multiplication_qty + ($_total_weight * 3.14) + 19.95,
						'Expedited (3 business days)'
					));
					$result->append($this->createMethod(
						'shippingperproduct3',
						// $multiplication_qty + ($_qouteitem_main_qty * ($_total_weight * 4.19)) + 29.95,
						$multiplication_qty + ($_total_weight * 4.19) + 29.95,
						'Two-Day Shipping (2 business days)'
					));
					$result->append($this->createMethod(
						'shippingperproduct4',
						// $multiplication_qty + ($_qouteitem_main_qty * ($_total_weight * 5.24)) + 49.95,
						$multiplication_qty + ($_total_weight * 5.24) + 49.95,
						'One-Day Shipping (1 business day)'
					));

					return $result;
				}
			}
		}
	}
    
    public function getAllowedMethods() {
        // return array('shippingperproduct'=>$this->getConfigData('name'));
		return array(
			'shippingperproduct' => $this->getConfigData('name'),
			'shippingperproduct2' => 'Expedited (3 business days)',
			'shippingperproduct3' => 'Two-Day Shipping (2 business days)',
			'shippingperproduct4' => 'One-Day Shipping (1 business day)'
		);
    }

	private function createMethod($method_id, $price, $title) {
		$new_method = Mage::getModel('shipping/rate_result_method');
		$new_method->setCarrier($method_id);
		$new_method->setCarrierTitle($title);
		$new_method->setMethod($method_id);
		$new_method->setMethodTitle($title);
		$new_method->setPrice($price);
		$new_method->setCost($price);
		return $new_method;
	}
}
