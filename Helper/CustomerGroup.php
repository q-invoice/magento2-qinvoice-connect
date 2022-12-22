<?php

namespace Qinvoice\Connect\Helper;

use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    protected $customerGroup;

    public function __construct(
        Context $context,
        CustomerGroup $customerGroup
    )
    {
        $this->customerGroup = $customerGroup;
        parent::__construct($context);
    }

    public function getCustomerGroups()
    {
        $customerGroups = $this->customerGroup->toOptionArray();
        return $customerGroups;
    }
}