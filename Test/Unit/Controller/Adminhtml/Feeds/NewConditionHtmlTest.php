<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Controller\Adminhtml\Feeds;

use Magento\Framework\DataObject;

/**
 * Class NewConditionHtmlTest
 *
 * @package Ceneo\Feed\Test\Unit\Controller\Adminhtml\Feeds
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class NewConditionHtmlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Ceneo\Feed\Controller\Adminhtml\Feeds\NewConditionHtml
     */
    protected $controllerNewConditionHtml;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->contextMock = $this->createMock(\Magento\Backend\App\Action\Context::class);
        $this->contextMock
            ->method('getRequest')
            ->willReturn($this->createMock(\Magento\Framework\App\RequestInterface::class));
        $this->responseMock = $this->getMockForAbstractClass(
            \Magento\Framework\App\ResponseInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['setBody']
        );
        $this->contextMock
            ->method('getResponse')
            ->willReturn($this->responseMock);
        $objectManagerMock = $this->getMockForAbstractClass(
            \Magento\Framework\ObjectManagerInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['setBody']
        );
        $objectManagerMock
            ->method('create')
            ->willReturn(new DataObject([]));
        $this->contextMock
            ->method('getObjectManager')
            ->willReturn($objectManagerMock);
        $coreRegistryMock = $this->createMock(\Magento\Framework\Registry::class);
        $feedFactoryMock = $this->getMockBuilder(\Ceneo\Feed\Model\FeedFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->controllerNewConditionHtml = $this->objectManager->getObject(
            \Ceneo\Feed\Controller\Adminhtml\Feeds\NewConditionHtml::class,
            [
                'context' => $this->contextMock,
                'coreRegistry' => $coreRegistryMock,
                'feedFactory' => $feedFactoryMock
            ]
        );
    }

    /**
     * Test function
     */
    public function testExecute()
    {
        $this->responseMock->expects($this->once())->method('setBody');
        $this->controllerNewConditionHtml->execute();
    }
}
