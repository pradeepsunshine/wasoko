<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Model;

use Wasoko\ZRAIntegration\Service\EsdZRAService;
use Wasoko\ZRAIntegration\Logger\Logger;
use Wasoko\ZRAIntegration\Helper\Config;
use Wasoko\ZRAIntegration\Model\ZraInvoiceManagementFactory;
use Magento\Framework\Serialize\Serializer\Json;


class AbstractEntity
{
    const CONFIG_BATCH_SIZE = 'wasoko_zra/api/api_batch_size';
    /**
     * @var EsdZRAService
     */
    protected $esdZRAService;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var ZraInvoiceManagement
     */
    protected $zraInvoiceManagementFactory;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @param EsdZRAService $esdZRAService
     * @param Logger $logger
     * @param \Wasoko\ZRAIntegration\Model\ZraInvoiceManagementFactory $zraInvoiceManagementFactory
     * @param Json $json
     * @param Config $configHelper
     */
    public function __construct(
        EsdZRAService                        $esdZRAService,
        Logger                               $logger,
        ZraInvoiceManagementFactory          $zraInvoiceManagementFactory,
        Json                                 $json,
        Config                               $configHelper
    )
    {
        $this->esdZRAService = $esdZRAService;
        $this->logger = $logger;
        $this->configHelper = $configHelper;
        $this->json = $json;
        $this->zraInvoiceManagementFactory = $zraInvoiceManagementFactory;
    }

    /**
     * @param $uri
     * @param $param
     * @param $method
     * @return void
     */
    protected function callService($param, $method)
    {
        return $this->esdZRAService->execute($param, $method);
    }
}
