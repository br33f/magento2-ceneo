<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Cron;

/**
 * Class FeedGenerationTest
 *
 * @package Ceneo\Feed\Test\Unit\Cron
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class FeedGenerationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Ceneo\Feed\Helper\Configuration|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationMock;

    /**
     * @var \Ceneo\Feed\Model\Generator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $generatorMock;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var \Ceneo\Feed\Cron\FeedGeneration
     */
    protected $feedGeneration;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManagerMock;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->configurationMock = $this->createMock(\Ceneo\Feed\Helper\Configuration::class);
        $this->generatorMock = $this->createMock(\Ceneo\Feed\Model\Generator::class);
        $this->loggerMock = $this->createMock(\Psr\Log\LoggerInterface::class);

        $defaultStoreView = $this->getMockBuilder(\Magento\Store\Model\Store::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();
        $defaultStoreView->method('getId')->willReturn(1);

        $this->storeManagerMock = $this->getMockBuilder(\Magento\Store\Model\StoreManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultStoreView'])
            ->getMock();
        $this->storeManagerMock->method('getDefaultStoreView')->willReturn($defaultStoreView);

        $collectionFactoryMock = $this->getMockBuilder(\Ceneo\Feed\Model\ResourceModel\Feed\CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $feedCollectionMock = $this->createMock(\Ceneo\Feed\Model\ResourceModel\Feed\Collection::class);
        $feedCollectionMock
            ->method('getIterator')
            ->willReturn(new \ArrayObject());
        $collectionFactoryMock
            ->method('create')
            ->willReturn($feedCollectionMock);
        $dateMock = $this->createMock(\Magento\Framework\Stdlib\DateTime\DateTime::class);
        $this->feedGeneration = $this->objectManager->getObject(
            \Ceneo\Feed\Cron\FeedGeneration::class,
            [
                'configuration' => $this->configurationMock,
                'generator' => $this->generatorMock,
                'logger' => $this->loggerMock,
                'collectionFactory' => $collectionFactoryMock,
                'date' => $dateMock,
                'storeManager' => $this->storeManagerMock,
            ]
        );
    }

    /**
     * Test function
     */
    public function testExecuteWithModuleDisable()
    {
        $this->configurationMock->method('isModuleEnabled')->willReturn(false);
        $this->generatorMock->expects($this->once())->method('distributeMessage')->with('Module is disabled');
        $this->feedGeneration->execute();
    }

    /**
     * Test function
     */
    public function testExecuteWithModuleEnable()
    {
        $this->configurationMock->method('isModuleEnabled')->willReturn(true);
        $this->configurationMock->method('isCronEnabled')->willReturn(false);
        $this->generatorMock->expects($this->once())->method('distributeMessage')->with('Cron is disabled');
        $this->feedGeneration->execute();
    }

    /**
     * Test function
     */
    public function testExecute()
    {
        $this->configurationMock->method('isModuleEnabled')->willReturn(true);
        $this->configurationMock->method('isCronEnabled')->willReturn(true);
        $this->generatorMock->expects($this->once())->method('distributeMessage')->with('Cron fired');
        $this->feedGeneration->execute();
    }

    /**
     * Test function
     */
    public function testExecuteWithException()
    {
        $this->configurationMock->method('isModuleEnabled')->willReturn(true);
        $this->configurationMock->method('isCronEnabled')->willReturn(true);
        $phraseMock = $this->createMock(\Magento\Framework\Phrase::class);
        $this->generatorMock->method('run')->will($this->throwException(new \Magento\Framework\Exception\FileSystemException($phraseMock)));
        $this->assertEquals(null, $this->feedGeneration->execute());
    }

    /**
     * Test function
     */
    public function testPublishMessageWithSuccess()
    {
        $this->assertEquals($this->feedGeneration, $this->feedGeneration->publishMessage('test'));
    }

    /**
     * Test function
     */
    public function testPublishMessageWithoutSuccess()
    {
        $this->assertEquals($this->feedGeneration, $this->feedGeneration->publishMessage('test', false));
    }
}
