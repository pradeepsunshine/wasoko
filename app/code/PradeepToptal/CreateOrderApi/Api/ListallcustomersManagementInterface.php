<?php
declare(strict_types=1);

namespace PradeepToptal\CreateOrderApi\Api;

/**
 * Interface ListallcustomersManagementInterface
 * @package PradeepToptal\CreateOrderApi\Api
 */
interface ListallcustomersManagementInterface
{

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListallcustomers();
}

