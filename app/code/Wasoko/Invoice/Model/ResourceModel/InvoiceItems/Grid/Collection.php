<?php
declare(strict_types=1);

namespace Wasoko\Invoice\Model\ResourceModel\InvoiceItems\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Wasoko\Invoice\Model\ResourceModel\InvoiceItems\Collection as EntityCollection;

class Collection extends SearchResult
{
    protected function _initSelect()
    {
        $this->addFilterToMap('created_at', 'invoicegrid.created_at');
        parent::_initSelect();
        $this->getSelect()
            ->join(
                [
                    'invoicegrid' => 'sales_invoice_grid'
                ],
                'main_table.parent_id = invoicegrid.entity_id',
                [
                    'increment_id',
                    'state',
                    'store_id',
                    'store_name',
                    'order_id',
                    'order_increment_id',
                    'order_created_at',
                    'customer_name',
                    'customer_email',
                    'customer_group_id',
                    'payment_method',
                    'store_currency_code',
                    'order_currency_code',
                    'base_currency_code',
                    'global_currency_code',
                    'billing_name',
                    'billing_address',
                    'shipping_address',
                    'shipping_information',
                    'subtotal',
                    'shipping_and_handling',
                    'grand_total',
                    'created_at',
                    'updated_at',
                    'base_grand_total'
                ]
            )->join(
                [
                    'orderitems' => 'sales_order_item'
                ],
                'main_table.order_item_id=orderitems.item_id',
                ['tax_percent','qty_ordered','qty_returned','qty_invoiced']
            )->where('main_table.price IS NOT NULL');
    }
}
