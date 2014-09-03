<?php
/* Vic Bobkov
* 2014-09-03
* /app/code/community/ACMElectronics/CatalogSearch/Model/Layer.php
*/

class ACMElectronics_CatalogSearch_Model_Layer extends Mage_CatalogSearch_Model_Layer
{
    public function getProductCollection()
    {
		return new Mage_Catalog_Model_Resource_Product_Collection();

        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = Mage::getResourceModel('catalogsearch/fulltext_collection');
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }
        return $collection;
    }

    public function prepareProductCollection($collection)
    {
		return null;

        $collection
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addSearchFilter(Mage::helper('catalogsearch')->getQuery()->getQueryText())
            ->setStore(Mage::app()->getStore())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite();

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);

        return $this;
    }
}
