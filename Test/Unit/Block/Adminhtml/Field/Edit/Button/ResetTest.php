<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Block\Adminhtml\Field\Edit\Button;

/**
 * Class ResetTest
 *
 * @package Ceneo\Feed\Test\Unit\Block\Adminhtml\Field\Edit\Button
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class ResetTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Ceneo\Feed\Block\Adminhtml\Field\Edit\Button\Reset
     */
    protected $buttonReset;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->buttonReset = $this->objectManager->getObject(\Ceneo\Feed\Block\Adminhtml\Field\Edit\Button\Reset::class);
    }

    /**
     * Test function
     */
    public function testGetButtonData()
    {
        $expects = [
            'label' => __('Reset'),
            'class' => 'reset',
            'on_click' => 'location.reload();',
            'sort_order' => 30
        ];
        $this->assertEquals($expects, $this->buttonReset->getButtonData());
    }
}
