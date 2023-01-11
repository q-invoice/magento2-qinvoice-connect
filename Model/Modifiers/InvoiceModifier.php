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

use Qinvoice\Connect\Helper\CustomerGroupCalculationMethodHelper;
use Qinvoice\Connect\Helper\CustomerGroupLayoutCodeHelper;
use Qinvoice\Connect\Service\DebugService;

class InvoiceModifier implements ModifierInterface
{
    use AddCdata;

    const PARENT_NODE = "invoice";
    const INVOICE_REMARK_CONFIG_KEY = 'invoice_options/invoice/invoice_remark';
    const INVOICE_PAID_REMARK_CONFIG_KEY = 'invoice_options/invoice/paid_remark';
    const INVOICE_REFERENCE_CONFIG_KEY = 'invoice_options/invoice/reference';
    const INVOICE_ACTION_CONFIG_CODE = 'invoice_options/invoice/invoice_action';
    const INVOICE_LAYOUT_CONFIG_LAYOUT_SELECTOR = 'layout/general/layout_selector';
    const INVOICE_LAYOUT_CONFIG_LAYOUT_CODE = 'layout/general/layout_code';
    const INVOICE_SAVE_RELATION_CONFIG_CODE = 'invoice_options/invoice/save_relation';
    const CALCULATION_METHOD_CONFIG = 'calculation/general/method';
    const CALCULATION_CUSTOMER_GROUP_RULES = 'calculation/general/customer_group_rules';
    const INVOICE_TAG_CONFIG_CODE = 'invoice_options/invoice/invoice_tag';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;
    /**
     * @var CustomerGroupLayoutCodeHelper
     */
    private $customerGroupLayoutCodeHelper;
    /**
     * @var CustomerGroupCalculationMethodHelper
     */
    private $customerGroupCalculationMethodHelper;


    private $debugService;

    /**
     * LoginModifier constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ProductMetadataInterface $productMetadata,
        CustomerGroupLayoutCodeHelper $customerGroupLayoutCodeHelper,
        CustomerGroupCalculationMethodHelper $customerGroupCalculationMethodHelper,
        DebugService $debugService
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->productMetadata = $productMetadata;
        $this->customerGroupLayoutCodeHelper = $customerGroupLayoutCodeHelper;
        $this->customerGroupCalculationMethodHelper = $customerGroupCalculationMethodHelper;
        $this->debugService = $debugService;
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
        $invoice['layout'] = $this->addCDATA($this->getLayoutCode(
            $this->scopeConfig->getValue(
                self::INVOICE_LAYOUT_CONFIG_LAYOUT_SELECTOR,
                ScopeInterface::SCOPE_STORE,
                $order->getStoreId()
            ), $order)
        );
        $invoice['paid'] = $this->getPaid($order, $isPaid);
        $invoice['action'] = $this->addCDATA(
            $this->scopeConfig->getValue(
                self::INVOICE_ACTION_CONFIG_CODE,
                ScopeInterface::SCOPE_STORE,
                $order->getStoreId()
            )
        );
        $invoice['saverelation'] = $this->addCDATA(
            $this->scopeConfig->getValue(
                self::INVOICE_SAVE_RELATION_CONFIG_CODE,
                ScopeInterface::SCOPE_STORE,
                $order->getStoreId()
            )
        );

        $calculation_method = $this->getCalculationMethod(
            $this->scopeConfig->getValue(
                self::CALCULATION_METHOD_CONFIG,
                ScopeInterface::SCOPE_STORE,
                $order->getStoreId()
            ), $order);

        $invoice['calculation_method'] = $this->addCDATA($calculation_method);
        $invoice['tags'] = $this->getTags($order);
        $invoice['magento_version'] = $this->getVersion();

        $this->debugService->debug('Created document', $invoice);

        return $document->addItem(self::PARENT_NODE, $invoice);
    }

    private function getCalculationMethod($configValue, OrderInterface $order)
    {

        switch ($configValue) {
            case 'incl':
                return 'incl';
            case 'excl':
                return 'excl';
            case 'dynamic':
                if (!is_null($order->getBillingAddress()->getCompany())) {
                    return 'excl';
                } else {
                    return 'incl';
                }
            case 'customer_groups':
                // get customer group
                $customerGroupId = $order->getCustomerGroupId();
                $configValue = $this->customerGroupCalculationMethodHelper->getCalculationMethodForCustomerGroup($customerGroupId);

                return $this->getCalculationMethod($configValue, $order);
        }
    }

    private function getLayoutCode($configValue, OrderInterface $order)
    {

        switch ($configValue) {
            default:
            case 'fixed':
                $layout_code = $this->scopeConfig->getValue(
                    self::INVOICE_LAYOUT_CONFIG_LAYOUT_CODE,
                    ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                );

                return $layout_code;

            case 'customer_groups':
                // get customer group
                $customerGroupId = $order->getCustomerGroupId();
                return $this->customerGroupLayoutCodeHelper->getLayoutCodeForCustomerGroup($customerGroupId);

        }
    }

    private function getRemark(OrderInterface $order, $isPaid)
    {
        $document_remark = $this->scopeConfig->getValue(
            self::INVOICE_REMARK_CONFIG_KEY,
            ScopeInterface::SCOPE_STORE
        );

        if (is_null($document_remark)) {
            $document_remark = '';
        }

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

    private function getReference(OrderInterface $order)
    {
        $reference = $this->scopeConfig->getValue(
            self::INVOICE_REFERENCE_CONFIG_KEY,
            ScopeInterface::SCOPE_STORE,
            $order->getStoreId()
        );

        $reference = str_replace('{order_id}', $order->getIncrementId(), $reference);

        return $reference;
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

    private function getTags(OrderInterface $order)
    {
        return [
            '@array' => [
                '@key' => 'tag',
                '@values' => [
                    $this->addCDATA($order->getIncrementId()),
                    $this->addCDATA(
                        $this->scopeConfig->getValue(
                            self::INVOICE_TAG_CONFIG_CODE,
                            ScopeInterface::SCOPE_STORE,
                            $order->getStoreId()
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
