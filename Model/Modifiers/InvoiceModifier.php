<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Modifiers;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\ScopeInterface;
use Qinvoice\Connect\Api\ModifierInterface;
use Qinvoice\Connect\Model\Document;

class InvoiceModifier implements ModifierInterface
{
    use AddCdata;
    const PARENT_NODE = "invoice";
    const INVOICE_REMARK_CONFIG_KEY =  'invoice_options/invoice/invoice_remark';
    const INVOICE_PAID_REMARK_CONFIG_KEY =  'invoice_options/invoice/paid_remark';
    const INVOICE_LAYOUT_CODE =  'invoice_options/invoice/layout_code';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * LoginModifier constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }
    /**
     * @param Document $document
     * @param OrderInterface $order
     * @param bool $isPaid
     * @return Document
     */
    public function modify(Document $document, OrderInterface $order, $isPaid = false)
    {
        $invoice = $document->getItem(self::PARENT_NODE);
        $invoice['reference'] = $this->addCDATA($order->getIncrementId());
        $invoice['date'] = $this->addCDATA($order->getCreatedAt());
        $invoice['recurring'] = $this->addCDATA("none");
        $invoice['remark'] = $this->addCDATA($this->getRemark($order, $isPaid));
        $invoice['layout'] = $this->addCDATA($this->getLayout());
        $invoice['paid'] = $this->getPaid($order, $isPaid);

        return $document->addItem(self::PARENT_NODE, $invoice);
    }

    private function getRemark($order, $isPaid)
    {
        $document_remark = $this->scopeConfig->getValue(
            self::INVOICE_REMARK_CONFIG_KEY,
            ScopeInterface::SCOPE_STORE
        );

        $document_remark = str_replace('{order_id}', $order->getIncrementId(), $document_remark);

        $paid_remark = '';
        if ($isPaid) {
            $paid_remark = $this->scopeConfig->getValue(
                self::INVOICE_PAID_REMARK_CONFIG_KEY,
                ScopeInterface::SCOPE_STORE
            );
        }

        return $document_remark . "\n" . $paid_remark;
    }

    private function getLayout()
    {
        $layout_code = $this->scopeConfig->getValue(
            self::INVOICE_LAYOUT_CODE,
            ScopeInterface::SCOPE_STORE
        );

        return isset($layout_code['default']) ? $layout_code['default'] : '';
    }

    private function getPaid($order, $isPaid)
    {
        $payment = $order->getPayment();
        return [
            '@value' => $this->addCDATA($isPaid ? 1 : 0),
            '@attributes' => [
                'method' => $payment->getMethod(),
                'label' => $payment->getMethodInstance()->getTitle(),
            ],
        ];
    }
}
