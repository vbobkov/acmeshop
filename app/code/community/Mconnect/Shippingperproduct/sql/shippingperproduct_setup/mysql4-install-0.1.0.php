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
$installer = $this;

$installer->startSetup();

$installer->run("-- DROP TABLE IF EXISTS {$this->getTable('shippingperproduct')};
CREATE TABLE {$this->getTable('shippingperproduct')} (
  `shippingperproduct_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`shippingperproduct_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, "shipping_rate",  array(
    "type"     => "varchar",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Flat Rate Shipping",
    "input"    => "text",
    "class"    => "",
    "source"   => "eav/entity_attribute_source_boolean",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,	
    "visible_on_front"  => false,
    "used_in_product_listing" => true,
    "unique"     => false));

$installer->endSetup(); 