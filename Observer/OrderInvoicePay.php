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
use Qinvoice\Connect\Model\Call;
use Qinvoice\Connect\Model\RequestFactory;

class OrderInvoicePay implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

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
     * OrderInvoicePay constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param \Qinvoice\Connect\Service\Communicator $communicator,
     * @param Call $call
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Qinvoice\Connect\Service\Communicator $communicator,
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
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();

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
            //$this->_connect->createInvoiceForQinvoice($order, true);
        }
    }
}
