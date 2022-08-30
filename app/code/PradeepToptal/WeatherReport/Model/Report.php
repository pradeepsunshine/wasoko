<?php
declare(strict_types=1);

namespace PradeepToptal\WeatherReport\Model;

use Magento\Framework\Api\DataObjectHelper;
use PradeepToptal\WeatherReport\Api\Data\ReportInterface;
use PradeepToptal\WeatherReport\Api\Data\ReportInterfaceFactory;
use PradeepToptal\WeatherReport\Model\Http\Client;
use PradeepToptal\WeatherReport\Api\ReportRepositoryInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Report extends \Magento\Framework\Model\AbstractModel
{
    const ICON_IMG_BASE_URL = 'http://openweathermap.org/img/w/';
    const ICON_IMG_EXTENSION = '.png';

    /**
     * @var ReportInterfaceFactory
     */
    protected $reportDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var ReportRepositoryInterface
     */
    protected $reportRepo;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SortOrder
     */
    protected $sortOrder;

    /**
     * @var
     */
    protected $logger;

    /**
     * @var DateTime 
     */
    protected $dateTime;

    protected $timeZone;

    protected $_eventPrefix = 'pradeeptoptal_weatherreport_report';

    /**
     * Report constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ReportInterfaceFactory $reportDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\Report $resource
     * @param ResourceModel\Report\Collection $resourceCollection
     * @param Client $httpClient
     * @param ReportRepositoryInterface $reportRepo
     * @param Json $json
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrder $sortOrder
     * @param LoggerInterface $logger
     * @param DateTime $dateTime
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ReportInterfaceFactory $reportDataFactory,
        DataObjectHelper $dataObjectHelper,
        \PradeepToptal\WeatherReport\Model\ResourceModel\Report $resource,
        \PradeepToptal\WeatherReport\Model\ResourceModel\Report\Collection $resourceCollection,
        Client $httpClient,
        ReportRepositoryInterface $reportRepo,
        Json $json,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrder $sortOrder,
        LoggerInterface $logger,
        DateTime $dateTime,
        array $data = []
    )
    {
        $this->reportDataFactory = $reportDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->httpClient = $httpClient;
        $this->reportRepo = $reportRepo;
        $this->json = $json;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrder = $sortOrder;
        $this->logger = $logger;
        $this->dateTime = $dateTime;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return ReportInterface
     */
    public function getDataModel()
    {
        $reportData = $this->getData();

        $reportDataObject = $this->reportDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $reportDataObject,
            $reportData,
            ReportInterface::class
        );

        return $reportDataObject;
    }

    /**
     * @param $request
     * @return ReportInterface|ReportInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getReportData($request)
    {
        if (
            $request->getParam('city') &&
            $request->getParam('country_id') &&
            $request->getParam('from') &&
            $request->getParam('to')
        ) {
            $from = $request->getParam('from');
            $to = $request->getParam('to');
            $fromFormatted = $this->dateTime->date('Y-m-d H:i:s', strtotime($from));
            $toFormatted = $this->dateTime->date('Y-m-d 23:59:59', strtotime($to));
            $sortOrder = $this->sortOrder->setField('report_date')
                ->setDirection('DESC');
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('report_date', $fromFormatted, 'gteq')
                ->addFilter('report_date', $toFormatted, 'lteq')
                ->addFilter('report_city', strtolower($request->getParam('city')))
                ->addFilter('report_country', strtolower($request->getParam('country_id')))
                ->setSortOrders([$sortOrder])
                ->create();
            $items = $this->reportRepo->getList($searchCriteria)->getItems();
            return $items;

        } elseif (
            $request->getParam('city') &&
            $request->getParam('country_id') &&
            !$request->getParam('from') &&
            !$request->getParam('to')
        ) {
            $response = $this->httpClient->getWeatherDataForCity($request->getParam('city'), $request->getParam('country_id'));
            if ($response) {
                $reportData = $this->reportDataFactory->create();
                $reportData->setReportData($response);
                $cityOffsetArr = $this->getCityCountryTimeOffsetFromReportData($reportData);
                //Change timezone to cover timezone diff.
                $timeZone = timezone_name_from_abbr('', $cityOffsetArr['offset'], (int)date('I'));
                date_default_timezone_set($timeZone);
                $currentDate = $this->dateTime->date('Y-m-d H:i:s');
                $reportData->setReportDate($currentDate);

                $reportData->setReportCity($cityOffsetArr['city']);
                $reportData->setReportCountry($cityOffsetArr['country']);
                try {
                    $reportData = $this->reportRepo->save($reportData);
                } catch (LocalizedException $e) {
                    $this->logger->critical($e->getMessage());
                }
                return $reportData;

            }
        }
    }

    /**
     * @param $reportData
     * @return array|bool|float|int|mixed|string|null
     */
    public function formatReportResult($reportData)
    {
        //Change timezone to match the location time.
        if (!$this->timeZone) {
            $cityOffsetArr = $this->getCityCountryTimeOffsetFromReportData($reportData);
            $this->timeZone = timezone_name_from_abbr('', $cityOffsetArr['offset'], (int)date('I'));
            date_default_timezone_set($this->timeZone);
        }

        $reportData = $this->json->unserialize($reportData->getReportData());

        $reportData['date'] = date("M j, Y, g:i A", $reportData['dt']);
        $reportData['city'] = ucwords($reportData['name']);
        $reportData['country'] = $reportData['sys']['country'];
        $reportData['temp'] = round($reportData['main']['temp']);
        $reportData['temp_feels_like'] = round($reportData['main']['feels_like']);
        $reportData['temp_min'] = round($reportData['main']['temp_min']);
        $reportData['temp_max'] = round($reportData['main']['temp_max']);
        $reportData['pressure'] = $reportData['main']['pressure'];
        $reportData['humidity'] = $reportData['main']['humidity'];
        $reportData['weather_description'] = ucwords($reportData['weather'][0]['description']);
        $reportData['img_url'] = self::ICON_IMG_BASE_URL.$reportData['weather'][0]['icon'].self::ICON_IMG_EXTENSION;
        return $reportData;

    }

    /**
     * @param $reportData
     * @return array
     */
    protected function getCityCountryTimeOffsetFromReportData($reportData)
    {
        $reportData = $this->json->unserialize($reportData->getReportData());
        $cityOffsetArr['city'] = strtolower($reportData['name']);
        $cityOffsetArr['offset'] = $reportData['timezone'];
        $cityOffsetArr['country'] = $reportData['sys']['country'];
        return $cityOffsetArr;
    }
}

