<?php

namespace Northstar\OrderApi\Plugin\api;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;


class OrderRepository
{

    /** @var \Magento\Sales\Api\Data\OrderItemExtensionFactory */
    protected $orderItemExtensionFactory;

    /**
     * @param \Magento\Sales\Api\Data\OrderItemExtensionFactory $orderItemExtensionFactory
     */
    public function __construct(
        \Magento\Sales\Api\Data\OrderItemExtensionFactory $orderItemExtensionFactory
    ) {
        $this->orderItemExtensionFactory = $orderItemExtensionFactory;
    }

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
        //if (strpos($shippingDesc, '&') !== false) {
        $shippingArr = explode('&', $shippingDesc);
        if (strpos($shippingArr[0], 'Flat Rate Shipping - Arrives 5-7 business days after it ships') !== false) {
            $modifiedShipping = 'FR 5-7 d';
        } else if (strpos($shippingArr[0], 'Standard Delivery - Arrives 5-7 business days after it ships') !== false) {
            $modifiedShipping = 'SD 5-7 d';
        } else if (strpos($shippingArr[0], 'Three-Day Select - Arrives 3 business days after it ships') !== false) {
            $modifiedShipping = 'TDS 3 d';
        } else if (strpos($shippingArr[0], 'Second Day Air - Arrives 2 business days after it ships') !== false) {
            $modifiedShipping = 'SDA 2 d';
        } else if (strpos($shippingArr[0], 'Next Day Air Saver - Arrives 1 business day after it ships') !== false) {
            $modifiedShipping = 'NDAS 1 d';
        } else if (strpos($shippingArr[0], 'Warehouse  Liftgate Delivery') !== false) {
            $modifiedShipping = 'Liftgate Delivery';
        } else if (strpos($shippingArr[0], 'Warehouse  Standard Delivery') !== false) {
            $modifiedShipping = 'Standard Delivery';
        } else {
            $modifiedShipping = $shippingArr[0];
        }


        $warehouseShipping = '';
        if (isset($shippingArr[1])) {
            if (strpos($shippingArr[1], 'Standard Delivery') !== false) {
                $warehouseShipping = 'Standard Delivery';
            } else {
                $warehouseShipping = 'Liftgate Delivery';
            }
            $modifiedShipping .= ' & ' . $warehouseShipping;
        }
        $order->setShippingDescription($modifiedShipping);
        //}
        $order = $this->addBundleOptionExtensionAttribute($order);

        return $order;
    }

    /**
     * Modified shipping description for order api
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
        $orders = $searchResult->getItems();

        foreach ($orders as &$order) {
            $shippingArr = [];
            $modifiedShipping = '';
            $shippingDesc = $order->getData(self::SHIPPING_DESCRIPTION);
            //if (strpos($shippingDesc, '&') !== false) {
            $shippingArr = explode('&', $shippingDesc);
            if (strpos($shippingArr[0], 'Flat Rate Shipping - Arrives 5-7 business days after it ships') !== false) {
                $modifiedShipping = 'FR 5-7 d';
            } else if (strpos($shippingArr[0], 'Standard Delivery - Arrives 5-7 business days after it ships') !== false) {
                $modifiedShipping = 'SD 5-7 d';
            } else if (strpos($shippingArr[0], 'Three-Day Select - Arrives 3 business days after it ships') !== false) {
                $modifiedShipping = 'TDS 3 d';
            } else if (strpos($shippingArr[0], 'Second Day Air - Arrives 2 business days after it ships') !== false) {
                $modifiedShipping = 'SDA 2 d';
            } else if (strpos($shippingArr[0], 'Next Day Air Saver - Arrives 1 business day after it ships') !== false) {
                $modifiedShipping = 'NDAS 1 d';
            } else if (strpos($shippingArr[0], 'Warehouse  Liftgate Delivery') !== false) {
                $modifiedShipping = 'Liftgate Delivery';
            } else if (strpos($shippingArr[0], 'Warehouse  Standard Delivery') !== false) {
                $modifiedShipping = 'Standard Delivery';
            } else {
                $modifiedShipping = $shippingArr[0];
            }


            $warehouseShipping = '';
            if (isset($shippingArr[1])) {
                if (strpos($shippingArr[1], 'Standard Delivery') !== false) {
                    $warehouseShipping = 'Standard Delivery';
                } else {
                    $warehouseShipping = 'Liftgate Delivery';
                }
                $modifiedShipping .= ' & ' . $warehouseShipping;
            }
            $order->setShippingDescription($modifiedShipping);
            $this->addBundleOptionExtensionAttribute($order);
        }

        return $searchResult;
    }

    /**
     * @param $order
     * @return void
     */
    private function addBundleOptionExtensionAttribute($order)
    {
        if (null !== $order->getItems()) {
            foreach ($order->getItems() as $orderItem) {
                if ($orderItem->getProductType() == 'bundle') {
                    $productOptions = $orderItem->getProductOptions();
                    if (isset($productOptions['bundle_options'])) {
                        /** @var \Magento\Sales\Api\Data\OrderItemExtension $orderItemExtension */
                        $orderItemExtension = $this->orderItemExtensionFactory->create();
                        $orderItemExtension->setCustomBundleOptions($productOptions['bundle_options']);
                        $orderItem->setExtensionAttributes($orderItemExtension);
                    }
                }
            }
        }
        return $order;
    }
}
