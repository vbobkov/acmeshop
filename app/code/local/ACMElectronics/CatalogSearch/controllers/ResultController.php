<?php
/* Vic Bobkov
* 2014-06-16
* /app/code/community/ACMElectronics/CatalogSearch/controllers/ResultController.php
*/

include(Mage::getBaseDir()."/app/code/core/Mage/CatalogSearch/controllers/ResultController.php");
class ACMElectronics_CatalogSearch_ResultController extends Mage_CatalogSearch_ResultController {
    public function indexAction() {
		$this->loadLayout();
		// $this->_initLayoutMessages('catalog/session');
		// $this->_initLayoutMessages('checkout/session');
		$this->renderLayout();
		return null;
	}
}
