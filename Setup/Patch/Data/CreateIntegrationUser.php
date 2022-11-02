<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Setup\Patch\Data;

use Magento\Config\Model\Config as SystemConfig;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Integration\Api\IntegrationServiceInterface;
use Magento\Integration\Api\OauthServiceInterface;
use Magento\Integration\Model\ConfigBasedIntegrationManager;

class CreateIntegrationUser implements DataPatchInterface
{
    const Q_INVOICE_INTEGRATION_NAME = 'q-invoice';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var ConfigBasedIntegrationManager
     */
    private $configBasedIntegrationManager;

    /**
     * @var IntegrationServiceInterface
     */
    private $integrationService;

    /**
     * @var OauthServiceInterface
     */
    private $oauthService;

    /**
     * GenerateSecretKey constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ConfigBasedIntegrationManager $configBasedIntegrationManager,
        IntegrationServiceInterface $integrationService,
        OauthServiceInterface $oauthService
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->configBasedIntegrationManager = $configBasedIntegrationManager;
        $this->integrationService = $integrationService;
        $this->oauthService = $oauthService;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        try{
            $this->configBasedIntegrationManager->processConfigBasedIntegrations([(string)self::Q_INVOICE_INTEGRATION_NAME => 1]);
        }catch(Exception $e){
            echo $e->getMessage();
        }
        $integration = $this->integrationService->findByName(self::Q_INVOICE_INTEGRATION_NAME);
        $consumerId = $integration->getConsumerId();

        $accessToken = $this->oauthService->getAccessToken($consumerId);
        if (!$accessToken && $this->oauthService->createAccessToken($consumerId, true)) {
            $this->oauthService->getAccessToken($consumerId);
        }

        $integration->setStatus(\Magento\Integration\Model\Integration::STATUS_ACTIVE);
        $this->integrationService->update($integration->getData());

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
