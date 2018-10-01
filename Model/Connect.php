<?php

/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model;

class Connect
{
    public function __construct
    (
        \Qinvoice\Connect\Model\Qinvoice $qinvoice,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Mail\TransportInterfaceFactory $mailTransportFactory,
        \Magento\Framework\Mail\MessageInterface $message

    )
    {
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

    public function createInvoiceForQinvoice($order, $ifPaid = false)
    {

        $paid = 0;
        $arrData = [];
        $varCurrenyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        // GETTING ORDER STATUS
        $rowOne = $order;

        if ($rowOne['status'] == 'processing' || $rowOne['status'] == 'complete' || $rowOne['total_paid'] == $rowOne['grand_total']) {
            $varStatus = 'Paid';
            // GETTING API URL
            $paid_remark = $this->_scopeConfig->getValue('invoice_options/invoice/paid_remark', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $paid = 1;
        } else {
            if ($ifPaid == true) {
                // cancel if invoice has to be paid
                return;
            }
            $paid_remark = '';
            $varStatus = 'Sent';
        }

        foreach ($order->getAllVisibleItems() as $row) {
            if ($row->getParentItemId())
                continue;

            $arrData[] = $row->getData();
        }

        if (!$arrData) {
            //return false;
        }
        //$comment = '';
        //$comment = $data['comment_text'];
        // getting po_number
        $random_number = rand(0, pow(10, 7));

        // GETTING API USERNAME
        $username = $this->_scopeConfig->getValue('invoice_options/invoice/api_username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        // GETTING API PASSWORD
        $password = $this->_scopeConfig->getValue('invoice_options/invoice/api_password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        // GETTING LAYOUT CODE
        $layout_code = $this->_scopeConfig->getValue('invoice_options/invoice/layout_code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $rowThree = $order->getBillingAddress()->getData();

        $invoice = $this->_qinvoice;

        $invoice->companyname = $rowThree['company'];       // Your customers company name
        $invoice->firstname = $rowThree['firstname'];       // Your customers contact name
        $invoice->lastname = $rowThree['lastname'];       // Your customers contact name
        $invoice->email = $rowOne['customer_email'];                // Your customers emailaddress (invoice will be sent here)
        $invoice->phone = $rowThree['telephone'];
        $invoice->address = $rowThree['street'];                // Self-explanatory
        $invoice->zipcode = $rowThree['postcode'];              // Self-explanatory
        $invoice->city = $rowThree['city'];                     // Self-explanatory
        $invoice->country = $rowThree['country_id'];                 // 2 character country code: NL for Netherlands, DE for Germany etc
        $invoice->vatnumber = strlen($rowThree['vat_id']) > 3 ? $rowThree['vat_id'] : $rowOne['customer_taxvat'];

        $rowFour = $order->getShippingAddress()->getData();

        $invoice->delivery_companyname = $rowFour['company'];       // Your customers company name
        $invoice->delivery_firstname = $rowFour['firstname'];       // Your customers contact name
        $invoice->delivery_lastname = $rowFour['lastname'];       // Your customers contact name
        $invoice->delivery_address = $rowFour['street'];                // Self-explanatory
        $invoice->delivery_zipcode = $rowFour['postcode'];              // Self-explanatory
        $invoice->delivery_city = $rowFour['city'];                     // Self-explanatory
        $invoice->delivery_country = $rowFour['country_id'];

        $invoice->vat = '';                     // Self-explanatory
        $invoice->paid = $paid;

        $save_relation = $this->_scopeConfig->getValue('invoice_options/invoice/save_relation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $invoice->saverelation = $save_relation;

        $invoice_remark = $this->_scopeConfig->getValue('invoice_options/invoice/invoice_remark', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $order_id = $rowOne['increment_id'];
        $invoice_remark = str_replace('{order_id}', $rowOne['increment_id'], $invoice_remark);
        $invoice_remark = str_replace('{shipping_description}', $rowOne['shipping_description'], $invoice_remark);

        $invoice->remark = $invoice_remark . "\n" . $paid_remark;

        $invoice_action = $this->_scopeConfig->getValue('invoice_options/invoice/invoice_action', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $invoice->action = $invoice_action;

        $calculation_method = $this->_scopeConfig->getValue('invoice_options/invoice/calculation_method', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $invoice->calculation_method = $calculation_method;

        $layout_code_s = @unserialize($layout_code);
        if ($layout_code_s !== false) {
            // serialized
            $layout_code = @unserialize($layout_code);
            if (isset($layout_code[$rowFour['country_id']])) {
                $layout_code = @$layout_code[$rowFour['country_id']];
            } else {
                $layout_code = @$layout_code['default'];
            }
        } else {
            // not serialized
            $layout_code = $layout_code;
        }

        $invoice->setLayout($layout_code);

        $invoice_tag = $this->_scopeConfig->getValue('invoice_options/invoice/invoice_tag', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $pa_array = $this->_scopeConfig->getValue('invoice_options/invoice/product_attributes', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);


        // OPTIONAL: Add tags
        $invoice->addTag($rowOne['increment_id']);
        $invoice->addTag($invoice_tag);
        //  $invoice->addTag('send: '. $send_mail);
        //  $invoice->addTag('paid: '. $paid .' '. $rowOne['total_paid']);

        $store_id = $order->getStoreId();


        $attributes = $this->_product->getAttributes();
        $attributeArray = array();

        foreach ($attributes as $attribute) {
            $attributeArray[$attribute->getData('attribute_code')] = $attribute->getData('frontend_label');
            // $attributeArray[] = array(
            //     'label' => $attribute->getData('frontend_label'),
            //     'value' => $attribute->getData('attribute_code')
            // );
        }


        //print_r($order);

        for ($i = 0; $i < count($arrData); $i++) {
            $category = '';
            $_productId = $arrData[$i]['product_id'];
            $_product = $this->_productFactory->create()->load($_productId);

            $category = $_product->getData('qinvoice_category');
            $productcode = $_product->getData('qinvoice_productcode');

            $arrItemOptions = $arrData[$i]['product_options'];

            $varDescription = '';

            //print_r();

            $product_attributes = explode(",", $pa_array);
            foreach ($product_attributes as $pa) {
                if (isset($_product[$pa])) {
                    $varDescription .= "\n" . $attributeArray[$pa] . ': ' . $_product[$pa];
                }
            }


            if (@$arrItemOptions['options']) {
                for ($k = 0; $k < count($arrItemOptions['options']); $k++) {
                    $varDescription .= "\n" . $arrItemOptions['options'][$k]['label'] . ": " . $arrItemOptions['options'][$k]['print_value'] . "\n";
                }
            }

            if (@$arrItemOptions['attributes_info']) {
                for ($k = 0; $k < count($arrItemOptions['attributes_info']); $k++) {
                    $varDescription .= "\n" . $arrItemOptions['attributes_info'][$k]['label'] . ": " . $arrItemOptions['attributes_info'][$k]['value'] . "\n";
                }
            }

            if (@$arrItemOptions['bundle_options']) {
                foreach ($arrItemOptions['bundle_options'] as $option) {
                    foreach ($option['value'] as $value) {
                        $varDescription .= "\n" . '[' . $option['label'] . '] ' . $value['qty'] . ' x ' . $value['title'];
                    }
                }
            }


            $params = array(
                'code' => $productcode,
                'description' => "[" . $arrData[$i]['sku'] . "] " . trim($arrData[$i]['name']) . $varDescription,
                'price' => $arrData[$i]['base_price'] * 100,
                //'price_incl' => ((($arrData[$i]['base_price']*$arrData[$i]['qty_ordered'])+$arrData[$i]['tax_amount'])/$arrData[$i]['qty_ordered'])*100,
                'price_incl' => round(((($arrData[$i]['base_price'] * $arrData[$i]['qty_ordered']) + $arrData[$i]['tax_amount']) / $arrData[$i]['qty_ordered']) * 100),
                'price_vat' => ($arrData[$i]['tax_amount'] / $arrData[$i]['qty_ordered']) * 100,
                'vatpercentage' => trim(number_format($arrData[$i]['tax_percent'], 2, '.', '')) * 100,
                'discount' => 0,
                'quantity' => $arrData[$i]['qty_ordered'] * 100,
                'categories' => $category
            );

            $invoice->addItem($params);

        }

        if ($rowOne['shipping_amount'] > 0) {
            $params = array(
                'code' => 'SHPMNT',
                'description' => trim($rowOne['shipping_description']),
                'price' => $rowOne['shipping_amount'] * 100,
                'price_incl' => $rowOne['shipping_incl_tax'] * 100,
                'price_vat' => $rowOne['shipping_tax_amount'] * 100,
                'vatpercentage' => round(($rowOne['shipping_tax_amount'] / $rowOne['shipping_amount']) * 100) * 100,
                'discount' => 0,
                'quantity' => 100,
                'categories' => 'shipping'
            );

            $invoice->addItem($params);

        }

        // $order = Mage::getModel('sales/order')->loadByIncrementId($varOrderID);

        // $orderDetails = $order->getData();

        $couponCode = $rowOne['coupon_code'];
        //echo $couponCode;
        //print_r($order);
        // $couponCode = $orderDetails['coupon_code'];

        if ($couponCode > '') {
            $oCoupon = $this->_couponFactory->create()->load($couponCode, 'code');
            $oRule = $this->_ruleFactory->create()->load($oCoupon->getRuleId());
            var_dump($oRule->getData());

            $ruleData = $oRule->getData();

            $discount = $ruleData['discount_amount'];
            $params = array(
                'code' => 'DSCNT',
                'description' => $couponCode,
                'price' => ($rowOne['base_subtotal'] * ($discount / 100)) * 100,
                'price_incl' => ($rowOne['base_subtotal'] * ($discount / 100)) * 100,
                'price_vat' => 0,
                'vatpercentage' => 0,
                'discount' => 0,
                'quantity' => -100,
                'categories' => 'discount'
            );

            $invoice->addItem($params);
        }


        // $coupon = Mage::getModel('salesrule/rule');
        // $couponCollection = $coupon->getCollection();
        // foreach($couponCollection as $c){
        //     print_r($c);
        //     echo 'Code:'.$c->getCode().'--->Discount Amount:'.$c->getDiscountAmount().'<br />';

        //     $params = array(  
        //         'code' => 'DSCNT',  
        //         'description' => $c->getCode(),
        //         'price' => $rowOne['base_subtotal'] * ($c->getDiscountAmount()/100),
        //         'price_incl' => $rowOne['base_subtotal'] * ($c->getDiscountAmount()/100),
        //         'price_vat' => 0,
        //         'vatpercentage' => 0,
        //         'discount' => 0,
        //         'quantity' => -100,
        //         'categories' => 'discount'
        //         );

        //     $invoice->addItem($params);

        // }

        $result = $invoice->sendRequest();

        if ($result != 1) {
            $this->notify_admin('Qinvoice Connect Error', 'Could not send invoice for order ' . $order_id);
        }

        return true;


        //$curlInvoiveResult = $this->sendCurlRequest($createInvoiceXML);
    }

    public function notify_admin($subject, $msg)
    {
        $varSubject = 'Qinvoice Notification';

        $this->_logger->addDebug($subject . ': ' . $msg);

        $this->_message->addTo($this->_scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE), $this->_scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        $this->_message->setFrom("support@qinvoice.com", "Qinvoice Support");
        $this->_message->setMessageType(\Magento\Framework\Mail\MessageInterface::TYPE_TEXT)
            ->setBody($msg)
            ->setSubject($subject);
        $transport = $this->_mailTransportFactory->create(['message' => clone $this->_message]);
        $transport->sendMessage();
    }
}
