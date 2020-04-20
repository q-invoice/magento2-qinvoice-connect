<?php

namespace Qinvoice\Connect\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class DebugService
{

    const DEBUG_MODE_CONFIG_PATH = "invoice_options/invoice/debug_mode";
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeInterface;

    public function __construct(
        LoggerInterface $logger,
        ScopeConfigInterface $scopeInterface
    ) {
        $this->logger = $logger;
        $this->scopeInterface = $scopeInterface;
    }

    public function logQInvoiceRequest($request)
    {
        if ($this->scopeInterface->getValue(self::DEBUG_MODE_CONFIG_PATH)) {
            $this->logger->debug($request);
        }
    }
}
