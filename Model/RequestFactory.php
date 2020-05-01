<?php

namespace Qinvoice\Connect\Model;

use Magento\Elasticsearch\SearchAdapter\DocumentFactory;
use Magento\Framework\Convert\ConvertArray;

class RequestFactory
{
    /**
     * @var Document
     */
    private $documentFactory;
    /**
     * @var ConvertArray
     */
    private $convertArray;

    public function __construct(
        DocumentFactory $documentFactory,
        ConvertArray $convertArray
    ) {
        $this->documentFactory = $documentFactory;
        $this->convertArray = $convertArray;
    }

    /**
     * @param $order
     * @param bool $isPaid
     */
    public function createDocumentFromOrder($order, $isPaid = false)
    {
        $qInvoice = $this->documentFactory->create();
        $xml = $this->convertArray->assocToXml($qInvoice->toArray(), $qInvoice::ROOT_NAME);
        file_put_contents('/app/new.xml', $xml->asXML());
    }
}
