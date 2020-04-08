<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model;

class Qinvoice
{

    protected $gateway = '';
    private $username;
    private $password;
    private $document_type = 'invoice';

    public $reference;
    public $companyname;
    public $firstname;
    public $lastname;
    public $email;
    public $address;
    public $zipcode;
    public $city;
    public $country;
    public $phone;
    public $calculation_method = 'excl';
    public $delivery_companyname;
    public $delivery_firstname;
    public $delivery_lastname;
    public $delivery_address;
    public $delivery_zipcode;
    public $delivery_city;
    public $delivery_country;
    public $delivery_phone;
    public $delivery_email;

    public $delivery_date;
    public $date;

    public $vatnumber;
    public $remark;
    public $paid = 0;
    public $payment_method;
    public $payment_method_label;
    public $action;
    public $saverelation = false;

    public $layout;

    private $tags = [];
    private $items = [];
    private $files = [];
    private $recurring;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface
    )
    {

        $this->username = $scopeInterface->getValue('invoice_options/invoice/api_username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->password = $scopeInterface->getValue('invoice_options/invoice/api_password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->recurring = 'none';

        $apiURL = $scopeInterface->getValue('invoice_options/invoice/api_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        // GETTING API URL
        $this->gateway = $apiURL;
    }

    public function addTag($tag)
    {
        $this->tags[] = $tag;
    }

    public function setLayout($code)
    {
        $this->layout = $code;
    }

    public function setRecurring($recurring)
    {
        $this->recurring = strtolower($recurring);
    }

    public function addItem($params)
    {
        $item['code'] = (isset($params['code']) ? $params['code'] : "");
        $item['description'] = $params['description'];
        $item['price'] = $params['price'];
        $item['price_incl'] = $params['price_incl'];
        $item['price_vat'] = $params['price_vat'];
        $item['vatpercentage'] = $params['vatpercentage'];
        $item['discount'] = $params['discount'];
        $item['quantity'] = $params['quantity'];
        $item['categories'] = $params['categories'];
        $this->items[] = $item;
    }

    public function addFile($name, $url)
    {
        $this->files[] = ['url' => $url, 'name' => $name];
    }

    public function sendRequest()
    {
        $content = "<?xml version='1.0' encoding='UTF-8'?>";
        $content .= $this->buildXML();

        $headers = ["Content-type: application/atom+xml"];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->gateway);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        } else {
            curl_close($ch);
        }
        return $data;
    }

    public function setDocumentType($type){
        $this->document_type = $type;
    }
    private function buildXML()
    {
        $string = '<request>
                        <login mode="new'. ucfirst($this->document_type) .'">
                            <username><![CDATA[' . $this->username . ']]></username>
                            <password><![CDATA[' . $this->password . ']]></password>
                            <identifier><![CDATA[Magento_2.2.3]]></identifier>
                        </login>
                        <'. $this->document_type .'>
                            <date><![CDATA[' . $this->date . ']]></date>
                            <reference><![CDATA[' . $this->reference . ']]></reference>
                            <companyname><![CDATA[' . $this->companyname . ']]></companyname>
                            <firstname><![CDATA[' . $this->firstname . ']]></firstname>
                            <lastname><![CDATA[' . $this->lastname . ']]></lastname>
                            <email><![CDATA[' . $this->email . ']]></email>
                            <phone><![CDATA[' . $this->phone . ']]></phone>
                            <address><![CDATA[' . $this->address . ']]></address>
                            <zipcode><![CDATA[' . $this->zipcode . ']]></zipcode>
                            <city><![CDATA[' . $this->city . ']]></city>
                            <country><![CDATA[' . $this->country . ']]></country>

                            <date><![CDATA[' . $this->date . ']]></date>
                            <delivery_companyname><![CDATA[' . $this->delivery_companyname . ']]></delivery_companyname>
                            <delivery_firstname><![CDATA[' . $this->delivery_firstname . ']]></delivery_firstname>
                            <delivery_lastname><![CDATA[' . $this->delivery_lastname . ']]></delivery_lastname>
                            <delivery_address><![CDATA[' . $this->delivery_address . ']]></delivery_address>
                            <delivery_zipcode><![CDATA[' . $this->delivery_zipcode . ']]></delivery_zipcode>
                            <delivery_city><![CDATA[' . $this->delivery_city . ']]></delivery_city>
                            <delivery_country><![CDATA[' . $this->delivery_country . ']]></delivery_country>

                            <vat><![CDATA[' . $this->vatnumber . ']]></vat>
                            <recurring><![CDATA[' . $this->recurring . ']]></recurring>
                            <remark><![CDATA[' . $this->remark . ']]></remark>
                            <layout><![CDATA[' . $this->layout . ']]></layout>
                            <paid method="'. $this->payment_method .'" label="'. $this->payment_method_label .'"><![CDATA[' . $this->paid . ']]></paid>
                            <action><![CDATA[' . $this->action . ']]></action>
                            <saverelation><![CDATA[' . $this->saverelation . ']]></saverelation>
                            <calculation_method><![CDATA[' . $this->calculation_method . ']]></calculation_method>
                            <tags>';
        foreach ($this->tags as $tag) {
            $string .= '<tag><![CDATA[' . $tag . ']]></tag>';
        }

        $string .= '</tags>
                    <items>';
        foreach ($this->items as $i) {

            $string .= '<item>
                <code><![CDATA[' . $i['code'] . ']]></code>
                <quantity><![CDATA[' . $i['quantity'] . ']]></quantity>
                <description><![CDATA[' . $i['description'] . ']]></description>
                <price>' . $i['price'] . '</price>
                <price_incl>' . $i['price_incl'] . '</price_incl>
                <price_vat>' . $i['price_vat'] . '</price_vat>
                <vatpercentage>' . $i['vatpercentage'] . '</vatpercentage>
                <discount>' . $i['discount'] . '</discount>
                <categories><![CDATA[' . $i['categories'] . ']]></categories>

            </item>';
        }

        $string .= '</items>
                    <files>';
        foreach ($this->files as $f) {
            $string .= '<file url="' . $f['url'] . '">' . $f['name'] . '</file>';
        }
        $string .= '</files>
                </'. $this->document_type .'>
            </request>';
        return $string;
    }
}
