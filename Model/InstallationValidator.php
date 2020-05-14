<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Model;

use Qinvoice\Connect\Model\Data\ApiResponseDataFactory;
use Qinvoice\Connect\Model\Data\ApiResponseFactory;

class InstallationValidator implements \Qinvoice\Connect\Api\InstallationValidatorInterface
{
    /**
     * @var ApiResponseFactory
     */
    private $apiResponseFactory;
    /**
     * @var ApiResponseData
     */
    private $apiResponseDataFactory;

    /**
     * InstallationValidator constructor.
     * @param ApiResponseFactory $apiResponseFactory
     */
    public function __construct(
        ApiResponseFactory $apiResponseFactory,
        ApiResponseDataFactory $apiResponseDataFactory
    ) {
        $this->apiResponseFactory = $apiResponseFactory;
        $this->apiResponseDataFactory = $apiResponseDataFactory;
    }

    /**
     * @inheritDoc
     */
    public function vlidate()
    {
        $apiResponse = $this->apiResponseFactory->create();
        $response = $this->apiResponseDataFactory->create();
        $response->setCode(self::RESPONSE_CODE)
            ->setMessage(self::RESPONSE_MESSAGE);
        $apiResponse->setResponse($response);
        return $apiResponse;
    }
}
