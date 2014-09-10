<?php
require('app/Mage.php');
Mage::app();
$amount = 0;
$model = Mage::getModel('catalog/product');
$products = $model->getCollection();
foreach ($products as $product) {
    $model->load($product->getId());
    $product->setUrlKey($model->getName())->save();
    set_time_limit();
    $amount++;
}
