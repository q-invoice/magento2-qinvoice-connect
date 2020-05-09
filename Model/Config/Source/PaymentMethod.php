<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Option\ArrayInterface;
use \Magento\Payment\Model\Config;

class Paymentmethod extends DataObject implements ArrayInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_appConfigScopeConfigInterface;
    /**
     * @var Config
     */
    protected $_paymentModelConfig;

    /**
     * @var MultisafepayConfigIntegration
     */
    private $multisafepayConfigIntegration;

    /**
     * Paymentmethod constructor.
     * @param ScopeConfigInterface $appConfigScopeConfigInterface
     * @param Config $paymentModelConfig
     * @param MultisafepayConfigIntegration $multisafepayConfigIntegration
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $appConfigScopeConfigInterface,
        Config $paymentModelConfig,
        \Qinvoice\Connect\Model\Config\Source\MultisafepayConfigIntegration $multisafepayConfigIntegration,
        $data = []
    ) {
        parent::__construct($data);
        $this->_appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
        $this->_paymentModelConfig = $paymentModelConfig;
        $this->multisafepayConfigIntegration = $multisafepayConfigIntegration;
    }

    public function toOptionArray()
    {
        $payments = $this->_paymentModelConfig->getActiveMethods();
        $methods = [];
        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = $this->_appConfigScopeConfigInterface
                ->getValue('payment/' . $paymentCode . '/title');
            $methods[$paymentCode] = [
                'label' => $paymentTitle,
                'value' => $paymentCode,
            ];
        }

        $methods = $this->multisafepayConfigIntegration->addActiveMethods($methods);

        return $methods;
    }
}
