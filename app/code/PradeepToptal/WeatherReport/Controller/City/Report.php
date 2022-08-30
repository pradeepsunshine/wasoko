<?php
declare(strict_types=1);

namespace PradeepToptal\WeatherReport\Controller\City;

use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use PradeepToptal\WeatherReport\Model\GeneratePdfFactory;
use PradeepToptal\WeatherReport\Model\ReportFactory;

/**
 * Class Report
 * @package PradeepToptal\WeatherReport\Controller\City
 */
class Report extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var GeneratePdfFactory
     */
    protected $generatePdfFactory;

    /**
     * @var ReportFactory
     */
    protected $reportFactory;

    /**
     * Report constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Validator $formKeyValidator
     * @param GeneratePdfFactory $generatePdfFactory
     * @param ReportFactory $reportFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Validator $formKeyValidator,
        GeneratePdfFactory $generatePdfFactory,
        ReportFactory $reportFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->generatePdfFactory = $generatePdfFactory;
        $this->reportFactory = $reportFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if(!$this->formKeyValidator->validate($this->getRequest()))
        {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('weatherreport');
            return $resultRedirect;
        }
        if ($this->getRequest()->getParam('generate')) {
            $reportFactory = $this->reportFactory->create();
            $reportData = $reportFactory->getReportData($this->getRequest());
            if(is_array($reportData) && count($reportData) > 0) {
                foreach ($reportData as $report) {
                    $reportItems['data'][] = $reportFactory->formatReportResult($report);
                }
                return $this->generatePdfFactory->create()->generate($reportItems);
            }
        }
        return $this->resultPageFactory->create();
    }
}
