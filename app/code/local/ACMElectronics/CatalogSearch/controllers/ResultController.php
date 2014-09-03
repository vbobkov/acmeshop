<?php
/* Vic Bobkov
* 2014-06-16
* /app/code/community/ACMElectronics/CatalogSearch/controllers/ResultController.php
*/

include(Mage::getBaseDir()."/app/code/core/Mage/CatalogSearch/controllers/ResultController.php");
class ACMElectronics_CatalogSearch_ResultController extends Mage_CatalogSearch_ResultController {
    public function indexAction() {
		if(!isset($_GET['q'])) {
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
