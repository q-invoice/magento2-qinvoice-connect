<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Data\OptionSourceInterface;

class CustomerGroup implements OptionSourceInterface
{
    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(
        GroupRepositoryInterface $groupRepository
    ) {
        $this->groupRepository = $groupRepository;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $customerGroups = $this->groupRepository->getList(\Magento\Customer\Model\Group::CUST_GROUP_ALL);
        $options = [];
        foreach ($customerGroups as $customerGroup) {
            $options[] = [
                'label' => $customerGroup->getCode(),
                'value' => $customerGroup->getId()
            ];
        }
        return $options;
    }
}




