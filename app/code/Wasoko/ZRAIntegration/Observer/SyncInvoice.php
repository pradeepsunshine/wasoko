<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Wasoko\ZRAIntegration\Model\SyncInvoiceFactory;

class SyncInvoice implements ObserverInterface
{
    /**
     * @var SyncInvoiceFactory
     */
    private $syncInvoiceFactory;

    /**
     * @param SyncInvoiceFactory $syncInvoiceFactory
     */
    public function __construct(SyncInvoiceFactory $syncInvoiceFactory)
    {
       $this->syncInvoiceFactory = $syncInvoiceFactory;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        if (!$invoice->getZraIsSynced()) {
            $this->syncInvoiceFactory->create()->sync($invoice->getEntityId());
        }
    }
}
