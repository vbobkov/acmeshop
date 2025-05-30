<?php
/* Vic Bobkov
* 2014-06-16
* /app/code/community/ACMElectronics/CatalogSearch/Model/Resource/Fulltext/Collection.php
*/

class ACMElectronics_CatalogSearch_Model_Resource_Fulltext_Collection extends Mage_CatalogSearch_Model_Resource_Fulltext_Collection {
    public function getAllIDsAndTitles($limit = null, $offset = null) {
        $pSelect = $this->_getClearSelect();
        $pSelect->columns('e.' . $this->getEntity()->getIdFieldName());
        $pSelect->limit($limit, $offset);
        $pSelect->resetJoinLeft();

		// if($limit != null && $limit > 0) {
			// $limit_clause = ' LIMIT ' . $limit;
			// if($offset != null && $offset > 0) {
				// $limit_clause .= ' OFFSET ' . $offset;
			// }
		// }
		// else {
			// $limit_clause = '';
		// }
		$new_query = str_replace("SELECT `e`.`entity_id`", "SELECT `e`.`entity_id`,catalog_product_entity_varchar.value AS name", (string)$pSelect);
/*
SELECT `e`.`entity_id`,catalog_product_entity_varchar.value AS name FROM `catalog_product_entity` AS `e` 
INNER JOIN `catalog_product_entity_varchar` ON catalog_product_entity_varchar.entity_id=e.entity_id  AND catalog_product_entity_varchar.attribute_id = 71 
INNER JOIN `catalogsearch_result` AS `search_result` ON search_result.product_id=e.entity_id AND search_result.query_id='1561' 
INNER JOIN `catalog_product_index_price` AS `price_index` ON price_index.entity_id = e.entity_id AND price_index.website_id = '1' AND price_index.customer_group_id = 0 
INNER JOIN `catalog_category_product_index` AS `cat_index` ON cat_index.product_id=e.entity_id AND cat_index.store_id='1' AND cat_index.visibility IN(3, 4) AND cat_index.category_id = '2';
*/
		// print_r(strpos((string)$pSelect, "INNER JOIN `catalogsearch_result` AS `search_result` ON search_result.product_id=e.entity_id AND search_result.query_id='1561'"));
		// print_r('<br /><br />');
		// print_r($pSelect);
		// print_r('<br /><br />');
		// print_r((string)$pSelect);
		// print_r('<br /><br />');
		// print_r($new_query);
		// print_r('<br /><br />');
		$new_query = substr_replace(
			$new_query,
			"INNER JOIN `catalog_product_entity_varchar` ON catalog_product_entity_varchar.entity_id=e.entity_id  AND catalog_product_entity_varchar.attribute_id = 71\n ",
			strpos($new_query, "INNER JOIN `catalogsearch_result` AS `search_result` ON search_result.product_id=e.entity_id AND search_result.query_id='"),
			0
		);
		// print_r($new_query);
		// print_r('<br /><br />');

		$zend_db_adapter = $this->getConnection();
		// $zend_db_adapter->setFetchMode(Zend_Db::FETCH_NUM);

		// return null;
        // return $zend_db_adapter->fetchCol($pSelect, $this->_bindParams);
		// return $zend_db_adapter->fetchPairs($new_query);
		return $zend_db_adapter->fetchAssoc($new_query);
		// return $zend_db_adapter->fetchAll($new_query);
    }

	// public function getProductCollection() {
		// return null;
	// }

	/*
    protected function _getQuery()
    {
        // return Mage::helper('catalogsearch')->getQuery();
		return null;
    }

    public function addSearchFilter($query)
    {
        return $this;
    }

    public function setOrder($attribute, $dir = 'desc')
    {
        return $this;
    }

    public function setGeneralDefaultQuery()
    {
        return $this;
    }
	*/
}
