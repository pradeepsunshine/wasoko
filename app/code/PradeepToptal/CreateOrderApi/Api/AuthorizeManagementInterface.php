<?php
declare(strict_types=1);

namespace PradeepToptal\CreateOrderApi\Api;

/**
 * Interface AuthorizeManagementInterface
 * @package PradeepToptal\CreateOrderApi\Api
 */
interface AuthorizeManagementInterface
{
    /**
     * POST for authorize api
     * @param string $user
     * @param string $password
     * @return string
     */
    public function authorizeAdminUser($user, $password);
}
