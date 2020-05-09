<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Modifiers;

use Magento\Sales\Api\Data\OrderInterface;
use Qinvoice\Connect\Api\ModifierInterface;
use Qinvoice\Connect\Model\Document;

class InvoiceModifier implements ModifierInterface
{
    use AddCdata;
    const PARENT_NODE = "invoice";

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
        $invoice['recurring'] = $this->addCDATA("none");
        return $document->addItem(self::PARENT_NODE, $invoice);
    }
}
