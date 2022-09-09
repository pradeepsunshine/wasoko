<?php
declare(strict_types=1);

namespace Wasoko\Invoice\Plugin\Api;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Framework\App\ResourceConnection;

class InvoiceRepository
{
    const ORDER_TAX_TABLE = 'sales_order_tax';
    const ORDER_TAX_ITEM_TABLE = 'sales_order_tax_item';

    /**
     * @var ResourceConnection
     */
    protected $resCon;

    /**
     * @param ResourceConnection $resCon
     */
    public function __construct(
        ResourceConnection                   $resCon
    )
    {
        $this->resCon = $resCon;
    }

    /**
     * @param InvoiceRepositoryInterface $subject
     * @param InvoiceInterface $invoice
     * @return InvoiceInterface
     */
    public function afterGet(InvoiceRepositoryInterface $subject, InvoiceInterface $invoice)
    {
        $extensionAttributes = $invoice->getExtensionAttributes(); /** get current extension attributes from entity **/
        $order = $invoice->getOrder();
        $dataArr = [];
        $customerName = $order->getCustomerFirstname() . ' ' . $order->getCustomerMiddlename() . ' ' . $order->getCustomerLastname();
        $dataArr['BuyerTPIN'] = $order->getData('customer_taxvat');
        $dataArr['BuyerName'] = $customerName;
        $dataArr['BuyerTaxAccountName'] = $customerName;
        $billingAddress = $order->getBillingAddress();
        $street = $billingAddress->getStreet();
        $dataArr['BuyerAddress'] =  $street[0]  . ', '
            .$billingAddress->getRegion() . ', ' . $billingAddress->getCountryId(). ',' . $billingAddress->getPostcode();
        $dataArr['BuyerTel'] = $order->getBillingAddress()->getTelephone();
        $dataJson = json_encode($dataArr);
        $extensionAttributes->setInvoiceZraData($dataJson);
        $invoice->setExtensionAttributes($extensionAttributes);
        $this->addTaxLabelExtensionAttribute($invoice);
        return $invoice;
    }

    /**
     * @param $orderId
     * @return mixed|string
     */
    protected function getOrderTaxCode($orderId)
    {
        $connection = $this->resCon->getConnection();
        $orderTaxTable = $this->resCon->getTableName(self::ORDER_TAX_TABLE);
        $select = $connection->select()
            ->from(
                ['order' => $orderTaxTable],
                ['code']
            )->joinLeft(
                ['tax_item' => self::ORDER_TAX_ITEM_TABLE],
                'order.tax_id = tax_item.tax_id'
            )->where('order.order_id = :orderid');

        $bind = ['orderid' => $orderId];

        $records = $connection->fetchAll($select, $bind);

        $taxItemIdTaxCodeArr = [];
        if (count($records)) {
            foreach ($records as $record) {
                $taxItemIdTaxCodeArr[$record['item_id']] = $record['code'];
            }
        }
        return $taxItemIdTaxCodeArr;
    }

    /**
     * @param $order
     * @return void
     */
    private function addTaxLabelExtensionAttribute($invoice)
    {
        $itemsArr = [];
        $orderId = $invoice->getOrder()->getEntityId();
        $taxCodeArr = $this->getOrderTaxCode($orderId);
        $itemsCollection = $invoice->getItems();
        if ($itemsCollection) {
            foreach ($itemsCollection as $item) {
                $taxCode = $taxCodeArr[$item->getOrderItem()->getId()] ?? 'NO TAX CODE FOUND';
                $extensionAttributes = $item->getExtensionAttributes();
                $isMtv = $item->getOrderItem()->getProduct()->getIsMtv();
                $mtv = $item->getOrderItem()->getProduct()->getMtv();
                $mtvDataArr = [
                    'is_mtv' => $isMtv,
                    'mtv' => $mtv
                ];
                $extensionAttributes->setMtvData(json_encode($mtvDataArr));
                $extensionAttributes->setTaxLabels($taxCode);
                $item->setExtensionAttributes($extensionAttributes);
            }
        }
        return $itemsArr;
    }
}
