<?php

/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Model;

/**
 * Class GeneratorTest
 *
 * @package Ceneo\Feed\Test\Unit\Model
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class GeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Filesystem\Io\File|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileMock;

    /**
     * @var \Ceneo\Feed\Helper\Configuration|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationMock;

    /**
     * @var \Ceneo\Feed\Model\ResourceModel\Feed\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactoryMock;

    /**
     * @var \Ceneo\Feed\Model\MessageBroker\MessageBrokerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageBrokerMock;

    /**
     * @var \Ceneo\Feed\Model\Generator
     */
    protected $generatorModel;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->fileMock = $this->createMock(\Magento\Framework\Filesystem\Io\File::class);
        $this->configurationMock = $this->createMock(\Ceneo\Feed\Helper\Configuration::class);
        $this->collectionFactoryMock = $this->getMockBuilder(\Ceneo\Feed\Model\ResourceModel\Feed\CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $feedCollectionMock = $this->createMock(\Ceneo\Feed\Model\ResourceModel\Feed\Collection::class);
        $feedCollectionMock
            ->method('getIterator')
            ->willReturn(new \ArrayObject());
        $this->collectionFactoryMock
            ->method('create')
            ->willReturn($feedCollectionMock);
        $this->messageBrokerMock = $this->createMock(\Ceneo\Feed\Model\MessageBroker\MessageBrokerInterface::class);
        $this->generatorModel = $this->objectManager->getObject(
            \Ceneo\Feed\Model\Generator::class,
            [
                'file' => $this->fileMock,
                'configuration' => $this->configurationMock,
                'collectionFactory' => $this->collectionFactoryMock
            ]
        );
    }

    /**
     * Test function
     */
    public function testAddMessageBroker()
    {
        $this->assertEquals($this->generatorModel, $this->generatorModel->addMessageBroker($this->messageBrokerMock));
    }

    /**
     * Test function
     */
    public function testDistributeMessage()
    {
        $message = 'Test message';
        $this->assertEquals($this->generatorModel, $this->generatorModel->distributeMessage($message));
    }

    /**
     * Test function
     */
    public function testIsForce()
    {
        $this->assertEquals(false, $this->generatorModel->isForce());
    }

    /**
     * Test function
     */
    public function testSetForce()
    {
        $force = true;
        $this->generatorModel->setForce($force);
        $this->assertEquals(true, $this->generatorModel->isForce());
    }

    /**
     * Test function
     */
    public function testGetFeedId()
    {
        $this->assertEquals(null, $this->generatorModel->getFeedId());
    }

    /**
     * Test function
     */
    public function testSetFeedId()
    {
        $feedId = 100;
        $this->generatorModel->setFeedId($feedId);
        $this->assertEquals($feedId, $this->generatorModel->getFeedId());
    }

    /**
     * Test function
     */
    public function testGetGenerationDelay()
    {
        $this->assertEquals(0, $this->generatorModel->getGenerationDelay());
    }

    /**
     * Test function
     */
    public function testSetGenerationDelay()
    {
        $generationDelay = 100;
        $this->generatorModel->setGenerationDelay($generationDelay);
        $this->assertEquals($generationDelay, $this->generatorModel->getGenerationDelay());
    }

    /**
     * Test function
     */
    public function testRunWithModuleDisable()
    {
        $this->configurationMock
            ->method('isModuleEnabled')
            ->willReturn(false);
        $this->generatorModel->addMessageBroker($this->messageBrokerMock);
        $this->assertEquals(false, $this->generatorModel->run());
    }

    /**
     * Test function
     */
    public function testRunWithNotWritableLocation()
    {
        $this->configurationMock
            ->method('isModuleEnabled')
            ->willReturn(true);
        $this->fileMock
            ->method('isWriteable')
            ->willReturn(false);
        $this->fileMock
            ->method('mkdir')
            ->willReturn(false);
        $this->generatorModel->addMessageBroker($this->messageBrokerMock);
        $this->assertEquals(false, $this->generatorModel->run());
    }

    /**
     * Test function
     */
    public function testRun()
    {
        $this->configurationMock
            ->method('isModuleEnabled')
            ->willReturn(true);
        $this->fileMock
            ->method('isWriteable')
            ->willReturn(true);
        $this->fileMock
            ->method('mkdir')
            ->willReturn(true);
        $this->generatorModel->addMessageBroker($this->messageBrokerMock);
        $this->messageBrokerMock->expects($this->atLeastOnce())->method('publishMessage');
        $this->generatorModel->run();
    }
}
