<?php

namespace Wasoko\Invoice\Ui\Component\Listing\Column;

use Magento\Directory\Model\Currency;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Item\CollectionFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class TotalSalesValue extends Column
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var StoreManagerInterface|null
     */
    private $storeManager;
    /**
     * @var CollectionFactory $collectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PriceCurrencyInterface $priceFormatter
     * @param array $components
     * @param array $data
     * @param Currency $currency
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PriceCurrencyInterface $priceFormatter,
        array $components = [],
        array $data = [],
        Currency $currency = null,
        StoreManagerInterface $storeManager = null,
        CollectionFactory $collectionFactory
    ) {
        $this->priceFormatter = $priceFormatter;
        $this->currency = $currency ?: ObjectManager::getInstance()
            ->get(Currency::class);
        $this->storeManager = $storeManager ?: ObjectManager::getInstance()
            ->get(StoreManagerInterface::class);
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->collectionFactory = $collectionFactory;
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
                $currencyCode = isset($item['base_currency_code']) ? $item['base_currency_code'] : null;
                if (!$currencyCode) {
                    $storeId = isset($item['store_id']) && (int)$item['store_id'] !== 0 ? $item['store_id'] :
                        $this->context->getFilterParam('store_id', Store::DEFAULT_STORE_ID);
                    $store = $this->storeManager->getStore(
                        $storeId
                    );
                    $currencyCode = $store->getBaseCurrency()->getCurrencyCode();
                }
                $basePurchaseCurrency = $this->currency->load($currencyCode);

                //$totalSalesVal = ((float)$item['qty_invoiced'] - (float)$item['qty_returned'])  * (float)$item['base_price_incl_tax'];
                $totalqty = ((float)$item['qty_invoiced'] - (float)$item['qty_returned']);
                $baseprice = $this->getItemPrice($item['parent_id']);
                $totalSalesVal =  $baseprice;
                $item[$this->getData('name')] = $basePurchaseCurrency
                    ->format($totalSalesVal, [], false);
            }
        }

        return $dataSource;
    }

    /**
     * @param $itemEntityID
     * @return array|mixed|null
     */
    private function getItemPrice($itemEntityID)
    {
        $itemCollection = $this->collectionFactory->create();
        $itemCollection->addFieldToFilter('parent_id',$itemEntityID);
        $itemPrice = [];
        foreach ($itemCollection as $item)
        {
            $itemPrice[]= ($item->getData('qty') *  $item->getData('base_price_incl_tax'));
        }
        if(!empty($itemPrice))
        {
            $totalItem_Price =array_sum($itemPrice);
        } else {
            $totalItem_Price = '';
        }
        return $totalItem_Price;

    }
}
