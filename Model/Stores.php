<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Model;

use Magento\Store\Model\StoreManagerInterface;
use Qinvoice\Connect\Api\Data\ResponseDataInterfaceFactory;
use Qinvoice\Connect\Api\Data\StoresResponseInterfaceFactory;
use Qinvoice\Connect\Api\StoresInterface;

class Stores implements StoresInterface
{
    /**
     * @var StoresResponseInterfaceFactory
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
     * Stores constructor.
     * @param StoresResponseInterfaceFactory $responseFactory
     * @param ResponseDataInterfaceFactory $apiResponseDataFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoresResponseInterfaceFactory $responseFactory,
        ResponseDataInterfaceFactory $apiResponseDataFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->responseFactory = $responseFactory;
        $this->apiResponseDataFactory = $apiResponseDataFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function get()
    {
        $response = $this->responseFactory->create();
        $stores = $this->storeManager->getStores();
        $responseData = $this->apiResponseDataFactory->create()
            ->setCode(self::RESPONSE_CODE)
            ->setMessage(sprintf(self::RESPONSE_MESSAGE, count($stores)));
        $response->setData($stores);
        $response->setResponse($responseData);
        return $response;
    }
}
