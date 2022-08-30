<?php
declare(strict_types=1);

namespace Wasoko\Creditmemo\Model\ResourceModel\CreditmemoItems;

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
            \Wasoko\Invoice\Model\CreditmemoItems::class,
            \Wasoko\Invoice\Model\ResourceModel\CreditmemoItems::class
        );
    }

}
