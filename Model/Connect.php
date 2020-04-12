<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */


namespace Qinvoice\Connect\Model;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\SalesRule\Model\CouponFactory;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Connect
{
    public function __construct(
        Qinvoice $qinvoice,
        ScopeConfigInterface $scopeInterface,
        StoreManagerInterface $storeManager,
        Product $product,
        ProductFactory $productFactory,
        CouponFactory $couponFactory,
        RuleFactory $ruleFactory,
        LoggerInterface $logger,
        TransportInterfaceFactory $mailTransportFactory,
        MessageInterface $message
    ) {
        $this->_scopeConfig = $scopeInterface;
        $this->_storeManager = $storeManager;
        $this->_qinvoice = $qinvoice;
        $this->_product = $product;
        $this->_productFactory = $productFactory;
        $this->_couponFactory = $couponFactory;
        $this->_logger = $logger;
        $this->_ruleFactory = $ruleFactory;
        $this->_mailTransportFactory = $mailTransportFactory;
        $this->_message = $message;
    }

    public function createInvoiceForQinvoice($order, $isPaid = false)
    {
        $document = $this->_qinvoice;

        $document->setDocumentType(
            $this->_scopeConfig->getValue(
                'invoice_options/invoice/document_type',
                ScopeInterface::SCOPE_STORE
            )
        );
        $paid_remark = '';

        $arrData = [];

        if ($isPaid) {
            // GETTING API URL
            $paid_remark = $this->_scopeConfig->getValue(
                'invoice_options/invoice/paid_remark',
                ScopeInterface::SCOPE_STORE
            );
            $document->paid = 1;
        }

        foreach ($order->getAllVisibleItems() as $row) {
            if ($row->getParentItemId()) {
                continue;
            }

            $arrData[] = $row->getData();
        }

        // GETTING LAYOUT CODE
        $layout_code = $this->_scopeConfig->getValue(
            'invoice_options/invoice/layout_code',
            ScopeInterface::SCOPE_STORE
        );

        $rowThree = $order->getBillingAddress()->getData();

        $payment = $order->getPayment();
        $payment_method = $payment->getMethodInstance();

        $document->payment_method = $payment->getMethod();
        $document->payment_method_label = $payment_method->getTitle();

        $document->companyname = $rowThree['company'];       // Your customers company name
        $document->firstname = $rowThree['firstname'];       // Your customers contact name
        $document->lastname = $rowThree['lastname'];       // Your customers contact name
        $document->email = $order->getCustomerEmail();     // Your customers emailaddress (invoice will be sent here)
        $document->phone = $rowThree['telephone'];
        $document->address = $rowThree['street'];                // Self-explanatory
        $document->zipcode = $rowThree['postcode'];              // Self-explanatory
        $document->city = $rowThree['city'];                     // Self-explanatory
        $document->country = $rowThree['country_id'];// 2 character country code: NL for Netherlands, DE for Germany etc
        $document->vatnumber = $order->getBillingAddress()->getVatId();

        if (is_object($order->getShippingAddress())) { // returns null when no address is specified
            $rowFour = $order->getShippingAddress()->getData();

            $document->delivery_companyname = $rowFour['company'];       // Your customers company name
            $document->delivery_firstname = $rowFour['firstname'];       // Your customers contact name
            $document->delivery_lastname = $rowFour['lastname'];       // Your customers contact name
            $document->delivery_address = $rowFour['street'];                // Self-explanatory
            $document->delivery_zipcode = $rowFour['postcode'];              // Self-explanatory
            $document->delivery_city = $rowFour['city'];                     // Self-explanatory
            $document->delivery_country = $rowFour['country_id'];
            $document->delivery_email = $order->getCustomerEmail();//Your customers emailaddress
            //(invoice will be sent here)
            $document->delivery_phone = $order->getShippingAddress()->getTelephone();
        }

        $document->vat = '';                     // Self-explanatory

        $save_relation = $this->_scopeConfig->getValue(
            'invoice_options/invoice/save_relation',
            ScopeInterface::SCOPE_STORE
        );
        $document->saverelation = $save_relation;

        $document_remark = $this->_scopeConfig->getValue(
            'invoice_options/invoice/invoice_remark',
            ScopeInterface::SCOPE_STORE
        );

        $document_remark = str_replace('{order_id}', $order->getIncrementId(), $document_remark);
        //$document_remark = str_replace('{shipping_description}', $rowOne['shipping_description'], $document_remark);

        $document->remark = $document_remark . "\n" . $paid_remark;

        $document_action = $this->_scopeConfig->getValue(
            'invoice_options/invoice/invoice_action',
            ScopeInterface::SCOPE_STORE
        );
        $document->action = $document_action;

        $calculation_method = $this->_scopeConfig->getValue(
            'invoice_options/invoice/calculation_method',
            ScopeInterface::SCOPE_STORE
        );
        $document->calculation_method = $calculation_method;

        $layout_code = isset($layout_code['default']) ? $layout_code['default'] : '';

        $document->setLayout($layout_code);

        $document_tag = $this->_scopeConfig->getValue(
            'invoice_options/invoice/invoice_tag',
            ScopeInterface::SCOPE_STORE
        );

        $pa_array = $this->_scopeConfig->getValue(
            'invoice_options/invoice/product_attributes',
            ScopeInterface::SCOPE_STORE
        );

        // OPTIONAL: Add tags
        $document->reference = $order->getIncrementId();
        $document->addTag($order->getIncrementId());
        $document->addTag($document_tag);

        $attributes = $this->_product->getAttributes();
        $attributeArray = [];

        foreach ($attributes as $attribute) {
            $attributeArray[$attribute->getData('attribute_code')] = $attribute->getData('frontend_label');
        }

        $cnt = count($arrData);
        for ($i = 0; $i < $cnt; $i++) {
            $category = '';
            $_productId = $arrData[$i]['product_id'];
            $_product = $this->_productFactory->create()->load($_productId);
            $arrItemOptions = $arrData[$i]['product_options'];

            $varDescription = '';

            $product_attributes = explode(",", $pa_array);
            foreach ($product_attributes as $pa) {
                if (isset($_product[$pa])) {
                    $varDescription .= "\n" . $attributeArray[$pa] . ': ' . $_product[$pa];
                }
            }

            if (isset($arrItemOptions['options']) && is_array($arrItemOptions['options'])) {
                $cntItemOptions = count($arrItemOptions['options']);
                for ($k = 0; $k < $cntItemOptions; $k++) {
                    $varDescription .= "\n"
                        . $arrItemOptions['options'][$k]['label']
                        . ": " . $arrItemOptions['options'][$k]['print_value'] . "\n";
                }
            }

            if (isset($arrItemOptions['attributes_info']) && is_array($arrItemOptions['attributes_info'])) {
                $cntItemOptions = count($arrItemOptions['attributes_info']);
                for ($k = 0; $k < $cntItemOptions; $k++) {
                    $varDescription .= "\n"
                        . $arrItemOptions['attributes_info'][$k]['label']
                        . ": " . $arrItemOptions['attributes_info'][$k]['value'] . "\n";
                }
            }

            if (isset($arrItemOptions['bundle_options']) && is_array($arrItemOptions['bundle_options'])) {
                foreach ($arrItemOptions['bundle_options'] as $option) {
                    foreach ($option['value'] as $value) {
                        $varDescription .= "\n"
                            . '['
                            . $option['label']
                            . '] '
                            . $value['qty'] . ' x ' . $value['title'];
                    }
                }
            }

            $params = [
                'code' => $arrData[$i]['sku'],
                'description' => trim($arrData[$i]['name']) . $varDescription,
                'price' => $arrData[$i]['base_price'] * 100,
                'price_incl' =>
                    round(
                        (
                            (($arrData[$i]['base_price'] * $arrData[$i]['qty_ordered'])
                                + $arrData[$i]['tax_amount']) / $arrData[$i]['qty_ordered']) * 100
                    ),
                'price_vat' => ($arrData[$i]['tax_amount'] / $arrData[$i]['qty_ordered']) * 100,
                'vatpercentage' => trim(number_format($arrData[$i]['tax_percent'], 2, '.', '')) * 100,
                'discount' => 0,
                'quantity' => $arrData[$i]['qty_ordered'] * 100,
                'categories' => $category,
            ];

            $document->addItem($params);

        }

        if ($order->getShippingAmount() > 0) {
            $params = [
                'code' => 'SHPMNT',
                'description' => trim($order->getShippingDescription()),
                'price' => $order->getShippingAmount() * 100,
                'price_incl' => $order->getShippingInclTax() * 100,
                'price_vat' => $order->getShippingTaxAmount() * 100,
                'vatpercentage' => round(($order->getShippingTaxAmount() / $order->getShippingAmount()) * 100) * 100,
                'discount' => 0,
                'quantity' => 100,
                'categories' => 'shipping',
            ];

            $document->addItem($params);

        }

        $couponCode = $order->getCouponCode();

        if ($couponCode > '') {
            $oCoupon = $this->_couponFactory->create()->load($couponCode, 'code');
            $oRule = $this->_ruleFactory->create()->load($oCoupon->getRuleId());

            $ruleData = $oRule->getData();

            $discount = $ruleData['discount_amount'];
            $params = [
                'code' => 'DSCNT',
                'description' => $couponCode,
                'price' => $order->getDiscountAmount() * 100,
                'price_incl' => $order->getDiscountAmount() * 100,
                'price_vat' => 0,
                'vatpercentage' => 0,
                'discount' => 0,
                'quantity' => -100,
                'categories' => 'discount',
            ];

            $document->addItem($params);
        }

        $result = $document->sendRequest();

        if (!is_numeric($result)) {
            $this->notifyAdmin('Qinvoice Connect Error', 'Could not send invoice for order '
                . $order->getIncrementId());
        }

        return true;
    }

    public function notifyAdmin($subject, $msg)
    {
        $varSubject = 'Qinvoice Notification';

        $this->_logger->addDebug($subject . ': ' . $msg);

        $this->_message->addTo(
            $this->_scopeConfig->getValue(
                'trans_email/ident_general/email',
                ScopeInterface::SCOPE_STORE
            )
        );
        $this->_message->setFrom("support@qinvoice.com");
        $this->_message->setMessageType(MessageInterface::TYPE_TEXT)
            ->setBody($msg)
            ->setSubject($subject);
        $transport = $this->_mailTransportFactory->create(['message' => clone $this->_message]);
        $transport->sendMessage();
    }
}
