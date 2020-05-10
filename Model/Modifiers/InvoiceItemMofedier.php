<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Modifiers;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Store\Model\ScopeInterface;
use Qinvoice\Connect\Api\ModifierInterface;
use Qinvoice\Connect\Model\Document;

class InvoiceItemMofedier implements ModifierInterface
{
    use AddCdata;

    const PARENT_NODE = "invoice";

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

        $items = $this->getItems($order);

        $invoice['items'] = [
            '@array' => [
                '@key' => 'item',
                '@values' => $items,
            ]
        ];
        return $document->addItem(self::PARENT_NODE, $invoice);
    }

    private function getItems(OrderInterface $order)
    {
        $productAttributes = $this->scopeConfig->getValue(
            'invoice_options/invoice/product_attributes',
            ScopeInterface::SCOPE_STORE
        );

        $items = [];
        $arrData = [];

        /** @var OrderItemInterface $orderItem */
        foreach ($order->getAllVisibleItems() as $orderItem) {
            if ($orderItem->getParentItemId()) {
                continue;
            }

            $orderProduct = $orderItem->getProduct();
            $description = [];
            $description[] = trim($orderProduct->getName());
            foreach (explode(",", $productAttributes) as $attrCode) {
                $attrVal = $orderProduct->getData($attrCode);
                if ($attrVal !== null) {
                    $description[] = sprintf("%s : %s", $attrCode, $attrVal);
                }
            }

            $productOptions = $orderItem->getProductOptions();

            if (isset($productOptions['options'])) {
                foreach ($productOptions['options'] as $option) {
                    $description[] = sprintf("%s : %s", $option['label'], $option['print_value']);
                }
            }

            if (isset($productOptions['bundle_options'])) {
                foreach ($productOptions['bundle_options'] as $option) {
                    foreach ($option['value'] as $value) {
                        $description[] = sprintf(
                            "[%s] %s x %s",
                            $option['label'],
                            $value['qty'],
                            $value['title']
                        );
                    }
                }
            }

            $itemData = [];
            $itemData['code'] = $this->addCDATA($orderItem->getSku());
            $itemData['quantity'] = $this->addCDATA($orderItem->getQtyOrdered() * 100);
            $itemData['discription']  = $this->addCDATA(implode("\n", $description));
            $itemData['price'] = $orderItem->getBasePrice() * 100;
            $itemData['price_incl'] = $orderItem->getBasePriceInclTax() * 100;
            $itemData['price_vat'] = $orderItem->getBaseTaxAmount()/$orderItem->getQtyOrdered() * 100;
            $itemData['vatpercentage'] = $orderItem->getTaxPercent() * 100;
            $itemData['discount']  = $orderItem->getBaseDiscountAmount() * 100;
            $itemData['categories']  = $this->addCDATA('');
            $items[] = $itemData;

            $itemDataOld[] = $orderItem;

        }

        return $items;
    }
}
