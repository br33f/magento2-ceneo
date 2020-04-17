<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Model\Feed\Condition;

use Magento\Framework\DataObject;

/**
 * Class CombineTest
 *
 * @package Ceneo\Feed\Test\Unit\Model\Feed\Condition
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class CombineTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Rule\Model\Condition\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Ceneo\Feed\Model\Feed\Condition\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $conditionFactoryMock;

    /**
     * @var \Ceneo\Feed\Model\Feed\Condition\Combine
     */
    protected $combine;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->contextMock = $this->createMock(\Magento\Rule\Model\Condition\Context::class);
        $this->conditionFactoryMock = $this->getMockBuilder(\Ceneo\Feed\Model\Feed\Condition\ProductFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $productMock = $this->getMockForAbstractClass(
            \Ceneo\Feed\Model\Feed\Condition\Product::class,
            [],
            '',
            false,
            true,
            true,
            ['loadAttributeOptions', 'getAttributeOption']
        );
        $productMock
            ->method('loadAttributeOptions')
            ->willReturn($productMock);
        $productMock
            ->method('getAttributeOption')
            ->willReturn([new DataObject()]);
        $this->conditionFactoryMock
            ->method('create')
            ->willReturn($productMock);
        $this->combine = $this->objectManager->getObject(
            \Ceneo\Feed\Model\Feed\Condition\Combine::class,
            [
                'context' => $this->contextMock,
                'conditionFactory' => $this->conditionFactoryMock,
            ]
        );
    }

    /**
     * Test function
     */
    public function testGetNewChildSelectOptions()
    {
        $this->assertNotEmpty($this->combine->getNewChildSelectOptions());
    }

    /**
     * Test function
     */
    public function testCollectValidatedAttributes()
    {
        $collection = $this->getMockBuilder(\Magento\Catalog\Model\ResourceModel\Product\Collection::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->assertEquals($this->combine, $this->combine->collectValidatedAttributes($collection));
    }
}
