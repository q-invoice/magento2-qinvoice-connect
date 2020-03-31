<?php


namespace Qinvoice\Connect\Setup\Patch\Data;


use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Math\Random;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class GenerateSecretKey implements DataPatchInterface
{
    const SECRET_KEY_CONFIG_PATH = 'invoice_options/invoice/webshop_secret';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var ConfigInterface
     */
    private $configWriter;
    /**
     * @var Random
     */
    private $random;

    /**
     * GenerateSecretKey constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ScopeConfigInterface $scopeConfig
     * @param ConfigInterface $configWriter
     * @param Random $random
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ScopeConfigInterface $scopeConfig,
        ConfigInterface $configWriter,
        Random $random
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->random = $random;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        if (!$this->scopeConfig->getValue(self::SECRET_KEY_CONFIG_PATH)) {
            $this->configWriter->saveConfig(
                self::SECRET_KEY_CONFIG_PATH, $this->random->getRandomString(32)
            );
        }

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
