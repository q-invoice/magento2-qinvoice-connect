<?php


namespace Qinvoice\Connect\Plugin;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Store\Model\ScopeInterface;
use Qinvoice\Connect\Model\RequestFactory;

class OrderManagmentPlugin
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

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
     */
    public function __construct(
        \Qinvoice\Connect\Service\Communicator $communicator,
        ScopeConfigInterface $scopeConfig,
        RequestFactory $requestFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->communicator = $communicator;
        $this->requestFactory = $requestFactory;
    }


    /**
     * @param \Magento\Sales\Api\OrderManagementInterface $subject
     * @param $result
     * @param OrderInterface $order
     */
    public function afterPlace(\Magento\Sales\Api\OrderManagementInterface $subject, $result, OrderInterface $order)
    {
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

        return $result;
    }
}
