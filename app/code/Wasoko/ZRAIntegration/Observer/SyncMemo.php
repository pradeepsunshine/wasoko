<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Wasoko\ZRAIntegration\Model\SyncMemoFactory;

class SyncMemo implements ObserverInterface
{
    /**
     * @var SyncMemoFactory
     */
    private $syncMemoFactory;

    /**
     * @param SyncMemoFactory $syncMemoFactory
     */
    public function __construct(SyncMemoFactory $syncMemoFactory)
    {
        $this->syncMemoFactory = $syncMemoFactory;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $memo = $observer->getEvent()->getCreditmemo();
        if (!$memo->getZraIsSynced()) {
            $this->syncMemoFactory->create()->sync($memo->getEntityId());
        }
    }
}
