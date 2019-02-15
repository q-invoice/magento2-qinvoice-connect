<?php

/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderPlaceAfter implements ObserverInterface
{
    protected $_collectionFactory;
    protected $_productFactory;
    protected $_storeManager;
    protected $_scopeConfig;
    protected $_call;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Qinvoice\Connect\Model\Call $call
    )
    {
        $this->_collectionFactory = $collectionFactory;
        $this->_productFactory = $productFactory;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_call = $call;

    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $this->_call->sendOnOrderPlace($order);
    }

}
