<?php
declare(strict_types=1);

namespace PradeepToptal\WeatherReport\Block\Report;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use PradeepToptal\WeatherReport\Model\ReportFactory;

/**
 * Class Result
 * @package PradeepToptal\WeatherReport\Block\Report
 */
class Result extends Template
{
    /**
     * @var ReportFactory
     */
    protected $reportFactory;

    /**
     * Result constructor.
     * @param Context $context
     * @param ReportFactory $reportFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        ReportFactory $reportFactory,
        array $data = []
    )
    {
        $this->reportFactory = $reportFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return array|false
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getReportData()
    {
        $reportFactory = $this->reportFactory->create();
        $reportData = $reportFactory->getReportData($this->getRequest());
        $reportItems = [];
        if (isset($reportData) && !is_array($reportData) && $reportData->getReportData()) {
            $reportItems['real'] = true;
            $reportItems['data'][] = $reportFactory->formatReportResult($reportData);
        } elseif (is_array($reportData) && is_array($reportData) && count($reportData) > 0) {
            $reportItems['real'] = false;
            foreach ($reportData as $report) {
                $reportItems['data'][] = $reportFactory->formatReportResult($report);
            }
        }
        if (count($reportItems)) {
            return $reportItems;
        }
        return false;
    }
}
