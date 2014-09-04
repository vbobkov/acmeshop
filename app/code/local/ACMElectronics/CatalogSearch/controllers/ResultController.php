<?php
/* Vic Bobkov
* 2014-06-16
* /app/code/community/ACMElectronics/CatalogSearch/controllers/ResultController.php
*/

include(Mage::getBaseDir()."/app/code/core/Mage/CatalogSearch/controllers/ResultController.php");
class ACMElectronics_CatalogSearch_ResultController extends Mage_CatalogSearch_ResultController {
    public function indexAction() {
		if(isset($_GET['fetch_details']) && $_GET['fetch_details'] == 1) {
			/*
			This little gem of a function solves a certain AJAX latency problem:
			http://stackoverflow.com/questions/941889/browser-waits-for-ajax-call-to-complete-even-after-abort-has-been-called-jquery
			http://php.net/manual/en/function.session-write-close.php#96982
			*/
			session_write_close();

			$w = Mage::getSingleton('core/resource')->getConnection('core_write');

			$entity_ids = array();
			foreach(
				$w->query(
					"SELECT entity_id FROM catalog_product_entity_varchar WHERE attribute_id = 71 AND value LIKE '%" . $_GET['q'] . "%'"
				)->fetchAll(PDO::FETCH_ASSOC)
				as
				$row
			) {
				$entity_ids[] = $row['entity_id'];
			}
			$entity_ids = implode(',', $entity_ids);

			$get_products_descriptions = $w->query(
				"SELECT entity_id,attribute_id,value
				FROM catalog_product_entity_text
				WHERE attribute_id IN(72,73) AND entity_id IN(" . $entity_ids . ")"
			)->fetchAll(PDO::FETCH_ASSOC);
			$get_products_images = $w->query(
				"SELECT entity_id,attribute_id,value
				FROM catalog_product_entity_varchar
				WHERE attribute_id = 85 AND entity_id IN(" . $entity_ids . ")"
			)->fetchAll(PDO::FETCH_ASSOC);

			$products_details = array(
				'descriptions' => array(),
				'images' => array()
			);
			foreach($get_products_descriptions as $description) {
				// $products_details['descriptions'][$description['entity_id']][$description['attribute_id']] = $description['value'];
				$products_details['descriptions'][$description['entity_id']][] = $description['value'];
			}
			foreach($get_products_images as $image) {
				$products_details['images'][$image['entity_id']] = $image['value'];
			}
			echo json_encode($products_details);
			return null;
		}
		else if(!isset($_GET['q'])) {
			$this->loadLayout();
			// $this->_initLayoutMessages('catalog/session');
			// $this->_initLayoutMessages('checkout/session');
			$this->renderLayout();
			return null;
		}
		else {
			// $this->addActionLayoutHandles();
			// $this->loadLayoutUpdates();
			// $this->generateLayoutXml();
			// $this->generateLayoutBlocks();
			// $this->_isLayoutLoaded = true;

			$this->loadLayout();
			$this->renderLayout();
			return null;
		}
	}
}
