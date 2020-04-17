<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Controller\Adminhtml\Feeds;

/**
 * Class EditTest
 *
 * @package Ceneo\Feed\Test\Unit\Controller\Adminhtml\Feeds
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class EditTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Controller\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    /**
     * @var \Magento\Framework\Controller\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $modelFeedMock;

    /**
     * @var \Ceneo\Feed\Controller\Adminhtml\Feeds\Edit
     */
    protected $controllerEdit;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $requestMock = $this->getMockForAbstractClass(
            \Magento\Framework\App\RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getParam', 'getMethod', 'getPostValue']
        );
        $requestMock->method('getMethod')->willReturn(\Zend\Http\Request::METHOD_POST);
        $contextMock = $this->createMock(\Magento\Backend\App\Action\Context::class);
        $contextMock
            ->method('getRequest')
            ->willReturn($requestMock);
        $contextMock
            ->method('getResultFactory')
            ->willReturn($this->createMock(\Magento\Framework\Controller\ResultFactory::class));
        $redirectFactoryMock = $this->getMockForAbstractClass(
            \Magento\Framework\Controller\Result\RedirectFactory::class,
            [],
            '',
            false,
            true,
            true,
            ['create']
        );
        $this->resultRedirectMock = $this->createMock(\Magento\Framework\Controller\Result\Redirect::class);
        $redirectFactoryMock
            ->method('create')
            ->willReturn($this->resultRedirectMock);
        $contextMock
            ->method('getResultRedirectFactory')
            ->willReturn($redirectFactoryMock);
        $registryMock = $this->createMock(\Magento\Framework\Registry::class);
        $this->modelFeedMock = $this->createMock(\Ceneo\Feed\Model\Feed::class);
        $registryMock
            ->method('registry')
            ->with(\Ceneo\Feed\Model\Definitions::RULE_REGISTRY_KEY)
            ->willReturn($this->modelFeedMock);
        $feedFactoryMock = $this->getMockForAbstractClass(
            \Ceneo\Feed\Model\FeedFactory::class,
            [],
            '',
            false,
            true,
            true,
            ['create']
        );
        $this->controllerEdit = $this->objectManager->getObject(\Ceneo\Feed\Controller\Adminhtml\Feeds\Edit::class,
            [
                'context' => $contextMock,
                'coreRegistry' => $registryMock,
                'feedFactory' => $feedFactoryMock
            ]
        );
    }

    /**
     * Test function
     */
    public function testExecute()
    {
        $this->modelFeedMock->expects($this->once())->method('save');
        $this->resultRedirectMock->expects($this->once())->method('setPath');
        $this->controllerEdit->execute();
    }
}
