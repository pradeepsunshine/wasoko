<?php
declare(strict_types=1);

namespace PradeepToptal\CreateOrderApi\Api;

/**
 * Interface CreateorderManagementInterface
 * @package PradeepToptal\CreateOrderApi\Api
 */
interface CreateorderManagementInterface
{

    /**
     * POST for createorder api
     * @param string $param
     * @return string
     */
    public function postCreateorder($param);
}

