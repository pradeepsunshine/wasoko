<?php
declare(strict_types=1);

namespace PradeepToptal\WeatherReport\Model\Data;

use PradeepToptal\WeatherReport\Api\Data\ReportInterface;

class Report extends \Magento\Framework\Api\AbstractExtensibleObject implements ReportInterface
{

    /**
     * Get report_id
     * @return string|null
     */
    public function getReportId()
    {
        return $this->_get(self::REPORT_ID);
    }

    /**
     * Set report_id
     * @param string $reportId
     * @return \PradeepToptal\WeatherReport\Api\Data\ReportInterface
     */
    public function setReportId($reportId)
    {
        return $this->setData(self::REPORT_ID, $reportId);
    }

    /**
     * Get report data
     * @return string|null
     */
    public function getReportData()
    {
        return $this->_get(self::REPORT_DATA);
    }

    /**
     * Set report data
     * @param string $reportData
     * @return \PradeepToptal\WeatherReport\Api\Data\ReportInterface
     */
    public function setReportData($reportData)
    {
        return $this->setData(self::REPORT_DATA, $reportData);
    }

    /**
     * Get report city
     * @return string|null
     */
    public function getReportCity()
    {
        return $this->_get(self::REPORT_CITY);
    }

    /**
     * Set report city
     * @param string $reportCity
     * @return \PradeepToptal\WeatherReport\Api\Data\ReportInterface
     */
    public function setReportCity($reportCity)
    {
        return $this->setData(self::REPORT_CITY, $reportCity);
    }

    /**
     * Get report country
     * @return string|null
     */
    public function getReportCountry()
    {
        return $this->_get(self::REPORT_COUNTRY);
    }

    /**
     * Set report country
     * @param string $reportCountry
     * @return \PradeepToptal\WeatherReport\Api\Data\ReportInterface
     */
    public function setReportCountry($reportCountry)
    {
        return $this->setData(self::REPORT_COUNTRY, $reportCountry);
    }

    /**
     * Get report date
     * @return string|null
     */
    public function getReportDate()
    {
        return $this->_get(self::REPORT_DATE);
    }

    /**
     * Set report date
     * @param string $reportDate
     * @return \PradeepToptal\WeatherReport\Api\Data\ReportInterface
     */
    public function setReportDate($reportDate)
    {
        return $this->setData(self::REPORT_DATE, $reportDate);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \PradeepToptal\WeatherReport\Api\Data\ReportExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \PradeepToptal\WeatherReport\Api\Data\ReportExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \PradeepToptal\WeatherReport\Api\Data\ReportExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

