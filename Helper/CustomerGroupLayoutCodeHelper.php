<?php


namespace Qinvoice\Connect\Helper;

use Magento\Store\Model\ScopeInterface;


class CustomerGroupLayoutCodeHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    const LAYOUT_CUSTOMER_GROUP_RULES = 'layout/general/customer_group_rules';

    protected $storeManager;
    protected $serialize;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $serialize)
    {
        $this->storeManager = $storeManager;
        $this->serialize = $serialize;
        parent::__construct($context);
    }

    public function getStoreid()
    {
        return $this->storeManager->getStore()->getId();
    }


    public function getCustomerGroupRules()
    {
        $groupRules = $this->scopeConfig->getValue(self::LAYOUT_CUSTOMER_GROUP_RULES, ScopeInterface::SCOPE_STORE, $this->getStoreid());

        if ($groupRules == '' || $groupRules == null)
            return;

        $unserializedata = $this->serialize->unserialize($groupRules);

        $groupRulesArray = array();
        foreach ($unserializedata as $key => $row) {
            $groupRulesArray[$row['customer_group_id']] = $row['layout_code'];
        }

        return $groupRulesArray;
    }

    public function getLayoutCodeForCustomerGroup($customerGroupId)
    {
        $groupRulesArray = $this->getCustomerGroupRules();
        return isset($groupRulesArray[$customerGroupId]) ? $groupRulesArray[$customerGroupId] : '00000';
    }
}