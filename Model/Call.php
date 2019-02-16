<?php

/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model;

class Call
{

    private $signature = false;
    private $nonce = false;

    private $code;
    private $message;
    private $version = '2.1.2';


    public function __construct
    (
        \Qinvoice\Connect\Model\Connect $connect,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Tax\Model\Calculation $calculation
    )
    {
        $this->_scopeConfig = $scopeInterface;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->_product = $product;
        $this->_productFactory = $productFactory;
        $this->_connect = $connect;
        $this->_stockItemRepository = $stockItemRepository;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_calculation = $calculation;
    }

    public function qinvoiceCall()
    {

        if ($this->_request->getParam('qc') > '') {

            $mode = $this->_request->getParam('qc');
            if (in_array($mode, ['test', 'stock', 'export', 'stores'])) {

                $this->signature = $this->_request->getParam('signature');
                $this->nonce = $this->_request->getParam('nonce');

                if (!$this->nonce || $this->nonce == '') {
                    $this->code = "010";
                    $this->message = 'Nonce missing.';
                    return $this->renderJson();
                }

                if ($this->nonce < time() - 5000 && 1 == 2) {
                    $this->code = "011";
                    $this->message = 'Nonce expired.';
                    return $this->renderJson();
                }


                if (!$this->signature || $this->signature == '') {
                    $this->code = "021";
                    $this->message = 'Signature missing.';
                    return $this->renderJson();
                }

                switch ($mode) {
                    case 'test':
                        $this->checkSignature(array("test"));
                        $this->code = "999";
                        $this->message = 'Plugin installed.';
                        $this->renderJson();
                        break;
                    case 'stock':
                        echo $this->updateStock($this->_request->getParam('sku'), $this->_request->getParam('quantity'));
                        break;
                    case 'export':
                        echo $this->exportCatalog($this->_request->getParam('store_id'));
                        break;
                    case 'stores':
                        echo $this->listStores();
                        break;

                }
                exit();
            }
        }
    }

    private function renderJson($data = null)
    {
        echo json_encode(
            array(
                'response' =>
                    array(
                        'version' => $this->version,
                        'code' => $this->code,
                        'message' => $this->message
                    ),
                'data' => $data
            )
        );
        exit();
    }

    public function updateStock($sku = '', $quantity = '')
    {



        if ($sku == '') {
            $this->code = '100';
            $this->message = 'SKU is missing';
            $this->renderJson();
        }

        if ($quantity == '') {
            $this->code = '101';
            $this->message = 'Quantity is missing';
            $this->renderJson();
        }

        $this->checkSignature(array("stock", $sku, $quantity));

        $_product = $this->_productFactory->create();
        $_product->load($_product->getIdBySku($sku));

        if (!$_product->getId() > 0) {
            $this->code = '110';
            $this->message = 'Product not found';
            $this->renderJson();
        }

        $stock = $this->_stockItemRepository->get($_product->getId());

        if ($stock->getId() > 0 and $stock->getManageStock()) {
            $stock->setQty($quantity);
            $stock->setIsInStock((int)($quantity > 0));
            if (!$stock->save()) {
                $this->code = '120';
                $this->message = 'Error while updating';
                $this->renderJson();
            } else {
                $this->code = '900';
                $this->message = 'Product updated successfully';
                $this->renderJson();
            }
        }

    }

    private function checkSignature($params = array())
    {

        $secret = $this->_scopeConfig->getValue('invoice_options/invoice/webshop_secret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (md5(implode("", $params) . $this->nonce . $secret) != $this->signature) {
            $this->code = '030';
            $this->message = 'Invalid signature';
            $this->renderJson();
        }
    }

    public function listStores()
    {

        $this->checkSignature(array("stores"));

        foreach ($this->_storeManager->getStores() as $store) {
            $store_array[] = $store->getData();
        }

        $this->code = '920';
        $this->message = sprintf('%d stores found', count($store_array));
        $this->renderJson($store_array);
        return;
    }

    public function exportCatalog($store_id = null)
    {

        $this->checkSignature(array("export", $store_id));

        $products_array = array();
        $products = $this->_productCollectionFactory->create();
        $products->addAttributeToSelect('*');

        if ($store_id != null) {
            try {
                $store = $this->_storeManager->getStore($store_id);

            } catch (\Exception $e) {
                $this->code = '130';
                $this->message = sprintf('Could not read from store ID %d', $store_id);
                $this->renderJson();
            }
            $products->setStoreId($store_id);
        }else{
            $store = $this->_storeManager->getStore('default');
        }

        //Magento does not load all attributes by default
        //Add as many as you like
        $products->addAttributeToSelect('name');
        $products->addAttributeToSelect('price');
        $products->addAttributeToSelect('special_price');
        foreach ($products as $product) {
            $tp_array = [];

            $taxCalculation = $this->_calculation;
            $request = $taxCalculation->getRateRequest(null, null, null, $store);
            $taxClassId = $product->getTaxClassId();
            $vat_percent = $taxCalculation->getRate($request->setProductClassId($taxClassId));

            $tier_prices = array();
            //$product_data = Mage::getModel('catalog/product')->loadByAttribute('sku',$this->sku); 
            $tier_prices = ($product->getTierPrice());
            foreach ($tier_prices as $tp) {
                $tp_array[$tp['price_qty']] = $tp['price'];
            }


            $stock = $this->_stockItemRepository->get($product->getId());
            $products_array[] = array(
                'entity_id' => $product['entity_id'],
                'sku' => $product['sku'],
                'name' => $product['name'],
                'price' => $product['price'],
                'weight' => $product['weight'],
                'thumbnail' => $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $product['thumbnail'],
                'special_price' => $product['special_price'],
                'stock' => $stock->getQty(),
                'min_stock' => $stock->getMinQty(),
                'vat' => $vat_percent * 100,
                'tier_prices' => $tp_array
            );
        }

        $this->code = '910';
        $this->message = sprintf('%d items exported', count($products_array));
        $this->renderJson($products_array);

        return;

    }

    public function sendOnOrderPlace($order)
    {
        // GETTING TRIGGER SETTING
        $order_triggers = explode(",",$this->_scopeConfig->getValue('invoice_options/invoice/invoice_trigger_payment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        $payment = $order->getPayment();

        if (in_array($payment->getMethod(), $order_triggers)) {
            $this->_connect->createInvoiceForQinvoice($order, false);
        }
    }

    public function sendOnOrderPay($order)
    {
        // GETTING TRIGGER SETTING
        $invoice_triggers = explode(",",$this->_scopeConfig->getValue('invoice_options/invoice/invoice_trigger_payment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        $payment = $order->getPayment();

        if (in_array($payment->getMethod(), $invoice_triggers)) {
            $this->_connect->createInvoiceForQinvoice($order, true);
        }
    }

    public function orderStatusChange($order)
    {
        // currently not in use
    }
}
