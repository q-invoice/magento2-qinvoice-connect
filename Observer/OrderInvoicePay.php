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
use Qinvoice\Connect\Service\DebugService;

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

    private DebugService $debugService;

    /**
     * OrderInvoicePay constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param \Qinvoice\Connect\Service\Communicator $communicator ,
     * @param RequestFactory $requestFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Qinvoice\Connect\Service\Communicator $communicator,
        RequestFactory $requestFactory,
        DebugService $debugService

    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->communicator = $communicator;
        $this->requestFactory = $requestFactory;
        $this->debugService = $debugService;
    }

    public function execute(Observer $observer)
    {

        $invoice = $observer->getEvent()->getInvoice();

        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $invoice->getOrder();

        $this->debugService->debug('Processing payment', array(
                "order_id" => $order->getIncrementId(),
                "store_id" => $order->getStoreName()
            )
        );

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
