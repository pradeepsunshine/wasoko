<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Cron;

use Wasoko\ZRAIntegration\Model\SyncCreditmemoToZraFactory;

class SyncMemoToZra
{
    /**
     * @var SyncMemoToZraFactory
     */
    private $syncMemoToZraFactory;

    /**
     * @param SyncMemoToZraFactory $syncMemoToZraFactory
     */
    public function __construct(
        SyncMemoToZraFactory $syncMemoToZraFactory
    ) {
        $this->syncMemoToZraFactory = $syncMemoToZraFactory;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->syncMemoToZraFactory->create()->sync();
    }
}
