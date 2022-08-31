<?php

namespace Wasoko\Creditmemo\Plugin\Api;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;

class CreditmemoRepository
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
    public function afterGet(CreditmemoRepositoryInterface $subject, CreditmemoInterface $memo)
    {
        $originalInvoiceCode = '';
        $originalInvoiceNumber = '';
        $extensionAttributes = $memo->getExtensionAttributes(); /** get current extension attributes from entity **/
        $this->addTaxLabelExtensionAttribute($memo);
        return $memo;
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
    private function addTaxLabelExtensionAttribute($memo)
    {
        $itemsArr = [];
        $orderId = $memo->getOrder()->getEntityId();
        $taxCodeArr = $this->getOrderTaxCode($orderId);
        $itemsCollection = $memo->getItems();
        if ($itemsCollection) {
            foreach ($itemsCollection as $item) {
                $taxCode = $taxCodeArr[$item->getOrderItem()->getId()] ?? 'NO TAX CODE FOUND';
                $extensionAttributes = $item->getExtensionAttributes();
                $isMtv = $item->getProduct()->getIsMtv();
                $extensionAttributes->setIsMtv($isMtv);
                $extensionAttributes->setTaxLabels($taxCode);
                $item->setExtensionAttributes($extensionAttributes);
            }
        }
        return $itemsArr;
    }
}
