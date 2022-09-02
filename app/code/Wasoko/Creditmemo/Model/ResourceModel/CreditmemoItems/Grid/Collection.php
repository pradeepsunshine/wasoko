<?php
declare(strict_types=1);

namespace Wasoko\Creditmemo\Model\ResourceModel\CreditmemoItems\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Wasoko\Invoice\Model\ResourceModel\InvoiceItems\Collection as EntityCollection;

class Collection extends SearchResult
{
    protected function _initSelect()
    {
        $this->addFilterToMap('created_at', 'creditmemo.created_at');
        parent::_initSelect();
        $this->getSelect()
            ->join(
                [
                    'creditmemo' => 'sales_creditmemo_grid'
                ],
                'main_table.parent_id = creditmemo.entity_id',
                [
                    'increment_id',
                    'state',
                    'store_id',
                    'order_id',
                    'order_increment_id',
                    'order_created_at',
                    'customer_name',
                    'customer_email',
                    'customer_group_id',
                    'payment_method',
                    'billing_name',
                    'billing_address',
                    'shipping_address',
                    'shipping_information',
                    'subtotal',
                    'shipping_and_handling',
                    'created_at',
                    'updated_at',
                    'base_grand_total',
                    'zra_invoice_number'
                ]
            )->join(
                [
                    'orderitems' => 'sales_order_item'
                ],
                'main_table.order_item_id=orderitems.item_id',
                ['tax_percent']
            )->where('main_table.price IS NOT NULL');
    }
}
