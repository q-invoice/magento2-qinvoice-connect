<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Helper;

use Magento\Store\Model\ScopeInterface;


class CustomerGroupCalculationMethodHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CALCULATION_CUSTOMER_GROUP_RULES = 'calculation/general/customer_group_rules';

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
        $groupRules = $this->scopeConfig->getValue(self::CALCULATION_CUSTOMER_GROUP_RULES, ScopeInterface::SCOPE_STORE, $this->getStoreid());

        if ($groupRules == '' || $groupRules == null)
            return;

        $unserializedata = $this->serialize->unserialize($groupRules);

        $groupRulesArray = array();
        foreach ($unserializedata as $key => $row) {
            $groupRulesArray[$row['customer_group_id']] = $row['calculation_method'];
        }

        return $groupRulesArray;
    }

    public function getCalculationMethodForCustomerGroup($customerGroupId)
    {
        if(is_null($customerGroupId)){
            $customerGroupId = 0;
        }
        $groupRulesArray = $this->getCustomerGroupRules();
        return isset($groupRulesArray[$customerGroupId]) ? $groupRulesArray[$customerGroupId] : null;
    }
}