<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Model\Config\Source;

/**
 * Class CronExpressionsTest
 *
 * @package Ceneo\Feed\Test\Unit\Model\Config\Source
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class CronExpressionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Ceneo\Feed\Model\Config\Source\CronExpressions
     */
    protected $cronExpressions;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->cronExpressions = $this->objectManager->getObject(\Ceneo\Feed\Model\Config\Source\CronExpressions::class);
    }

    /**
     * Test function
     */
    public function testToOptionArray()
    {
        $result = $this->cronExpressions->toOptionArray(true);
        $this->assertEquals(['label' => __('-- Please Select a Cron Expressions --'), 'value' => ''], $result[0]);
    }
}
