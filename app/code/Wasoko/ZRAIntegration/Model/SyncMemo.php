<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Model;

class SyncMemo extends AbstractEntity
{
    const ENABLE_CONFIG_PATH = 'wasoko_zra/api_invoice_return/enabled';

    public function sync($memoId = 4)
    {
        if ($this->configHelper->getConfig(self::ENABLE_CONFIG_PATH)) {
            $params = [];
            $this->logger->info($memoId.'---MEMO Sync process Initiated.');
            $data = [
                'id' =>  $memoId,
                'type' => 'CreditMemo'
            ];
            $serializedData = $this->json->serialize($data);
            $params['body'] = $serializedData;
            $response = $this->callService($params, \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST);
            $this->logger->info($memoId.'---Response captured :'. $response->getContent());
            $this->zraInvoiceManagementFactory->create()->logApiDataAndUpdateMemo($response, $memoId, $this->logger,'creditmemo');
        } else {
            $this->logger->info('Memo Sync could ot be started as it is marked as disabled in admin config.');
        }
    }
}
