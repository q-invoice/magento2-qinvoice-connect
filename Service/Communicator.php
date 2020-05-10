<?php


namespace Qinvoice\Connect\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Zend\Http\Client\Adapter\Curl;
use Zend\Http\ClientFactory;

class Communicator
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeInterface;

    /**
     * @var ClientFactory
     */
    private $httpClientFactory;

    /**
     * @var DebugService
     */
    private $debugService;

    public function __construct(
        ScopeConfigInterface $scopeInterface,
        ClientFactory $httpClientFactory,
        DebugService $debugService
    ) {
        $this->scopeInterface = $scopeInterface;
        $this->httpClientFactory = $httpClientFactory;
        $this->debugService = $debugService;
    }

    public function sendRequest($content)
    {
        $headers = ["Content-type: application/atom+xml"];

        $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders($headers);

        $request = new \Zend\Http\Request();
        $request->setHeaders($httpHeaders);

        $apiURL = $this->scopeInterface->getValue(
            'invoice_options/invoice/api_url',
            ScopeInterface::SCOPE_STORE
        );

        $request->setUri($apiURL);
        $request->setMethod(\Zend\Http\Request::METHOD_GET);

        $request->setContent($content);

        /** @var \Zend\Http\Client $client */
        $client = $this->httpClientFactory->create();

        $options = [
            'adapter'   => Curl::class,
            'curloptions' => [
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $content
            ]
            ,
            'timeout' => 120
        ];
        $client->setOptions($options);

        $this->debugService->logQInvoiceRequest($request);

        $response = $client->send($request);

        $content = $response->getContent();

        if (!is_numeric($response->getContent())) {
            throw new LocalizedException('Qinvoice Connect Error Could not send invoice for order ');
        }
    }
}
