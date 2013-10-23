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
class Mconnect_Shippingperproduct_Block_Shippingperproduct extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getShippingperproduct()     
     { 
        if (!$this->hasData('shippingperproduct')) {
            $this->setData('shippingperproduct', Mage::registry('shippingperproduct'));
        }
        return $this->getData('shippingperproduct');
        
    }
     
    public function Cartproduct(){
        if(Mage::getStoreConfig('shippingperproduct/mconnectshippingmethod/shipping_enable')){
         
            if(Mage::getStoreConfig('carriers/mconnectshippingmethod/sallowspecific')){
            
                echo Mage::getStoreConfig('carriers/mconnectshippingmethod/specificcountry');
            }
         $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
         $quote = Mage::getSingleton('checkout/session')->getQuote(); 
         echo 'hello'.$quote->getShippingAddress(); 
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
            echo "<br />";
             echo $Sipping_Rate_Attr.'hardik rate <br />';
             if(($Sipping_Rate_Attr == '' || $Sipping_Rate_Attr == NULL) && Mage::getStoreConfig('shippingperproduct/mconnectshippingmethod/shipping_default_value_enable')){
                 
                    $Sipping_Rate_Attr = Mage::getStoreConfig('shippingperproduct/mconnectshippingmethod/shipping_default_value');
             } 
            echo 'ID: '.$item->getId().'<br />';
            echo 'productID: '.$item->getProductId().'<br />';
            echo 'Name: '.$item->getName().'<br />';
            echo 'Sku: '.$item->getSku().'<br />';
            echo 'Quantity: '.$item->getQty().'<br />';
            echo 'Price: '.$item->getPrice().'<br />';
            echo 'Type: '.$type_id.'<br />';
            echo 'parent ids'.$item->getParentItemId(); echo "<br />";
 
            if($item->getParentItemId() == NULL && $item->getHasChildren()){
                $_quoteitem_qty_rate = 0;
                $_qouteitem_main_qty = $item->getQty();
            }
            else if($item->getParentItemId() == NULL && !$item->getHasChildren()){
                //$_quoteitem_qty_rate = $item->getQty() * $Sipping_Rate_Attr;
                $_quoteitem_qty_rate = $Sipping_Rate_Attr;
                $_qouteitem_main_qty = $item->getQty();
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
        echo $multiplication_qty;
         }
      }
    }
}