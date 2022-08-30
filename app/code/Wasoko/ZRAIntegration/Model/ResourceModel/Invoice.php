<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Invoice extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('wasoko_zraintegration_invoice', 'id');
    }
}

