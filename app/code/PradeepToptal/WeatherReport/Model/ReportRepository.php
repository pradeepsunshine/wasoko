<?php
declare(strict_types=1);

namespace PradeepToptal\WeatherReport\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use PradeepToptal\WeatherReport\Api\Data\ReportInterfaceFactory;
use PradeepToptal\WeatherReport\Api\Data\ReportSearchResultsInterfaceFactory;
use PradeepToptal\WeatherReport\Api\ReportRepositoryInterface;
use PradeepToptal\WeatherReport\Model\ResourceModel\Report as ResourceReport;
use PradeepToptal\WeatherReport\Model\ResourceModel\Report\CollectionFactory as ReportCollectionFactory;

class ReportRepository implements ReportRepositoryInterface
{
    /**
     * @var ResourceReport
     */
    protected $resource;

    /**
     * @var ReportFactory
     */
    protected $reportFactory;

    /**
     * @var ReportCollectionFactory
     */
    protected $reportCollectionFactory;

    /**
     * @var ReportSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var ReportInterfaceFactory
     */
    protected $dataReportFactory;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceReport $resource
     * @param ReportFactory $reportFactory
     * @param ReportInterfaceFactory $dataReportFactory
     * @param ReportCollectionFactory $reportCollectionFactory
     * @param ReportSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceReport $resource,
        ReportFactory $reportFactory,
        ReportInterfaceFactory $dataReportFactory,
        ReportCollectionFactory $reportCollectionFactory,
        ReportSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->reportFactory = $reportFactory;
        $this->reportCollectionFactory = $reportCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataReportFactory = $dataReportFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \PradeepToptal\WeatherReport\Api\Data\ReportInterface $report
    ) {
        $reportData = $this->extensibleDataObjectConverter->toNestedArray(
            $report,
            [],
            \PradeepToptal\WeatherReport\Api\Data\ReportInterface::class
        );

        $reportModel = $this->reportFactory->create()->setData($reportData);

        try {
            $this->resource->save($reportModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the report: %1',
                $exception->getMessage()
            ));
        }
        return $reportModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($reportId)
    {
        $report = $this->reportFactory->create();
        $this->resource->load($report, $reportId);
        if (!$report->getId()) {
            throw new NoSuchEntityException(__('Report with id "%1" does not exist.', $reportId));
        }
        return $report->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->reportCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \PradeepToptal\WeatherReport\Api\Data\ReportInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \PradeepToptal\WeatherReport\Api\Data\ReportInterface $report
    ) {
        try {
            $reportModel = $this->reportFactory->create();
            $this->resource->load($reportModel, $report->getReportId());
            $this->resource->delete($reportModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Report: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($reportId)
    {
        return $this->delete($this->get($reportId));
    }
}

