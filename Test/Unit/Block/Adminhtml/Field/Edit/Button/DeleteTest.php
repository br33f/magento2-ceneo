<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Block\Adminhtml\Field\Edit\Button;

/**
 * Class DeleteTest
 *
 * @package Ceneo\Feed\Test\Unit\Block\Adminhtml\Field\Edit\Button
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class DeleteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Ceneo\Feed\Block\Adminhtml\Field\Edit\Button\Delete
     */
    protected $buttonDelete;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $urlBuilder = $this->createMock(\Magento\Framework\UrlInterface::class);
        $this->request = $this->createMock(\Magento\Framework\App\RequestInterface::class);
        $this->buttonDelete = $this->objectManager->getObject(\Ceneo\Feed\Block\Adminhtml\Field\Edit\Button\Delete::class,
            [
                'urlBuilder' => $urlBuilder,
                'request' => $this->request
            ]
        );
    }

    /**
     * Test function
     */
    public function testGetButtonDataWithId()
    {
        $this->request->method('getParam')->with('id')->willReturn(1);
        $this->request->expects($this->once())->method('getParam');
        $this->assertNotEmpty($this->buttonDelete->getButtonData());
    }

    /**
     * Test function
     */
    public function testGetButtonDataWithoutId()
    {
        $this->request->method('getParam')->with('id')->willReturn(null);
        $this->request->expects($this->once())->method('getParam');
        $this->assertEquals([], $this->buttonDelete->getButtonData());
    }
}
