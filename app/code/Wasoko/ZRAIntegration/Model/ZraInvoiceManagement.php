<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Model;

use Magento\Sales\Model\Order\InvoiceFactory;
use Wasoko\ZRAIntegration\Model\InvoiceFactory as ZraInvoiceFactory;
use Magento\Sales\Model\Order\Creditmemo;

class ZraInvoiceManagement implements \Wasoko\ZRAIntegration\Api\ZraInvoiceManagementInterface
{
    /**
     * @var InvoiceFactory
     */
    private $invoiceFactory;

    /**
     * @var \Wasoko\ZRAIntegration\Model\InvoiceFactory
     */
    private $zraInvoiceFactory;

    /**
     * @var Creditmemo
     */
    private $creditmemo;

    /**
     * @param InvoiceFactory $invoiceFactory
     * @param \Wasoko\ZRAIntegration\Model\InvoiceFactory $zraInvoiceFactory
     * @param Creditmemo $creditmemoFactory
     */
    public function __construct(
        InvoiceFactory $invoiceFactory,
        ZraInvoiceFactory $zraInvoiceFactory,
        Creditmemo $creditmemo
        ) {
        $this->_invoiceFactory = $invoiceFactory;
        $this->zraInvoiceFactory = $zraInvoiceFactory;
        $this->creditmemo = $creditmemo;
    }

    /**
     * Function to postZraInvoice.
     * @api
     * @param string $params
     * @return mixed
     */
    public function postZraInvoice($params)
    {

    }
     /**
     * Function to postZraInvoice.
     * @api
     * @param string $params
     * @return mixed
     */
    public function logApiDataAndUpdateInvoice($response, $entityId, $logger, $entityType)
    {
        $logger->info($entityId.'---Invoice management triggerd, time to save the response data.');

        $paramsArr = \Safe\json_decode($response->getContent(), true);
        $requestArr = \Safe\json_decode($response->getRequestBody(), true);
        try {
            if(!empty($paramsArr) && isset($paramsArr['data']) && isset($paramsArr['data']['tpin']))
            {
                $paramsArr = $paramsArr['data'];
                $logger->info($entityId.'---Saving Invoice data. Loading invoice.');
                $entityModel = $this->_invoiceFactory->create()->load($entityId);
                $logger->info('Saving Invoice data. invoice loaded with ID: '.$entityModel->getEntityId());
                $entityModel->setZraIsSynced(1);
                $vefdtime = $paramsArr['veFDTime'] ?? '';
                $terminalID = $paramsArr['terminalId'] ?? '';
                $invoiceCode = $paramsArr['invoiceCode'] ?? '';
                $fiscalCode = $paramsArr['fiscalCode'] ?? '';
                $invoiceNumber = $paramsArr['invoiceNumber'] ?? '';

                $entityModel->setZraTpin($paramsArr['tpin']);
                $entityModel->setZraVefdtime($vefdtime);
                $entityModel->setZraTerminalId($terminalID);
                $entityModel->setZraInvoiceCode($invoiceCode);
                $entityModel->setZraFiscalCode($fiscalCode);
                $entityModel->setZraInvoiceNumber($invoiceNumber);
                $entityModel->save();
                $logger->info($entityId.'---Saving Invoice data. Saving finished.');
            }
            $this->saveRawResponse($response, $entityId, $logger, $entityType);

        } catch (Exception $e) {

        }
        return;
    }

    /**
     * Function to postZraInvoice.
     * @api
     * @param string $params
     * @return mixed
     */
    public function logApiDataAndUpdateMemo($response, $entityId, $logger, $entityType)
    {
        $logger->info($entityId.'---MEMO management triggerd, time to save the response data.');

        $paramsArr = \Safe\json_decode($response->getContent(), true);
        $requestArr = \Safe\json_decode($response->getRequestBody(), true);
        
        try {
            if(!empty($paramsArr) && isset($paramsArr['data']) && isset($paramsArr['data']['tpin']))
            {
                $paramsArr = $paramsArr['data'];
                $logger->info($entityId.'---Saving MEMO data. Loading invoice.');

                $entityModel = $this->creditmemo->load($entityId);

                $vefdtime = $paramsArr['veFDTime'] ?? '';
                $terminalID = $paramsArr['terminalId'] ?? '';
                $invoiceCode = $paramsArr['invoiceCode'] ?? '';
                $fiscalCode = $paramsArr['fiscalCode'] ?? '';
                $invoiceNumber = $paramsArr['invoiceNumber'] ?? '';

                $entityModel->setZraIsSynced(1);
                $entityModel->setZraTpin($paramsArr['tpin']);
                $entityModel->setZraVefdtime($vefdtime);
                $entityModel->setZraTerminalId($terminalID);
                $entityModel->setZraInvoiceCode($invoiceCode);
                $entityModel->setZraFiscalCode($fiscalCode);
                $entityModel->setZraInvoiceNumber($invoiceNumber);
                $entityModel->save();
                $logger->info($entityId.'---Saving MEMO data. Saving finished.');
            }
            $this->saveRawResponse($response, $entityId, $logger, $entityType);
        } catch (Exception $e) {

        }
    }

    /**
     * @param $params
     * @return void
     */
    private function saveRawResponse($response, $entityId, $logger, $entityType)
    {
        $logger->info($entityId.'---Saving Respose Data data. Starting.');
        $requestBody = $response->getRequestBody();
        $requestArr = \Safe\json_decode($requestBody, true);
        $zraInvoiceLog = $this->zraInvoiceFactory->create();
        $zraInvoiceLog->setEntityId($entityId);
        $zraInvoiceLog->setEntityType($entityType);
        $zraInvoiceLog->setBoomiZraResponse($response->getContent());
        $zraInvoiceLog->setResponseError($response->getErrorReason());
        $zraInvoiceLog->setResponseRequest($response->getRequestBody());
        $zraInvoiceLog->setResponseCode($response->getStatus());
        $zraInvoiceLog->save();
        $logger->info($entityId.'----Saving Respose Data data. FINISHED.');
    }
}

