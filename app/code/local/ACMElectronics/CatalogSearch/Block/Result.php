<?php
class ACMElectronics_CatalogSearch_Block_Result extends Mage_CatalogSearch_Block_Result {
	protected function _getProductCollection() {
		return null;
		if (is_null($this->_productCollection)) {
			$this->_productCollection = $this->getListBlock()->getLoadedProductCollection();
			// $this->_productCollection = Mage::getModel('catalogsearch/fulltext')->getCollection();
			// $this->_productCollection = new Mage_CatalogSearch_Model_Resource_Query_Collection();
		}

		return $this->_productCollection;
	}

	public function getResultCount() {
		return 1;
	}
}
