<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\Manager;

class MultisafepayConfigIntegration
{
    const PAYMENT_CONFIG_PATH_PATTERN = "gateways/%s/active";

    /**
     * @var Data
     */
    private $data;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * MultisafepayConfigIntegration constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Manager $moduleManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Manager $moduleManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->moduleManager = $moduleManager;
    }

    public function addActiveMethods($methods) {
        if ($this->moduleManager->isEnabled('MultiSafepay_Connect') === false) {
            return $methods;
        }
        foreach ($this->getDataHelper()->getAllMethods() as $code => $name) {
            $active = $this->scopeConfig->getValue(sprintf(self::PAYMENT_CONFIG_PATH_PATTERN, $code));
            if ($active) {
                $methods[$code] = [
                    'label' => $name,
                    'value' => $code,
                ];
            }
        }

        return $methods;
    }

    /**
     * Return DataHelper if module exists and enabled
     * @return mixed|Data
     */
    private function getDataHelper()
    {
        if ($this->data === null) {
            $this->data = \Magento\Framework\App\ObjectManager::getInstance()->get('MultiSafepay\Connect\Helper\Data');
        }

        return $this->data;
    }
}
