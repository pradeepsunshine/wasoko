<?php
declare(strict_types=1);

namespace PradeepToptal\WeatherReport\Api\Data;

interface ReportInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const REPORT_ID = 'report_id';
    const REPORT_DATA = 'report_data';
    const REPORT_DATE = 'report_date';
    const REPORT_CITY = 'report_city';
    const REPORT_COUNTRY = 'report_country';

    /**
     * Get report_id
     * @return string|null
     */
    public function getReportId();

    /**
     * Set report_id
     * @param string $reportId
     * @return \PradeepToptal\WeatherReport\Api\Data\ReportInterface
     */
    public function setReportId($reportId);

    /**
     * Get report data
     * @return string|null
     */
    public function getReportData();

    /**
     * Set report data
     * @param string $reportData
     * @return \PradeepToptal\WeatherReport\Api\Data\ReportInterface
     */
    public function setReportData($reportData);

    /**
     * Get report city
     * @return string|null
     */
    public function getReportCity();

    /**
     * Set report city
     * @param string $city
     * @return \PradeepToptal\WeatherReport\Api\Data\ReportInterface
     */
    public function setReportCity($city);

    /**
     * Get report country
     * @return string|null
     */
    public function getReportCountry();

    /**
     * Set report country
     * @param string $country
     * @return \PradeepToptal\WeatherReport\Api\Data\ReportInterface
     */
    public function setReportCountry($country);

    /**
     * Get report date
     * @return string|null
     */
    public function getReportDate();

    /**
     * Set report date
     * @param string $reportData
     * @return \PradeepToptal\WeatherReport\Api\Data\ReportInterface
     */
    public function setReportDate($reportDate);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \PradeepToptal\WeatherReport\Api\Data\ReportExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \PradeepToptal\WeatherReport\Api\Data\ReportExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \PradeepToptal\WeatherReport\Api\Data\ReportExtensionInterface $extensionAttributes
    );
}

