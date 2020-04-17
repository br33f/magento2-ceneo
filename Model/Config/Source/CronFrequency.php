<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CronFrequency implements ArrayInterface
{
    /**
     * @var array
     */
    private $cronExpressions = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cronExpressions = [
            '* * * * *'  => __('Every minute'),
            '*/5 * * * *'  => __('Every 5 minutes'),
            '*/10 * * * *'  => __('Every 10 minutes'),
            '*/30 * * * *'  => __('Every 30 minutes'),
            '0 */1 * * *'  => __('At minute 0 past every hour'),
            '0 */3 * * *'  => __('At minute 0 past every 3rd hour'),
            '0 */6 * * *'  => __('At minute 0 past every 6th hour'),
            '0 */12 * * *' => __('At minute 0 past every 12th hour'),
        ];
    }

    /**
     * Return option array
     *
     * @param bool $addEmpty
     * @return array
     */
    public function toOptionArray($addEmpty = true)
    {
        $options = [];

        if ($addEmpty) {
            $options[] = ['label' => __('-- Please Select a Cron Frequency --'), 'value' => ''];
        }

        foreach ($this->cronExpressions as $value => $label) {
            $options[] = [
                'label' => $label,
                'value' => $value
            ];
        }

        return $options;
    }
}
