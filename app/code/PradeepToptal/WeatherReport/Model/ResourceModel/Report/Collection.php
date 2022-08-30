<?php
declare(strict_types=1);

namespace PradeepToptal\WeatherReport\Model\ResourceModel\Report;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'report_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \PradeepToptal\WeatherReport\Model\Report::class,
            \PradeepToptal\WeatherReport\Model\ResourceModel\Report::class
        );
    }
}

