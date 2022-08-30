<?php
declare(strict_types=1);

namespace Wasoko\Invoice\Model\ResourceModel\InvoiceItems;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Wasoko\Invoice\Model\InvoiceItems::class,
            \Wasoko\Invoice\Model\ResourceModel\InvoiceItems::class
        );
    }

}
