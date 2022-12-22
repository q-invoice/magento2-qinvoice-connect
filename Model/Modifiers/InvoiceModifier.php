<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Modifiers;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
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
    const INVOICE_REFERENCE_CONFIG_KEY =  'invoice_options/invoice/reference';
    const INVOICE_LAYOUT_CONFIG_CODE =  'invoice_options/invoice/layout_code';
    const INVOICE_ACTION_CONFIG_CODE =  'invoice_options/invoice/invoice_action';
    const INVOICE_SAVE_RELATION_CONFIG_CODE =  'invoice_options/invoice/save_relation';
    const INVOICE_CALCULATION_METHOD_CONFIG_CODE =  'invoice_options/invoice/calculation_method';
    const INVOICE_TAG_CONFIG_CODE =  'invoice_options/invoice/invoice_tag';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * LoginModifier constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ProductMetadataInterface $productMetadata
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productMetadata = $productMetadata;
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
        $invoice['reference'] = $this->addCDATA($this->getReference($order));
        $invoice['date'] = $this->addCDATA($order->getCreatedAt());
        $invoice['recurring'] = $this->addCDATA("none");
        $invoice['remark'] = $this->addCDATA($this->getRemark($order, $isPaid));
        $invoice['layout'] = $this->addCDATA($this->getLayout());
        $invoice['paid'] = $this->getPaid($order, $isPaid);
        $invoice['action'] = $this->addCDATA(
            $this->scopeConfig->getValue(
                self::INVOICE_ACTION_CONFIG_CODE,
                ScopeInterface::SCOPE_STORE
            )
        );
        $invoice['saverelation'] = $this->addCDATA(
            $this->scopeConfig->getValue(
                self::INVOICE_SAVE_RELATION_CONFIG_CODE,
                ScopeInterface::SCOPE_STORE
            )
        );

        switch($this->addCDATA(
            $this->scopeConfig->getValue(
                self::INVOICE_CALCULATION_METHOD_CONFIG_CODE,
                ScopeInterface::SCOPE_STORE
            )
        )){
            case 'incl':
            case 'excl':
                $calculation_method = $this->addCDATA(
                    $this->scopeConfig->getValue(
                        self::INVOICE_CALCULATION_METHOD_CONFIG_CODE,
                        ScopeInterface::SCOPE_STORE
                    )
                );
                break;
            case 'dynamic':
                if(!is_null($order->getBillingAddress()->getCompany())){
                    $calculation_method = 'excl';
                }else{
                    $calculation_method = 'incl';
                }
                break;
        }
        $invoice['calculation_method'] = $calculation_method;
        $invoice['tags'] = $this->getTags($order);
        $invoice['magento_version'] = $this->getVersion();

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

    private function getReference($order)
    {
        $reference = $this->scopeConfig->getValue(
            self::INVOICE_REFERENCE_CONFIG_KEY,
            ScopeInterface::SCOPE_STORE
        );

        $reference = str_replace('{order_id}', $order->getIncrementId(), $reference);

        return $reference;
    }

    private function getLayout()
    {
        $layout_code = $this->scopeConfig->getValue(
            self::INVOICE_LAYOUT_CONFIG_CODE,
            ScopeInterface::SCOPE_STORE
        );

        return isset($layout_code) ? $layout_code : '';
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

    private function getTags($order)
    {
        return [
            '@array' => [
                '@key' => 'tag',
                '@values' => [
                    $this->addCDATA($order->getIncrementId()),
                    $this->addCDATA(
                        $this->scopeConfig->getValue(
                            self::INVOICE_TAG_CONFIG_CODE,
                            ScopeInterface::SCOPE_STORE
                        )
                    ),
                ]
            ]
        ];
    }

    private function getVersion()
    {
        return $this->productMetadata->getVersion();
    }
}
