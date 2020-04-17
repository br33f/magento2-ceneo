<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Controller\Adminhtml\Feeds;

/**
 * Class IndexTest
 *
 * @package Ceneo\Feed\Test\Unit\Controller\Adminhtml\Feeds
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class IndexTest extends \PHPUnit\Framework\TestCase
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
     * @var \Magento\Framework\View\Page\Title\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageTitleMock;

    /**
     * @var \Ceneo\Feed\Controller\Adminhtml\Feeds\Index
     */
    protected $controllerIndex;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->contextMock = $this->createMock(\Magento\Backend\App\Action\Context::class);
        $resultFactoryMock = $this->createMock(\Magento\Framework\Controller\ResultFactory::class);
        $resultInterfaceMock = $this->getMockForAbstractClass(
            \Magento\Framework\Controller\ResultInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['setActiveMenu', 'addBreadcrumb', 'getConfig']
        );
        $configMock = $this->createMock(\Magento\Framework\View\Page\Config::class);
        $this->pageTitleMock = $this->createMock(\Magento\Framework\View\Page\Title::class);
        $configMock
            ->method('getTitle')
            ->willReturn($this->pageTitleMock);
        $resultInterfaceMock
            ->method('getConfig')
            ->willReturn($configMock);
        $resultFactoryMock
            ->method('create')
            ->willReturn($resultInterfaceMock);
        $this->contextMock
            ->method('getResultFactory')
            ->willReturn($resultFactoryMock);
        $this->contextMock
            ->method('getResultRedirectFactory')
            ->willReturn($this->createMock(\Magento\Framework\Controller\Result\RedirectFactory::class));
        $this->controllerIndex = $this->objectManager->getObject(
            \Ceneo\Feed\Controller\Adminhtml\Feeds\Index::class,
            [
                'context' => $this->contextMock
            ]
        );
    }

    /**
     * Test function
     */
    public function testExecute()
    {
        $this->pageTitleMock->expects($this->once())->method('prepend');
        $this->controllerIndex->execute();
    }
}
