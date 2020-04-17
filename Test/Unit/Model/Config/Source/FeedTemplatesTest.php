<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Model\Config\Source;

use Magento\Framework\DataObject;

/**
 * Class FeedTemplatesTest
 *
 * @package Ceneo\Feed\Test\Unit\Model\Config\Source
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class FeedTemplatesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Ceneo\Feed\Model\ResourceModel\FeedTemplate\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactoryMock;

    /**
     * @var \Ceneo\Feed\Model\ResourceModel\FeedTemplate\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateCollection;

    /**
     * @var \Ceneo\Feed\Model\Config\Source\FeedTemplates
     */
    protected $feedTemplates;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->collectionFactoryMock = $this->getMockBuilder(\Ceneo\Feed\Model\ResourceModel\FeedTemplate\CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->templateCollection = $this->getMockBuilder(\Ceneo\Feed\Model\ResourceModel\FeedTemplate\Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->templateCollection
            ->method('count')
            ->willReturn(1);
        $this->templateCollection
            ->method('getItems')
            ->willReturn([new DataObject(['id' => 1, 'name' => 'test'])]);
        $this->collectionFactoryMock
            ->method('create')
            ->willReturn($this->templateCollection);
        $this->feedTemplates = $this->objectManager->getObject(
            \Ceneo\Feed\Model\Config\Source\FeedTemplates::class,
            [
                 'collectionFactory' => $this->collectionFactoryMock
            ]
        );
    }

    /**
     * Test function
     */
    public function testToOptionArray()
    {
        $result = $this->feedTemplates->toOptionArray();
        $this->assertEquals(array_merge([[
            'label' => __('-- Select template to load --'),
            'value' => ''
        ]], [['value' => 1, 'label' => 'test']]), $result);
    }

    /**
     * Test function
     */
    public function testInit()
    {
        $this->templateCollection->expects($this->once())->method('getItems');
        $this->feedTemplates->init();
    }
}
