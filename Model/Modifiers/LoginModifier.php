<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Modifiers;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\ScopeInterface;
use Qinvoice\Connect\Api\ModifierInterface;
use Qinvoice\Connect\Model\Document;
use Qinvoice\Connect\Service\ModuleVersion;

class LoginModifier implements ModifierInterface
{
    use AddCdata;
    const DOCUMENT_TYPE_CONFIG_KEY = 'invoice_options/invoice/document_type';
    const API_USERNAME_CONFIG_KEY = 'invoice_options/invoice/api_username';
    const API_PASSWORD_CONFIG_KEY = 'invoice_options/invoice/api_password';
    const IDENTIFIER = 'Magento';
    const PARENT_NODE = "login";

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ModuleVersion
     */
    private $moduleVersion;
    /**
     * LoginModifier constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ModuleVersion $moduleVersion
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ModuleVersion $moduleVersion

    ) {
        $this->scopeConfig = $scopeConfig;
        $this->moduleVersion = $moduleVersion;
    }

    /**
     * @param Document $document
     * @param OrderInterface $order
     * @param bool $isPaid
     * @return Document
     */
    public function modify(Document $document, OrderInterface $order, $isPaid = false)
    {
        $documentType = $this->scopeConfig->getValue(
            self::DOCUMENT_TYPE_CONFIG_KEY,
            ScopeInterface::SCOPE_STORE
        );

        $username = $this->scopeConfig->getValue(
            self::API_USERNAME_CONFIG_KEY,
            ScopeInterface::SCOPE_STORE
        );
        $password = $this->scopeConfig->getValue(
            self::API_PASSWORD_CONFIG_KEY,
            ScopeInterface::SCOPE_STORE
        );

        $login = [
            '@attributes' => [
                'mode' => 'new' . ucfirst($documentType),
            ],
            'username' => $this->addCDATA($username),
            'password' => $this->addCDATA($password),
            'identifier' => $this->addCDATA(self::IDENTIFIER .' '. $this->moduleVersion->get()),
        ];

        $document->addItem(self::PARENT_NODE, $login);
        return $document;
    }
}
