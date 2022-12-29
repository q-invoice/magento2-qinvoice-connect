<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Group implements OptionSourceInterface
{
    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private \Magento\Framework\Api\FilterBuilder $filterBuilder;
    private \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder;


    public function __construct(
        Context $context,
        CustomerGroup $customerGroup
    )
    {
        $this->customerGroup = $customerGroup;
        parent::__construct($context);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        $customerGroups = $this->customerGroup->toOptionArray();
        return $customerGroups;
    }
}




