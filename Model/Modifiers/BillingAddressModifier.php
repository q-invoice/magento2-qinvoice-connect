<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Modifiers;

use Magento\Sales\Api\Data\OrderInterface;
use Qinvoice\Connect\Api\ModifierInterface;
use Qinvoice\Connect\Model\Document;

class BillingAddressModifier implements ModifierInterface
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
        $billingAddress = $order->getBillingAddress();
        $invoice['companyname'] = $this->addCDATA($billingAddress->getCompany());
        $invoice['firstname'] = $this->addCDATA($billingAddress->getFirstname());
        $invoice['lastname'] = $this->addCDATA($billingAddress->getLastname());
        $invoice['email'] = $this->addCDATA($order->getCustomerEmail());
        $invoice['phone'] = $this->addCDATA($billingAddress->getTelephone());
        $invoice['address'] = $this->addCDATA(implode("\n", $billingAddress->getStreet()));
        $invoice['zipcode'] = $this->addCDATA($billingAddress->getPostcode());
        $invoice['city'] = $this->addCDATA($billingAddress->getCity());
        $invoice['country'] = $this->addCDATA($billingAddress->getCountryId());
        $invoice['vat'] = $this->addCDATA($billingAddress->getVatId());
        return $document->addItem(self::PARENT_NODE, $invoice);
    }
}
