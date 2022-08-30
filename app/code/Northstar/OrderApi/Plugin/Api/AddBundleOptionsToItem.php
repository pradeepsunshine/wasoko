<?php
declare(strict_types=1);

namespace Northstar\OrderApi\Plugin\Api;

use Magento\Catalog\Model\ProductFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\Data\OrderItemExtensionFactory;
use Magento\Sales\Api\Data\OrderItemSearchResultInterface;

class AddBundleOptionsToItem
{
    /**
     * @var OrderItemExtensionFactory
     */
    protected $orderItemExtensionFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @param OrderItemExtensionFactory $orderItemExtensionFactory
     * @param ProductFactory $productFactory
     */
    public function __construct(
        OrderItemExtensionFactory $orderItemExtensionFactory,
        ProductFactory $productFactory
    ) {
        $this->orderItemExtensionFactory = $orderItemExtensionFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * Add "my_custom_product_attribute" to order item
     *
     * @param OrderItemInterface $orderItem
     *
     * @return OrderItemInterface
     */
    protected function addMyCustomProductAttributeData(OrderItemInterface $orderItem)
    {
        $product = $this->productFactory->create();
        $product->load($orderItem->getProductId());
        $customAttribute = $product->getMyCustomProductAttribute();

        if (isset($customAttribute)) {
            $orderItemExtension = $this->orderItemExtensionFactory->create();
            $orderItemExtension->setMyCustomProductAttribute($customAttribute);

            $orderItem->setExtensionAttributes($orderItemExtension);
        }

        return $orderItem;
    }

    public function afterGet(OrderItemInterface $subject, OrderItemInterface $orderItem)
    {
        return '';
        $customAttribute = '1234';

        $extensionAttributes = $orderItem->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();

        $extensionAttributes->setMyCustomProductAttribute($customAttribute);
        $orderItem->setExtensionAttributes($extensionAttributes);

        return $orderItem;
    }

    /**
     * Add "my_custom_product_attribute" extension attribute to order data object
     * to make it accessible in API data
     *
     * @param OrderItemRepositoryInterface $subject
     * @param OrderItemSearchResultInterface $searchResult
     *
     * @return OrderItemSearchResultInterface
     */
    public function afterGetList(OrderItemRepositoryInterface $subject, OrderItemSearchResultInterface $searchResult)
    {
        $orders = $searchResult->getItems();

        foreach ($orders as &$order) {
            $order = $this->addMyCustomProductAttributeData($order);
        }

        return $searchResult;
    }
}
