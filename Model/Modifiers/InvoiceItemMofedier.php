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

        $items = $this->getOrderItems($order);

        if ($order->getShippingAmount() > 0) {
            $items[] = $this->getShipmentItem($order);
        }

        if ($order->getCouponCode()) {
            $items[] = $this->getCouponItem($order);
        }

        $invoice['items'] = [
            '@array' => [
                '@key' => 'item',
                '@values' => $items,
            ]
        ];
        return $document->addItem(self::PARENT_NODE, $invoice);
    }

    private function getOrderItems(OrderInterface $order)
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
            $itemData['description']  = $this->addCDATA(implode("\n", $description));
            $itemData['price'] = $orderItem->getBasePrice() * 100;
            $itemData['price_incl'] = $orderItem->getBasePriceInclTax() * 100;
            $itemData['price_vat'] = $orderItem->getBaseTaxAmount()/$orderItem->getQtyOrdered() * 100;
            $itemData['vatpercentage'] = $orderItem->getTaxPercent() * 100;
            $itemData['discount']  = 0;
            $itemData['categories']  = $this->addCDATA('');
            $items[] = $itemData;

            $itemDataOld[] = $orderItem;

        }

        return $items;
    }

    private function getShipmentItem($order)
    {
        return [
            'code' => $this->addCDATA('SHPMNT'),
            'description' => $this->addCDATA(trim($order->getShippingDescription())),
            'price' => $order->getShippingAmount() * 100,
            'price_incl' => $order->getShippingInclTax() * 100,
            'price_vat' => $order->getShippingTaxAmount() * 100,
            'vatpercentage' => round(($order->getShippingTaxAmount() / $order->getShippingAmount()) * 100) * 100,
            'discount' => 0,
            'quantity' => $this->addCDATA(100),
            'categories' => 'shipping',
        ];
    }

    private function getCouponItem($order)
    {
        return [
            'code' => $this->addCDATA('DSCNT'),
            'description' => $this->addCDATA($order->getCouponCode()),
            'price' => $order->getDiscountAmount() * 100,
            'price_incl' => $order->getDiscountAmount() * 100,
            'price_vat' => 0,
            'vatpercentage' => 0,
            'discount' => 0,
            'quantity' => $this->addCDATA(-100),
            'categories' => $this->addCDATA('discount'),
        ];
    }
}
