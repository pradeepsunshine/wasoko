<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wasoko\Invoice\Model;

use Magento\Framework\Model\AbstractModel;
use Wasoko\Invoice\Api\Data\InvoiceItemsInterface;

class InvoiceItems extends AbstractModel
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Wasoko\Invoice\Model\ResourceModel\InvoiceItems::class);
    }
}

