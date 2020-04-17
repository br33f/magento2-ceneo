<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Block\Adminhtml\Field\Edit\Button;

/**
 * Class SaveTest
 *
 * @package Ceneo\Feed\Test\Unit\Block\Adminhtml\Field\Edit\Button
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class SaveTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Ceneo\Feed\Block\Adminhtml\Field\Edit\Button\Delete
     */
    protected $buttonSave;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->buttonSave = $this->objectManager->getObject(\Ceneo\Feed\Block\Adminhtml\Field\Edit\Button\Save::class);
    }

    /**
     * Test function
     */
    public function testGetButtonData()
    {
        $expects = [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',

            ],
            'sort_order' => 90,
            'on_click' => '',
        ];
        $this->assertEquals($expects, $this->buttonSave->getButtonData());
    }
}
