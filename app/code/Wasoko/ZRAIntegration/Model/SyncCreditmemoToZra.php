<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Model;

use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory;
use Wasoko\ZRAIntegration\Model\SyncMemoFactory;

class SyncCreditmemoToZra
{
    const IS_SYNCED_FIELD = 'zra_is_synced';
    const CREATED_AT_FIELD_NAME = 'created_at';

    /**
     * @var CollectionFactory
     */
    private $memoCollectionFactory;

    /**
     * @var \Wasoko\ZRAIntegration\Model\SyncMemoFactory
     */
    private $syncMemoFactory;

    /**
     * @param CollectionFactory $memoCollectionFactory
     * @param \Wasoko\ZRAIntegration\Model\SyncMemoFactory $syncMemoFactory
     */
    public function __construct(
        CollectionFactory $memoCollectionFactory,
        SyncMemoFactory $syncMemoFactory
    )
    {
        $this->memoCollectionFactory = $memoCollectionFactory;
        $this->syncMemoFactory = $syncMemoFactory;
    }

    /**
     * @return void
     */
    public function sync()
    {
        $memoCollection = $this->memoCollectionFactory->create()
            ->addFieldToFilter(self::IS_SYNCED_FIELD, 0)
            ->addFieldToFilter(self::CREATED_AT_FIELD_NAME,
                [
                    'from' => strtotime('-1 day', time()),
                    'to' => time(),
                    'datetime' => true
                ]
            )->addFieldToSelect('entity_id');
        if ($memoCollection->getSize()) {
            foreach ($memoCollection as $memo) {
                $this->syncMemoFactory->create()->sync($memo->getEntityId());
            }
        }
    }
}
