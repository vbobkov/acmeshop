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
class Mconnect_Shippingperproduct_Model_Mysql4_Shippingperproduct extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the shippingperproduct_id refers to the key field in your database table.
        $this->_init('shippingperproduct/shippingperproduct', 'shippingperproduct_id');
    }
}