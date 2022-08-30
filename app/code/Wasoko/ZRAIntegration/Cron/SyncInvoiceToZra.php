<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Cron;

use Wasoko\ZRAIntegration\Model\SyncInvoiceToZraFactory;

class SyncInvoiceToZra
{
    /**
     * @var SyncInvoiceToZraFactory
     */
    private $syncInvoiceToZraFactory;

    /**
     * @param SyncInvoiceToZraFactory $syncInvoiceToZraFactory
     */
    public function __construct(
        SyncInvoiceToZraFactory $syncInvoiceToZraFactory
    ) {
        $this->syncInvoiceToZraFactory = $syncInvoiceToZraFactory;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->syncInvoiceToZraFactory->create()->sync();
    }
}
