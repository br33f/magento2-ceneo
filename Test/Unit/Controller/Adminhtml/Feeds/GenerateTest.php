<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Controller\Adminhtml\Feeds;

/**
 * Class GenerateTest
 *
 * @package Ceneo\Feed\Test\Unit\Controller\Adminhtml\Feeds
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class GenerateTest extends \PHPUnit\Framework\TestCase
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
     * @var \Magento\Framework\Message\ManagerInterface\PHPUnit_Framework_MockObject_MockObject
     */
    protected $managerMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreRegistryMock;

    /**
     * @var \Ceneo\Feed\Model\FeedFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $feedFactoryMock;

    /**
     * @var \Ceneo\Feed\Model\Generator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $generatorMock;

    /**
     * @var \Ceneo\Feed\Helper\Configuration|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationMock;

    /**
     * @var \Ceneo\Feed\Controller\Adminhtml\Feeds\Generate
     */
    protected $controllerGenerate;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->contextMock = $this->createMock(\Magento\Backend\App\Action\Context::class);
        $this->managerMock = $this->createMock(\Magento\Framework\Message\ManagerInterface::class);
        $this->contextMock
            ->method('getMessageManager')
            ->willReturn($this->managerMock);
        $this->contextMock
            ->method('getRequest')
            ->willReturn($this->createMock(\Magento\Framework\App\RequestInterface::class));
        $redirectFactoryMock = $this->getMockForAbstractClass(
            \Magento\Framework\Controller\Result\RedirectFactory::class,
            [],
            '',
            false,
            true,
            true,
            ['create']
        );
        $resultRedirectMock = $this->createMock(\Magento\Framework\Controller\Result\Redirect::class);
        $redirectFactoryMock
            ->method('create')
            ->willReturn($resultRedirectMock);
        $this->contextMock
            ->method('getResultRedirectFactory')
            ->willReturn($redirectFactoryMock);
        $this->coreRegistryMock = $this->createMock(\Magento\Framework\Registry::class);
        $modelFeedMock = $this->createMock(\Ceneo\Feed\Model\Feed::class);
        $modelFeedMock->method('getId')->willReturn(1);
        $this->coreRegistryMock
            ->method('registry')
            ->with(\Ceneo\Feed\Model\Definitions::RULE_REGISTRY_KEY)
            ->willReturn($modelFeedMock);
        $this->feedFactoryMock = $this->getMockForAbstractClass(\Ceneo\Feed\Model\FeedFactory::class,
            [],
            '',
            false,
            true,
            true,
            ['create']
        );
        $this->generatorMock = $this->createMock(\Ceneo\Feed\Model\Generator::class);
        $this->configurationMock = $this->createMock(\Ceneo\Feed\Helper\Configuration::class);
        $this->configurationMock
            ->method('isModuleEnabled')
            ->willReturn(true);
        $this->controllerGenerate = $this->objectManager->getObject(
            \Ceneo\Feed\Controller\Adminhtml\Feeds\Generate::class,
            [
                'context' => $this->contextMock,
                'coreRegistry' => $this->coreRegistryMock,
                'feedFactory' => $this->feedFactoryMock,
                'generator' => $this->generatorMock,
                'configuration' => $this->configurationMock
            ]
        );
    }

    /**
     * Test function
     */
    public function testExecute()
    {
        $this->managerMock->expects($this->once())->method('addSuccess');
        $this->controllerGenerate->execute();
    }
}
