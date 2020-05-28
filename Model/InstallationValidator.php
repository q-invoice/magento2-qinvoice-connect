<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Model;

use Qinvoice\Connect\Api\Data\InstallationValidatorResponseInterfaceFactory;
use Qinvoice\Connect\Api\Data\ResponseDataInterfaceFactory;

class InstallationValidator implements \Qinvoice\Connect\Api\InstallationValidatorInterface
{
    /**
     * @var InstallationValidatorResponseInterfaceFactory
     */
    private $apiResponseFactory;
    /**
     * @var ResponseDataInterfaceFactory
     */
    private $apiResponseDataFactory;

    /**
     * InstallationValidator constructor.
     * @param InstallationValidatorResponseInterfaceFactory $apiResponseFactory
     * @param ResponseDataInterfaceFactory $apiResponseDataFactory
     */
    public function __construct(
        InstallationValidatorResponseInterfaceFactory $apiResponseFactory,
        ResponseDataInterfaceFactory $apiResponseDataFactory
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
