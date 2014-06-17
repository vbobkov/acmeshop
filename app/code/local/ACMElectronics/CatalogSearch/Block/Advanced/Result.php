<?php
class ACMElectronics_CatalogSearch_Block_Advanced_Result extends Mage_CatalogSearch_Block_Advanced_Result {
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
			// $this->_productCollection = $this->getListBlock()->getLoadedProductCollection();
			$this->_productCollection = Mage::getModel('catalogsearch/fulltext')->getCollection();
        }

        return $this->_productCollection;
    }
}
