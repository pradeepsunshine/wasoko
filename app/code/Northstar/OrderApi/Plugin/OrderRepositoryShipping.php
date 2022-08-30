<?php

namespace Northstar\OrderApi\Plugin;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;


class OrderRepositoryShipping
{

    const SHIPPING_DESCRIPTION = 'shipping_description';


    /**
     * Modified shipping description for order api
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        $shippingArr = [];
        $modifiedShipping = '';
        $shippingDesc = $order->getData(self::SHIPPING_DESCRIPTION);
        $modifiedShipping = str_replace('UPS - ', '', $shippingDesc);

        $order->setShippingDescription($modifiedShipping);

        return $order;
    }

    /**
     * Modified shipping description for order api
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function beforeSave(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        $shippingArr = [];
        $modifiedShipping = '';
        $shippingDesc = $order->getData(self::SHIPPING_DESCRIPTION);
        $modifiedShipping = str_replace('UPS - ', '', $shippingDesc);

        $order->setShippingDescription($modifiedShipping);

        return $order;
    }
}
