<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Observer;

use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Qinvoice\Connect\Model\Call;

class OrderPlaceAfter implements ObserverInterface
{
    protected $_collectionFactory;
    protected $_productFactory;
    protected $_storeManager;
    protected $_scopeConfig;
    protected $_call;

    public function __construct(
        CollectionFactory $collectionFactory,
        ProductFactory $productFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Call $call
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_productFactory = $productFactory;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_call = $call;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        $this->_call->sendOnOrderPlace($order);
    }
}
