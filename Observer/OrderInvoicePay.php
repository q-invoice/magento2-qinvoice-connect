<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Qinvoice\Connect\Model\RequestFactory;

class OrderInvoicePay implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Qinvoice\Connect\Service\Communicator
     */
    private $communicator;
    /**
     * @var RequestFactory
     */
    private $requestFactory;
    private \Psr\Log\LoggerInterface $logger;

    /**
     * OrderInvoicePay constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param \Qinvoice\Connect\Service\Communicator $communicator ,
     * @param RequestFactory $requestFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Qinvoice\Connect\Service\Communicator $communicator,
        RequestFactory $requestFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->communicator = $communicator;
        $this->requestFactory = $requestFactory;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {

        $invoice = $observer->getEvent()->getInvoice();

        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $invoice->getOrder();

        $this->logger->log('debug', sprintf('Processing payment for order %s in store %s', $order->getIncrementId(), $order->getStoreName()));

        // GETTING TRIGGER SETTING
        $invoice_triggers = explode(
            ",",
            $this->scopeConfig->getValue(
                'invoice_options/invoice/invoice_trigger_payment',
                ScopeInterface::SCOPE_STORE
            )
        );



        $payment = $order->getPayment();

        if (in_array($payment->getMethod(), $invoice_triggers)) {
            $document = $this->requestFactory->createDocumentFromOrder($order, true);
            $this->communicator->sendRequest($document);
        }
    }
}
