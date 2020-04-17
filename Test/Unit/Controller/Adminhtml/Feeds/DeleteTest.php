<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Controller\Adminhtml\Feeds;

use phpDocumentor\Reflection\Types\This;

/**
 * Class DeleteTest
 *
 * @package Ceneo\Feed\Test\Unit\Controller\Adminhtml\Feeds
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class DeleteTest extends \PHPUnit\Framework\TestCase
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
     * @var \Ceneo\Feed\Model\FeedFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $factoryMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $managerMock;

    /**
     * @var \Ceneo\Feed\Controller\Adminhtml\Feeds\Delete
     */
    protected $controllerDelete;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->contextMock = $this->createMock(\Magento\Backend\App\Action\Context::class);
        $requestMock = $this->createMock(\Magento\Framework\App\RequestInterface::class);
        $requestMock->method('getParam')->with('id')->willReturn(1);
        $this->contextMock
            ->method('getRequest')
            ->willReturn($requestMock);
        $resultRedirectFactoryMock = $this->createMock(\Magento\Framework\Controller\Result\RedirectFactory::class);
        $resultRedirectFactoryMock
            ->method('create')
            ->willReturn($this->createMock(\Magento\Framework\Controller\Result\Redirect::class));
        $this->contextMock
            ->method('getResultRedirectFactory')
            ->willReturn($resultRedirectFactoryMock);
        $this->managerMock = $this->createMock(\Magento\Framework\Message\ManagerInterface::class);
        $this->contextMock
            ->method('getMessageManager')
            ->willReturn($this->managerMock);
        $this->factoryMock = $this->getMockBuilder(\Ceneo\Feed\Model\FeedFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $modelFeedMock = $this->createMock(\Ceneo\Feed\Model\Feed::class);
        $modelFeedMock
            ->method('getResource')
            ->willReturn($this->createMock(\Ceneo\Feed\Model\ResourceModel\Feed::class));
        $this->factoryMock->method('create')->willReturn($modelFeedMock);
        $this->controllerDelete = $this->objectManager->getObject(
            \Ceneo\Feed\Controller\Adminhtml\Feeds\Delete::class,
            [
                'context' => $this->contextMock,
                'factory' => $this->factoryMock
            ]
        );
    }

    /**
     * Test function
     */
    public function testExecute()
    {
        $this->managerMock->expects($this->once())->method('addSuccess');
        $this->controllerDelete->execute();
    }
}
