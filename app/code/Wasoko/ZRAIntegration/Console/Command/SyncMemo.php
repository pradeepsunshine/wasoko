<?php

namespace Wasoko\ZRAIntegration\Console\Command;

use Wasoko\ZRAIntegration\Model\SyncCreditmemoToZraFactory;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncMemo extends Command
{
    /**
     * @var SyncCreditmemoToZraFactory
     */
    private $syncMemoFactory;

    /**
     * @var State
     */
    protected $state;

    /**
     * @param SyncCreditmemoToZraFactory $syncMemoFactory
     * @param State $state
     */
    public function __construct(
        SyncCreditmemoToZraFactory $syncMemoFactory,
        State $state
    ) {
        $this->syncMemoFactory = $syncMemoFactory;
        $this->state = $state;
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('wasoko:zrasync:memo')
            ->setDescription('Sync all unsynced credit memos to ZRA (last 24 hours).');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_CRONTAB);
        $this->syncMemoFactory->create()->sync();
    }
}
