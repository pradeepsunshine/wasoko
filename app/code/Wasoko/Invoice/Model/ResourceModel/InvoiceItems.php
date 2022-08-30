<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wasoko\Invoice\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class InvoiceItems extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales_invoice_item', 'entity_id');
    }
}

