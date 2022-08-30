<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Api;

interface ZraInvoiceManagementInterface
{

    /**
     * Function to postZraInvoice.
     * @param mixed $params
     * @return mixed
     * @api
     */
    public function postZraInvoice($params);
}
