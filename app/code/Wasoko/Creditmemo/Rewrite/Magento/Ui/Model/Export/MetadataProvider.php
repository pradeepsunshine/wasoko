<?php
declare(strict_types=1);

namespace Wasoko\Creditmemo\Rewrite\Magento\Ui\Model\Export;

use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Ui\Component\MassAction\Filter;
use Wasoko\Customer\Model\Source\AgentData;

class MetadataProvider extends \Magento\Ui\Model\Export\MetadataProvider
{
    private AgentData $agentData;

    /**
     * @param Filter $filter
     * @param TimezoneInterface $localeDate
     * @param ResolverInterface $localeResolver
     * @param string $dateFormat
     * @param AgentData $agentData
     * @param array $data
     */
    public function __construct(Filter            $filter,
                                TimezoneInterface $localeDate,
                                ResolverInterface $localeResolver,
                                $dateFormat = 'M j, Y h:i:s A',
                                AgentData         $agentData,
                                array             $data = [])
    {
        parent::__construct($filter, $localeDate, $localeResolver, $dateFormat, $data);
        $this->agentData = $agentData;
    }

    /**
     * Returns row data
     *
     * @param DocumentInterface $document
     * @param array $fields
     * @param array $options
     *
     * @return string[]
     */
    public function getRowData(DocumentInterface $document, $fields, $options): array
    {
        $row = [];
        foreach ($fields as $column) {
            if (isset($options[$column])) {
                $key = $document->getCustomAttribute($column)->getValue();
                if (isset($options[$column][$key])) {
                    $row[] = $options[$column][$key];
                } else {
                    $row[] = $key;
                }
            } else {
                $value = $document->getCustomAttribute($column)->getValue();
                if (in_array('creditmemo_dummy_field', $fields)) {
                    if ($column == 'base_price' || $column == 'tax_amount' || $column == 'base_row_total_incl_tax') {
                        // $value = 'ZMK -'.$value;
                         $value = 'ZMK -' . number_format((float)$value, 2, '.', '');
                    }
                    if ($column == 'qty' || $column == 'tax_percent') {
                        $value = number_format((float)$value, 2, '.', '');
                    }
                } else {
                    if ($column == 'base_price' || $column == 'tax_amount' || $column == 'base_row_total_incl_tax') {
                       // $value = 'ZMK '.$value;
                        $value = 'ZMK ' . number_format((float)$value, 2, '.', '');
                    }
                    if ($column == 'qty' || $column == 'tax_percent') {
                        $value = number_format((float)$value, 2, '.', '');
                    }
                }
                if($column === 'agent_id') {
                    $value = $this->agentData->getOptionText($value);
                }
                $row[] = $value;
            }
        }
        return $row;
    }
}
