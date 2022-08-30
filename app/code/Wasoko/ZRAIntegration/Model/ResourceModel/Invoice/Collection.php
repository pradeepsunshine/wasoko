<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Model\ResourceModel\Invoice;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Wasoko\ZRAIntegration\Model\Invoice::class,
            \Wasoko\ZRAIntegration\Model\ResourceModel\Invoice::class
        );
    }
}

