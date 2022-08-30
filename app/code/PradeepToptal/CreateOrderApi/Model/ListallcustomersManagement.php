<?php
declare(strict_types=1);

namespace PradeepToptal\CreateOrderApi\Model;

/**
 * Class ListallcustomersManagement
 * @package PradeepToptal\CreateOrderApi\Model
 */
class ListallcustomersManagement implements \PradeepToptal\CreateOrderApi\Api\ListallcustomersManagementInterface
{
    /**
     * @var \Magento\Customer\Model\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroup
     */
    private $filterGroup;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * ListallcustomersManagement constructor.
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $this->customerRepository = $customerRepository;
        $this->searchCriteria = $criteria;
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListallcustomers()
    {
        return $this->getCustomersData();
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCustomersData()
    {
        $customers = $this->customerRepository->getList($this->searchCriteria);
        $customersList = $customers->getItems();

        return $customersList;
    }
}

