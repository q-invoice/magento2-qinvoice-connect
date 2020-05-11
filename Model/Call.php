<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Model\Calculation;
use Psr\Log\LoggerInterface;

class Call
{

    private $signature = false;
    private $nonce = false;

    private $code;
    private $message;
    private $version = '2.3.0';

    protected $_logger;
    /**
     * @var RequestFactory
     */
    private $documentFactory;

    /**
     * Call constructor.
     * @param ScopeConfigInterface $scopeInterface
     * @param StoreManagerInterface $storeManager
     * @param Http $request
     * @param Product $product
     * @param ProductFactory $productFactory
     * @param StockItemRepository $stockItemRepository
     * @param CollectionFactory $productCollectionFactory
     * @param Calculation $calculation
     * @param StockRegistryInterface $stockRegistry
     * @param JsonFactory $resultJsonFactory
     * @param LoggerInterface $logger
     * @param RequestFactory $documentFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeInterface,
        StoreManagerInterface $storeManager,
        Http $request,
        Product $product,
        ProductFactory $productFactory,
        StockItemRepository $stockItemRepository,
        CollectionFactory $productCollectionFactory,
        Calculation $calculation,
        StockRegistryInterface $stockRegistry,
        JsonFactory $resultJsonFactory,
        LoggerInterface $logger,
        RequestFactory $documentFactory
    ) {
        $this->_scopeConfig = $scopeInterface;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->_product = $product;
        $this->_productFactory = $productFactory;
        $this->_stockRegistry = $stockRegistry;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_calculation = $calculation;
        $this->_stockItemRepository = $stockItemRepository;
        $this->resultJsonFactory        = $resultJsonFactory;
        $this->_logger = $logger;
        $this->documentFactory = $documentFactory;
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
                        $this->checkSignature(["test"]);
                        $this->code = "999";
                        $this->message = 'Plugin installed.';
                        $this->renderJson();
                        break;
                    case 'stock':
                         $this->updateStock(
                             $this->_request->getParam('sku'),
                             $this->_request->getParam('quantity')
                         );
                        return '';
                    case 'export':
                        return $this->exportCatalog($this->_request->getParam('store_id'));
                    case 'stores':
                        return $this->listStores();
                }
                return '';
            }
        }
    }

    private function renderJson($data = null)
    {
        $json = json_encode(
            [
                'response' =>
                    [
                        'version' => $this->version,
                        'code' => $this->code,
                        'message' => $this->message,
                    ],
                'data' => $data,
            ]
        );
        $jsonResult = $this->resultJsonFactory->create();
        $jsonResult->setData($json);
        return $jsonResult;
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

        $this->checkSignature(["stock", $sku, $quantity]);

        try {
            $stockItem = $this->_stockRegistry->getStockItemBySku($sku);
        } catch (Exception $e) {
            $this->code = '102';
            $this->message = $e->getMessage();
            $this->renderJson();
        }

        $stockItem->setQty($quantity);
        $stockItem->setIsInStock((bool)$quantity); // this line
        try {
            $this->_stockRegistry->updateStockItemBySku($sku, $stockItem);
        } catch (Exception $e) {
            $this->code = '199';
            $this->message = $e->getMessage();
            $this->renderJson();
        }

        $this->code = '900';
        $this->message = 'Product updated successfully';
        $this->renderJson();
    }

    private function checkSignature($params = [])
    {

        $secret = $this->_scopeConfig->getValue('invoice_options/invoice/webshop_secret', ScopeInterface::SCOPE_STORE);

        if ($secret != $this->signature) {
            $this->code = '030';
            $this->message = 'Invalid signature';
            return $this->renderJson();
        }
    }

    public function listStores()
    {

        $this->checkSignature(["stores"]);

        foreach ($this->_storeManager->getStores() as $store) {
            $store_array[] = $store->getData();
        }

        $this->code = '920';
        $this->message = sprintf('%d stores found', count($store_array));
        return $this->renderJson($store_array);
    }

    public function exportCatalog($store_id = null)
    {

        $this->checkSignature(["export", $store_id]);

        $products_array = [];
        $products = $this->_productCollectionFactory->create();
        $products->addAttributeToSelect('*');

        if ($store_id != null) {
            try {
                $store = $this->_storeManager->getStore($store_id);

            } catch (Exception $e) {
                $this->code = '130';
                $this->message = sprintf('Could not read from store ID %d', $store_id);
                $this->renderJson();
            }
            $products->setStoreId($store_id);
        } else {
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

            $tier_prices = [];
            //$product_data = Mage::getModel('catalog/product')->loadByAttribute('sku',$this->sku);
            $tier_prices = ($product->getTierPrice());
            foreach ($tier_prices as $tp) {
                $tp_array[$tp['price_qty']] = $tp['price'];
            }

            try {
                $stock = $this->_stockItemRepository->get($product->getId());
            } catch (Exception $e) {
                $stock = false;
            }

            $products_array[] = [
                'entity_id' => $product['entity_id'],
                'sku' => $product['sku'],
                'name' => $product['name'],
                'price' => $product['price'],
                'weight' => $product['weight'],
                'thumbnail' => $this->_storeManager->getStore()->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                )
                    . $product['thumbnail'],
                'special_price' => $product['special_price'],
                'stock' => !$stock ? 0 : $stock->getQty(),
                'min_stock' => !$stock ? 0 : $stock->getMinQty(),
                'vat' => $vat_percent * 100,
                'tier_prices' => $tp_array,
            ];
        }

        $this->code = '910';
        $this->message = sprintf('%d items exported', count($products_array));
        $this->renderJson($products_array);

        return '';
    }
}