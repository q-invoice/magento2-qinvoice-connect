<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Data\OptionSourceInterface;
use \Magento\Framework\Api\FilterBuilder;
use \Magento\Framework\Api\Search\FilterGroupBuilder;

class CustomerGroup implements OptionSourceInterface
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


    /**
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder
    )
    {
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {


        // Create a SearchCriteriaBuilder object
        $searchCriteriaBuilder = $this->searchCriteriaBuilder->create();


        $filter_1 = $this->filterBuilder
            ->setField('customer_group_id')
            ->setConditionType('neq')
            ->setValue(\Magento\Customer\Model\Group::CUST_GROUP_ALL)
            ->create();

        $filter_group = $this->filterGroupBuilder
            ->addFilter($filter_1)
            ->create();

        // Set the filters for the search criteria
        $searchCriteriaBuilder->setFilterGroups($filter_group);


        $customerGroups = $this->groupRepository->getList(SearchCriteria::PAGE_SIZE);
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




