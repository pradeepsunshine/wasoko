<?php
declare(strict_types=1);

namespace Wasoko\ZRAIntegration\Model;

use Magento\Framework\App\ResourceConnection;



class TaxCategoryByOrder
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
     * @param $orderId
     * @return mixed|string
     */
    public function getOrderTaxCode($orderId)
    {
        $connection = $this->resCon->getConnection();
        $orderTaxTable = $this->resCon->getTableName(self::ORDER_TAX_TABLE);
        $select = $connection->select()
            ->from(
                ['order' => $orderTaxTable],
                ['title']
            )->joinLeft(
                ['tax_item' => self::ORDER_TAX_ITEM_TABLE],
                'order.tax_id = tax_item.tax_id'
            )->where('order.order_id = :orderid');

        $bind = ['orderid' => $orderId];

        $records = $connection->fetchAll($select, $bind);

        $taxItemIdTaxCodeArr = [];
        if (count($records)) {
            foreach ($records as $record) {
                $taxItemIdTaxCodeArr[$record['item_id']] = $record['title'];
            }
        }
        return $taxItemIdTaxCodeArr;
    }
}