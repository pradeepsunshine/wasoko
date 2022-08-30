<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Model;

use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory;
use Wasoko\ZRAIntegration\Helper\Config;
use Wasoko\ZRAIntegration\Model\SyncInvoiceFactory;

class SyncInvoiceToZra
{
    const IS_SYNCED_FIELD = 'zra_is_synced';
    const CREATED_AT_FIELD_NAME = 'created_at';

    /**
     * @var CollectionFactory
     */
    private $invoiceCollectionFactory;

    /**
     * @var \Wasoko\ZRAIntegration\Model\SyncInvoiceFactory
     */
    private $syncInvoiceFactory;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @param CollectionFactory $invoiceCollectionFactory
     * @param \Wasoko\ZRAIntegration\Model\SyncInvoiceFactory $syncInvoiceFactory
     */
    public function __construct(
        CollectionFactory  $invoiceCollectionFactory,
        SyncInvoiceFactory $syncInvoiceFactory,
        Config             $configHelper
    )
    {
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->syncInvoiceFactory = $syncInvoiceFactory;
        $this->configHelper = $configHelper;
    }

    /**
     * @return void
     */
    public function sync()
    {
        $invoiceCollection = $this->invoiceCollectionFactory->create()
            ->addFieldToFilter(self::IS_SYNCED_FIELD, 0)
            ->addFieldToFilter(self::CREATED_AT_FIELD_NAME,
                [
                    'from' => strtotime('-1 day', time()),
                    'to' => time(),
                    'datetime' => true
                ]
            )->addFieldToSelect('entity_id');
        if ($batchSize = $this->configHelper->getConfig(\Wasoko\ZRAIntegration\Model\AbstractEntity::CONFIG_BATCH_SIZE)) {
            $invoiceCollection->setPageSize($batchSize);
        }
        if ($invoiceCollection->getSize()) {
            foreach ($invoiceCollection as $invoice) {
                $this->syncInvoiceFactory->create()->sync($invoice->getEntityId());
            }
        }
    }
}
