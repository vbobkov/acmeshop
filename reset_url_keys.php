<?php
require('app/Mage.php');
Mage::app();

$w = Mage::getSingleton('core/resource')->getConnection('core_write');
$w->query("UPDATE catalog_product_entity_varchar SET value = '' WHERE attribute_id IN(97,98)");


/*
function generateURLKey($product_title) {
	return strtolower(
		preg_replace('/-{2,}/', '-',
			preg_replace('/^-/', '',
				preg_replace('/-$/', '',
					preg_replace('/[^0-9A-Za-z-]/', '-', $product_title)
				)
			)
		)
	);
}

$amount = 0;
$total = 0;
$model = Mage::getModel('catalog/product');
echo "fetching the entire product collection\n";
$products = $model->getCollection()->addAttributeToSelect('url_key')->addAttributeToSelect('name');
echo "fetched " . $products->count() . " products\n";
foreach ($products as $product) {
	$total++;
	if($product->getUrlKey() != generateURLKey($product->getName())) {
		$prev_key = $product->getUrlKey();
		// $model->load($product->getId());
		// $product->setUrlKey($model->getName())->save();
		$product->setUrlKey($product->getName())->save();
		// set_time_limit();
		$amount++;
		echo "[" . $product->getName() . "] " . $prev_key . " set to " . $product->getUrlKey() . " [" . $amount . "/" . $total . "]\n";
	}
}
echo "[" . $amount . "/" . $total . "]\n";
*/
