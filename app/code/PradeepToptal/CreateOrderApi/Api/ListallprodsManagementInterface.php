<?php
declare(strict_types=1);

namespace PradeepToptal\CreateOrderApi\Api;

/**
 * Interface ListallprodsManagementInterface
 * @package PradeepToptal\CreateOrderApi\Api
 */
interface ListallprodsManagementInterface
{

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getListallprods();
}

