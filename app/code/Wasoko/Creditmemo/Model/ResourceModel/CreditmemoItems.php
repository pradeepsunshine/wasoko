<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wasoko\Creditmemo\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CreditmemoItems extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales_creditmemo_item', 'entity_id');
    }
}

