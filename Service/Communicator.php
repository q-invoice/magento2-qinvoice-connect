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

    private $xmlApi = 'https://app.q-invoice.com/api/xml/';

    public function __construct(
        ScopeConfigInterface $scopeInterface,
        ClientFactory $httpClientFactory,
        DebugService $debugService
    )
    {
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

        $apiVersion = $this->scopeInterface->getValue(
            'invoice_options/invoice/api_url',
            ScopeInterface::SCOPE_STORE
        );

        $apiURL = $this->xmlApi;

        switch ($apiVersion) {
            case '';
            default:
            case '1_4':
                $apiURL .= '1.4/';
                break;
            case '1_3':
                $apiURL .= '1.3/';
                break;
            case '1_2':
                $apiURL .= '1.2/';
                break;
        }


        $request->setUri($apiURL);
        $request->setMethod(\Zend\Http\Request::METHOD_GET);

        $request->setContent($content);

        /** @var \Zend\Http\Client $client */
        $client = $this->httpClientFactory->create();

        $options = [
            'adapter' => Curl::class,
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

        var_dump($content);


        switch ($apiVersion) {
            case '1_4':
            case '';
            default:
                // We expect a JSON response
                $decoded_content = json_decode($content);
                var_dump($decoded_content);
                if ($decoded_content->result != 'OK') {
                    throw new LocalizedException(__('Qinvoice Connect Error Could not send invoice for order '));
                }
                break;
            case '1_1':
            case '1_2':
            case '1_3':
                if (preg_match('#[^0-9]#',trim($content))) {
                    $this->debugService->logQInvoiceRequest($content);
                    throw new LocalizedException(sprintf('Qinvoice Connect Error: Unexpected response "%s"'), $content);
                }
                break;

        }

    }
}
