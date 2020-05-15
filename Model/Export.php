<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Model;

use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Model\Calculation;
use Qinvoice\Connect\Api\Data\ExportResponseInterfaceFactory;
use Qinvoice\Connect\Api\Data\ResponseDataInterfaceFactory;
use Qinvoice\Connect\Api\ExportInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class Export implements ExportInterface
{
    /**
     * @var ExportResponseInterfaceFactory
     */
    private $exportResponseFactory;

    /**
     * @var ResponseDataInterfaceFactory
     */
    private $apiResponseDataFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var Calculation
     */
    private $calculation;
    /**
     * @var StockItemRepository
     */
    private $stockItemRepository;

    /**
     * Export constructor.
     * @param ExportResponseInterfaceFactory $exportResponseFactory
     * @param ResponseDataInterfaceFactory $apiResponseDataFactory
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $collectionFactory
     * @param Calculation $calculation
     * @param StockItemRepository $stockItemRepository
     */
    public function __construct(
        ExportResponseInterfaceFactory $exportResponseFactory,
        ResponseDataInterfaceFactory $apiResponseDataFactory,
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        Calculation $calculation,
        StockItemRepository $stockItemRepository
    ) {
        $this->exportResponseFactory = $exportResponseFactory;
        $this->apiResponseDataFactory = $apiResponseDataFactory;
        $this->storeManager = $storeManager;
        $this->collectionFactory = $collectionFactory;
        $this->calculation = $calculation;
        $this->stockItemRepository = $stockItemRepository;
    }

    /**
     * @inheritDoc
     */
    public function export($storeId = "default")
    {
        $apiResponse = $this->exportResponseFactory->create();
        $response = $this->apiResponseDataFactory->create();

        try {
            $store = $this->storeManager->getStore($storeId);
        } catch (\Exception $e) {
            $response->setCode(self::STORE_NOT_FOUND_CODE)
                ->setMessage(sprintf(self::STORE_NOT_FOUND_MESSAGE, $storeId));
            $apiResponse->setResponse($response);
            return $apiResponse;
        }

        $products = $this->getProducts($storeId, $store);
        $response->setCode(self::SUCESS_CODE)
            ->setMessage(sprintf(self::SUCESS_MESSAGE, count($products)));

        $apiResponse->setData($products);
        $apiResponse->setResponse($response);
        return $apiResponse;
    }

    /**
     * @param $storeId
     * @param $store
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProducts($storeId, $store)
    {
        $productsArray = [];
        $productCollection = $this->collectionFactory->create();
        $productCollection->addAttributeToSelect('*');

        $productCollection->setStoreId($storeId);

        $productCollection->addAttributeToSelect('name');
        $productCollection->addAttributeToSelect('price');
        $productCollection->addAttributeToSelect('special_price');
        foreach ($productCollection as $product) {
            $tp_array = [];
            $request = $this->calculation->getRateRequest(null, null, null, $store);
            $taxClassId = $product->getTaxClassId();
            $vat_percent = $this->calculation->getRate($request->setProductClassId($taxClassId));

            $tier_prices = ($product->getTierPrice());
            foreach ($tier_prices as $tp) {
                $tp_array[$tp['price_qty']] = $tp['price'];
            }

            try {
                $stock = $this->stockItemRepository->get($product->getId());
            } catch (\Exception $e) {
                $stock = false;
            }

            $productsArray[] = [
                'entity_id' => $product['entity_id'],
                'sku' => $product['sku'],
                'name' => $product['name'],
                'price' => $product['price'],
                'weight' => $product['weight'],
                'thumbnail' => $this->storeManager->getStore()->getBaseUrl(
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

        return $productsArray;
    }
}
