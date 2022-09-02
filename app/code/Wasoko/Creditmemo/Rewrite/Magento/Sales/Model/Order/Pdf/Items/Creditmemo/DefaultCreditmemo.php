<?php
declare(strict_types=1);

namespace Wasoko\Creditmemo\Rewrite\Magento\Sales\Model\Order\Pdf\Items\Creditmemo;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Model\RtlTextHandler;
use Wasoko\Creditmemo\Rewrite\Magento\Sales\Model\Order\Pdf\Creditmemo;

class DefaultCreditmemo extends \Magento\Sales\Model\Order\Pdf\Items\Creditmemo\DefaultCreditmemo
{
    /**
     * @var RtlTextHandler
     */
    private $rtlTextHandler;

    /**
     * @var OrderItemRepositoryInterface
     */
    private $orderItemRepositoryInterface;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param RtlTextHandler|null $rtlTextHandler
     * @param OrderItemRepositoryInterface $orderItemRepositoryInterface
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        ?RtlTextHandler $rtlTextHandler = null,
        OrderItemRepositoryInterface $orderItemRepositoryInterface
    ) {
        $this->string = $string;
        $this->rtlTextHandler = $rtlTextHandler  ?: ObjectManager::getInstance()->get(RtlTextHandler::class);
        $this->orderItemRepositoryInterface = $orderItemRepositoryInterface;
        parent::__construct(
            $context,
            $registry,
            $taxData,
            $filesystem,
            $filterManager,
            $string,
            $resource,
            $resourceCollection,
            $data,
            $this->rtlTextHandler
        );
    }

    /**
     * Draw item line
     *
     * @return void
     */
    public function draw()
    {
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = [];

        // draw Product name
        $lines[0][] = [
            'text' => $this->string->split($this->prepareText((string)$item->getName()), 35, true, true),
            'feed' => 35
        ];

        // draw SKU
        $lines[0][] = [
            'text' => $this->string->split($this->prepareText((string)$this->getSku($item)), 17),
            'feed' => 260,
            'align' => 'right',
        ];

        // draw QTY
        $lines[0][] = ['text' => $item->getQty() * 1, 'feed' => 395, 'align' => 'right'];

        try {
            $orderItem = $this->getItem()->getOrderItem();
            $taxPercent = ($orderItem->getTaxPercent()) ? round($orderItem->getTaxPercent(), 2) : '0.00';
            if ($orderItem->getProduct()->getIsMtv()) {
                $taxPercent = 16;
            }
            $lines[0][] = ['text' => $taxPercent, 'feed' => 435, 'align' => 'right'];
        } catch (NoSuchEntityException $noSuchEntityException) {

        }

        // draw item Prices
        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        $feedPrice = 330;
        $feedSubtotal = $feedPrice + 235;
        foreach ($prices as $priceData) {
            if (isset($priceData['label'])) {
                // draw Price label
                $lines[$i][] = ['text' => $priceData['label'], 'feed' => $feedPrice, 'align' => 'right'];
                // draw Subtotal label
                $lines[$i][] = ['text' => $priceData['label'], 'feed' => $feedSubtotal, 'align' => 'right'];
                $i++;
            }

            // draw Price
            $lines[$i][] = [
                'text' => str_replace(Creditmemo::CURRENCY_CODE, Creditmemo::REPLACEMENT_TEXT, $priceData['price']),
                'feed' => $feedPrice,
                'font' => 'bold',
                'align' => 'right',
            ];
            // draw Subtotal
            $lines[$i][] = [
                'text' => str_replace(Creditmemo::CURRENCY_CODE, Creditmemo::REPLACEMENT_TEXT, $priceData['subtotal']),
                'feed' => $feedSubtotal,
                'font' => 'bold',
                'align' => 'right',
            ];
            $i++;
        }

        // draw Tax
        $lines[0][] = [
            'text' => str_replace(Creditmemo::CURRENCY_CODE, Creditmemo::REPLACEMENT_TEXT, $order->formatPriceTxt($item->getTaxAmount())),
            'feed' => 495,
            'font' => 'bold',
            'align' => 'right',
        ];

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = [
                    'text' => $this->string->split($this->filterManager->stripTags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 35,
                ];

                // Checking whether option value is not null
                if ($option['value'] !== null) {
                    if (isset($option['print_value'])) {
                        $printValue = $option['print_value'];
                    } else {
                        $printValue = $this->filterManager->stripTags($option['value']);
                    }
                    $values = explode(', ', $printValue);
                    foreach ($values as $value) {
                        $lines[][] = ['text' => $this->string->split($value, 30, true, true), 'feed' => 40];
                    }
                }
            }
        }

        $lineBlock = ['lines' => $lines, 'height' => 20];

        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $this->setPage($page);
    }

    /**
     * Returns prepared for PDF text, reversed in case of RTL text
     *
     * @param string $string
     * @return string
     */
    private function prepareText(string $string): string
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        return $this->rtlTextHandler->reverseRtlText(html_entity_decode($string));
    }
}
