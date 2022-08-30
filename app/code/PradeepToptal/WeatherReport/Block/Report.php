<?php
declare(strict_types=1);

namespace PradeepToptal\WeatherReport\Block;

use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Directory\Block\Data;

/**
 * Class Report
 * @package PradeepToptal\WeatherReport\Block
 */
class Report extends Template
{
    const FORM_ACTION_URL = 'weatherreport/city/report';

    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * @var Data
     */
    protected $directoryData;

    /**
     * Report constructor.
     * @param Context $context
     * @param FormKey $formKey
     * @param Data $directoryData
     * @param array $data
     */
    public function __construct(
        Context $context,
        FormKey $formKey,
        Data $directoryData,
        array $data = []
    )
    {
        $this->formKey = $formKey;
        $this->directoryData = $directoryData;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFromKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl(self::FORM_ACTION_URL, ['_secure' => true]);
    }

    /**
     * @return string
     */
    public function getCountrySelectorHtml($defaultValue = null)
    {
        return $this->directoryData->getCountryHtmlSelect($defaultValue);
    }
}
