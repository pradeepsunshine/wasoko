<?php
declare(strict_types=1);

namespace AC\MassOrderAction\Model;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Convert\OrderFactory;

class MassOrderProcess
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var InvoiceService
     */
    protected $invoiceService;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var OrderFactory
     */
    protected $convertOrder;


    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param InvoiceService $invoiceService
     * @param Transaction $transaction
     * @param OrderFactory $convertOrder
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        InvoiceService           $invoiceService,
        Transaction              $transaction,
        OrderFactory             $convertOrder
    )
    {
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->convertOrder = $convertOrder;
    }

    /**
     * @param $orderIds
     * @return string
     * @throws \Exception
     */
    public function processInvoice($orderIds = [])
    {
        $errorMessage = '';

        foreach ($orderIds as $orderId) {
            $order = $this->orderRepository->get($orderId);

            if ($order->canInvoice()) {
                try {
                    $invoice = $this->invoiceService->prepareInvoice($order);
                    $invoice->register();
                    $invoice->save();

                    $transactionSave =
                        $this->transaction
                            ->addObject($invoice)
                            ->addObject($invoice->getOrder());
                    $transactionSave->save();
                } catch (\Exception $e) {
                    $errorMessage .= 'Order ID ' . $order->getIncrementId() . ' Can not be invoiced. ';
                }
                $order->addCommentToStatusHistory(
                    __('Invoice egenrated by Mass Action: ', $invoice->getId())
                )->setIsCustomerNotified(false)->save();
                $order->setIsInProcess(true)->save();
            } else {
                $errorMessage .= 'Order ID ' . $order->getIncrementId() . ' Can not be invoiced. ';
            }
        }
        return $errorMessage;
    }

    /**
     * @param $orderIds
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processShipments($orderIds = [])
    {
        $errorMessage = '';
        foreach ($orderIds as $orderId) {
            $order = $this->orderRepository->get($orderId);
            if (!$order->canShip()) {
                $errorMessage .= 'Order ID ' . $order->getIncrementId() . ' Can not be Shipped. ';
                continue;
            }
            $orderShipment = $this->convertOrder->create()->toShipment($order);

            foreach ($order->getAllItems() as $orderItem) {

                // Check virtual item and item Quantity
                if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }

                $qty = $orderItem->getQtyToShip();
                $shipmentItem = $this->convertOrder->create()->itemToShipmentItem($orderItem)->setQty($qty);

                $orderShipment->addItem($shipmentItem);
            }
            $orderShipment->register();


            try {
                $orderShipment->save();
                $orderShipment->getOrder()->save();
            } catch (\Exception $e) {
                $errorMessage .= 'Exception: Order ID ' . $order->getIncrementId() . ' Can not be Shipped. ';
            }
        }
        return $errorMessage;
    }

    /**
     * @param $orderIds
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processInvoiceAndShipments($orderIds = [])
    {
        $this->processInvoice($orderIds);
        $this->processShipments($orderIds);
    }
}

