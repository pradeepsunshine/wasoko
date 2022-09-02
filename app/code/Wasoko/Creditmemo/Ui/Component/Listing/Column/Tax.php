<?php

namespace Wasoko\Creditmemo\Ui\Component\Listing\Column;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class Price
 *
 * UiComponent class for Price format column
 */
class Tax extends Column
{
    /**
     * @var StoreManagerInterface|null
     */
    private $storeManager;

    private $productRepo;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ProductRepositoryInterface $productRepo
     * @param array $components
     * @param array $data
     * @param StoreManagerInterface|null $storeManager
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductRepositoryInterface $productRepo,
        array $components = [],
        array $data = [],
        StoreManagerInterface $storeManager = null
    ) {
        $this->productRepo = $productRepo;
        $this->storeManager = $storeManager ?: ObjectManager::getInstance()
            ->get(StoreManagerInterface::class);
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                
                try {
                    $product = $this->productRepo->getById($item['product_id']);
                    if ($product->getIsMtv()) {
                        $item['tax_percent'] = 16;
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return $dataSource;
    }
}
