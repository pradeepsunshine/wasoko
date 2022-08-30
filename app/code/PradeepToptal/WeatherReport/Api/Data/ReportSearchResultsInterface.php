<?php
declare(strict_types=1);

namespace PradeepToptal\WeatherReport\Api\Data;

interface ReportSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Report list.
     * @return \PradeepToptal\WeatherReport\Api\Data\ReportInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \PradeepToptal\WeatherReport\Api\Data\ReportInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

