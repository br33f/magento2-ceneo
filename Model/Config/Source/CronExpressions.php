<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CronExpressions implements ArrayInterface
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
            '0 */1 * * *'  => __('At minute 0 past every hour'),
            '0 */3 * * *'  => __('At minute 0 past every 3rd hour'),
            '0 */6 * * *'  => __('At minute 0 past every 6th hour'),
            '0 */12 * * *' => __('At minute 0 past every 12th hour'),
            '0 22 * * *'   => __('At 10:00 pm'),
            '0 3 * * *'    => __('At 3:00 am'),
            '0 6 * * *'    => __('At 6:00 am'),
            '0 8 * * *'    => __('At 8:00 am'),
            '0 3 * * 1'    => __('At 03:00 on Monday'),
            '0 3 * * 2'    => __('At 03:00 on Tuesday'),
            '0 3 * * 3'    => __('At 03:00 on Wednesday'),
            '0 3 * * 4'    => __('At 03:00 on Thursday'),
            '0 3 * * 5'    => __('At 03:00 on Friday'),
            '0 3 * * 6'    => __('At 03:00 on Saturday'),
            '0 3 * * 7'    => __('At 03:00 on Sunday'),
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
            $options[] = ['label' => __('-- Please Select a Cron Expressions --'), 'value' => ''];
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
