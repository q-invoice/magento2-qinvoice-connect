<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model;

use Qinvoice\Connect\Api\ModifierInterface;
use Qinvoice\Connect\Model\DocumentFactory;
use Qinvoice\Connect\Service\ConvertArray;

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
    /**
     * @var array
     */
    private $modifiers;

    /**
     * RequestFactory constructor.
     * @param \Qinvoice\Connect\Model\DocumentFactory $documentFactory
     * @param ConvertArray $convertArray
     * @param array $modifiers
     */
    public function __construct(
        DocumentFactory $documentFactory,
        ConvertArray $convertArray,
        $modifiers = []
    ) {
        $this->documentFactory = $documentFactory;
        $this->convertArray = $convertArray;
        $this->modifiers = $modifiers;
    }

    /**
     * @param $order
     * @param bool $isPaid
     */
    public function createDocumentFromOrder($order, $isPaid = false)
    {
        $qInvoice = $this->documentFactory->create();
        /** @var ModifierInterface $modifier */
        foreach ($this->modifiers as $modifier) {
            $modifier->modify($qInvoice, $order, $isPaid);
        }
        $xml = $this->convertArray->assocToXml($qInvoice->getItems(), $qInvoice::ROOT_NAME);
        file_put_contents('/app/new.xml', htmlspecialchars_decode($xml->asXML()));
    }
}
