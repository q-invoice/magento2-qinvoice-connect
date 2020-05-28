<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Model;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Qinvoice\Connect\Api\Data\ResponseDataInterfaceFactory;
use Qinvoice\Connect\Api\Data\StockResponseInterfaceFactory;
use Qinvoice\Connect\Api\StockInterface;

class Stock implements StockInterface
{
    /**
     * @var StockResponseInterfaceFactory
     */
    private $responseFactory;
    /**
     * @var ResponseDataInterfaceFactory
     */
    private $apiResponseDataFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * Stores constructor.
     * @param StockResponseInterfaceFactory $responseFactory
     * @param ResponseDataInterfaceFactory $apiResponseDataFactory
     * @param StoreManagerInterface $storeManager
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(
        StockResponseInterfaceFactory $responseFactory,
        ResponseDataInterfaceFactory $apiResponseDataFactory,
        StoreManagerInterface $storeManager,
        StockRegistryInterface $stockRegistry
    ) {
        $this->responseFactory = $responseFactory;
        $this->apiResponseDataFactory = $apiResponseDataFactory;
        $this->storeManager = $storeManager;
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * @inheritDoc
     */
    public function update($sku, $quantity)
    {
        $response = $this->responseFactory->create();
        try {
            $stockItem = $this->stockRegistry->getStockItemBySku($sku);
        } catch (\Exception $e) {
            $responseData =  $this->apiResponseDataFactory->create()
                ->setCode(self::ITEM_NOT_FOUND_RESPONSE_CODE)
                ->setMessage($e->getMessage());
            return $response->setResponse($responseData);
        }

        $stockItem->setQty($quantity);
        $stockItem->setIsInStock((bool)$quantity);
        try {
            $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
        } catch (\Exception $e) {
            $responseData =  $this->apiResponseDataFactory->create()
                ->setCode(self::CANNOT_UPDATE_STOCK_REPONSE_CODE)
                ->setMessage($e->getMessage());
            return $response->setResponse($responseData);
        }

        $responseData = $this->apiResponseDataFactory->create()
            ->setCode(self::RESPONSE_CODE)
            ->setMessage(self::RESPONSE_MESSAGE);
        $response->setData([$sku, $quantity]);
        $response->setResponse($responseData);
        return $response;
    }
}
