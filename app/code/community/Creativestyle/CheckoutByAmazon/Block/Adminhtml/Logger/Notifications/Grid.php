<?php

/**
 * This file is part of The Official Amazon Payments Magento Extension
 * (c) creativestyle GmbH <amazon@creativestyle.de>
 * All rights reserved
 *
 * Reuse or modification of this source code is not allowed
 * without written permission from creativestyle GmbH
 *
 * @category   Creativestyle
 * @package    Creativestyle_CheckoutByAmazon
 * @copyright  Copyright (c) 2011 - 2013 creativestyle GmbH (http://www.creativestyle.de)
 * @author     Marek Zabrowarny / creativestyle GmbH <amazon@creativestyle.de>
 */
class Creativestyle_CheckoutByAmazon_Block_Adminhtml_Logger_Notifications_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('amazon_logger_notifications_grid');
        $this->setDefaultSort('creation_time');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('checkoutbyamazon/log_iopn')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('creation_time', array(
            'header'        => Mage::helper('checkoutbyamazon')->__('Date'),
            'index'         => 'creation_time',
            'type'          => 'datetime',
            'width'         => '150px'
        ));

        $this->addColumn('uuid', array(
            'header'        => Mage::helper('checkoutbyamazon')->__('UUID'),
            'index'         => 'uuid',
            'width'         => '300px'
        ));

        $this->addColumn('notification_type', array(
            'header'        => Mage::helper('checkoutbyamazon')->__('Notification type'),
            'index'         => 'notification_type',
            'type'          => 'options',
            'options'       => Mage::getModel('checkoutbyamazon/lookup_notification_type')->getOptions(),
            'width'         => '200px'
        ));

        $this->addColumn('processing_result', array(
            'header'        => Mage::helper('checkoutbyamazon')->__('Processing result'),
            'index'         => 'processing_result',
            'align'         => 'center',
            'width'         => '100px'
        ));

        $this->addColumn('order_id', array(
            'header'        => Mage::helper('checkoutbyamazon')->__('Assigned orders'),
            'index'         => 'order_id',
            'renderer'      => 'checkoutbyamazon/adminhtml_logger_grid_renderer_order',
            'filter_index'  => 'order_increment_id',
            'sortable'      => false
        ));

//        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action', array(
                'header'    => Mage::helper('checkoutbyamazon')->__('Action'),
                'type'      => 'action',
                'align'     => 'center',
                'width'     => '50px',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('checkoutbyamazon')->__('View'),
                        'url'     => array('base' => '*/*/view'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
            ));
//        }
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
//        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/*/view', array('id' => $row->getId()));
//        }
        return false;
    }

}
