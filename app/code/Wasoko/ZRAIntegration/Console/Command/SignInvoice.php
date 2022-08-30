<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Console\Command;

use Wasoko\ZRAIntegration\Model\SyncInvoiceToZraFactory;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SignInvoice extends Command
{
    /**
     * @var SyncInvoiceToZraFactory
     */
    private $syncInvoiceFactory;

    /**
     * @var State
     */
    protected $state;

    /**
     * @param SyncInvoiceToZraFactory $syncInvoiceFactory
     * @param State $state
     */
    public function __construct(
        SyncInvoiceToZraFactory $syncInvoiceFactory,
        State $state
    ) {
        $this->syncInvoiceFactory = $syncInvoiceFactory;
        $this->state = $state;
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('wasoko:zrasync:invoice')
            ->setDescription('Sync all unsynced invoices to ZRA (last 24 hours).');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_CRONTAB);
        $this->syncInvoiceFactory->create()->sync();
    }
}
