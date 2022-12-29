<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Modifiers;

use Magento\Sales\Api\Data\OrderInterface;
use Qinvoice\Connect\Api\ModifierInterface;
use Qinvoice\Connect\Model\Document;

class ShippingAddressModifier implements ModifierInterface
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
        $shippingAddress = $order->getShippingAddress();
        if(is_null($shippingAddress)){
            return $document;
        }
        $invoice['delivery_companyname'] = $this->addCDATA($shippingAddress->getCompany());
        $invoice['delivery_firstname'] = $this->addCDATA($shippingAddress->getFirstname());
        $invoice['delivery_lastname'] = $this->addCDATA($shippingAddress->getLastname());
        $invoice['delivery_address'] = $this->addCDATA(implode("\n", $shippingAddress->getStreet()));
        $invoice['delivery_zipcode'] = $this->addCDATA($shippingAddress->getPostcode());
        $invoice['delivery_city'] = $this->addCDATA($shippingAddress->getCity());
        $invoice['delivery_country'] = $this->addCDATA($shippingAddress->getCountryId());

        return $document->addItem(self::PARENT_NODE, $invoice);
    }
}
