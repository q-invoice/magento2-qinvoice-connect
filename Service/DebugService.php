<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class DebugService
{

    const DEBUG_MODE_CONFIG_PATH = "debug/general/debug_mode";
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

    // fallback
    public function logQInvoiceRequest($request)
    {
        $this->debug($request, []);
    }

    public function debug($message, array $context = array())
    {
        if ($this->scopeInterface->getValue(self::DEBUG_MODE_CONFIG_PATH)) {
            $this->logger->debug($message, $context);
        }
    }
}
