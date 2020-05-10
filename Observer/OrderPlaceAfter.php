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
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Qinvoice\Connect\Model\Call;
use Qinvoice\Connect\Model\RequestFactory;

class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    protected $_call;

    /**
     * @var \Qinvoice\Connect\Service\Communicator
     */
    private $communicator;
    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * OrderPlaceAfter constructor.
     * @param \Qinvoice\Connect\Service\Communicator $communicator
     * @param ScopeConfigInterface $scopeConfig
     * @param Call $call
     */
    public function __construct(
        \Qinvoice\Connect\Service\Communicator $communicator,
        ScopeConfigInterface $scopeConfig,
        RequestFactory $requestFactory,
        Call $call
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_call = $call;
        $this->communicator = $communicator;
        $this->requestFactory = $requestFactory;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        // GETTING TRIGGER SETTING
        $order_triggers = explode(
            ",",
            $this->scopeConfig->getValue(
                'invoice_options/invoice/invoice_trigger_order',
                ScopeInterface::SCOPE_STORE
            )
        );
        $payment = $order->getPayment();

        if (in_array($payment->getMethod(), $order_triggers)) {
            $document = $this->requestFactory->createDocumentFromOrder($order, false);
            $this->communicator->sendRequest($document);
        }
    }
}
