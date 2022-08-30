<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Model;

class SyncInvoice extends AbstractEntity
{
    const ENABLE_CONFIG_PATH = 'wasoko_zra/api_invoice_signin/enabled';

    /**
     * @param $invoiceId
     * @return void
     */
    public function sync($invoiceId = 24)
    {
        if ($this->configHelper->getConfig(self::ENABLE_CONFIG_PATH)) {
            $this->logger->info($invoiceId.'---Invoice Sync process Initiated.');
            $params = [];
            $this->logger->info($invoiceId.'---Invoice loaded with increment ID .'. $invoiceId);
            $data = [
                'id' => $invoiceId,
                'type' => 'Invoice'
            ];
            $serializedData = $this->json->serialize($data);
            $params['body'] = $serializedData;
            $this->logger->info($invoiceId.'---Data Sent .'. print_r($params, true));
            $response = $this->callService($params, \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST);
            $this->logger->info($invoiceId.'---Response captured :'. $response->getContent());
            $this->zraInvoiceManagementFactory->create()->logApiDataAndUpdateInvoice($response, $invoiceId, $this->logger, 'invoice');
        } else {
            $this->logger->info('Invoice Sync could ot be started as it is marked as disabled in admin config.');
        }
    }
}
