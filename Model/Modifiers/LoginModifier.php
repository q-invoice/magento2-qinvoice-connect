<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Modifiers;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Qinvoice\Connect\Api\ModifierInterface;
use Qinvoice\Connect\Model\Document;

class LoginModifier implements ModifierInterface
{
    use AddCdata;
    const DOCUMENT_TYPE_CONFIG_KEY = 'invoice_options/invoice/document_type';
    const API_USERNAME_CONFIG_KEY = 'invoice_options/invoice/api_username';
    const API_PASSWORD_CONFIG_KEY = 'invoice_options/invoice/api_password';

    const IDENTIFIER = 'Magento_2.2.3';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * LoginModifier constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param Document $document
     * @return Document
     */
    public function modify(Document $document)
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
            'identifier' => $this->addCDATA(self::IDENTIFIER),
        ];

        $document->addItem('login', $login);
        return $document;
    }
}
