<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Model\Feed\Condition;

use Magento\Framework\DataObject;

/**
 * Class ProductTest
 *
 * @package Ceneo\Feed\Test\Unit\Model\Feed\Condition
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class ProductTest extends \PHPUnit\Framework\TestCase
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
     * @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataMock;

    /**
     * @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Catalog\Model\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactoryMock;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionMock;

    /**
     * @var \Magento\Framework\Locale\FormatInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formatMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeMock;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productCollectionMock;

    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $selectMock;

    /**
     * @var \Ceneo\Feed\Model\Feed\Condition\Product
     */
    protected $productObject;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $objectManagerMock = $this->createMock(\Magento\Framework\ObjectManagerInterface::class);
        \Magento\Framework\App\ObjectManager::setInstance($objectManagerMock);
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->contextMock = $this->createMock(\Magento\Rule\Model\Condition\Context::class);
        $this->dataMock = $this->createMock(\Magento\Backend\Helper\Data::class);
        $this->configMock = $this->createMock(\Magento\Eav\Model\Config::class);
        $this->attributeMock = $this->createMock(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
        $this->configMock
            ->method('getAttribute')
            ->willReturn($this->attributeMock);
        $this->productFactoryMock = $this->createMock(\Magento\Catalog\Model\ProductFactory::class);
        $this->repositoryMock = $this->createMock(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->resourceMock = $this->createMock(\Magento\Catalog\Model\ResourceModel\Product::class);
        $this->collectionMock = $this->createMock(\Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection::class);
        $this->formatMock = $this->createMock(\Magento\Framework\Locale\FormatInterface::class);
        $this->storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $this->resourceMock
            ->method('loadAllAttributes')
            ->willReturn($this->resourceMock);
        $this->resourceMock
            ->method('getAttributesByCode')
            ->willReturn([new DataObject(['attribute_code' => 1, 'frontend_label' => 'test'])]);
        $this->productCollectionMock = $this->createMock(\Ceneo\Feed\Model\ResourceModel\ProductAdapter\Collection::class);
        $this->selectMock = $this->createMock(\Magento\Framework\DB\Select::class);
        $this->productCollectionMock
            ->method('getSelect')
            ->willReturn($this->selectMock);
        $this->productObject = $this->objectManager->getObject(
            \Ceneo\Feed\Model\Feed\Condition\Product::class,
            [
                'context' => $this->contextMock,
                'backendData' => $this->dataMock,
                'config' => $this->configMock,
                'productFactory' => $this->productFactoryMock,
                'productRepository' => $this->repositoryMock,
                'productResource' => $this->resourceMock,
                'attrSetCollection' => $this->collectionMock,
                'localeFormat' => $this->formatMock,
                'storeManager' => $this->storeManagerMock
            ]
        );
    }

    /**
     * Test function
     */
    public function testLoadAttributeOptions()
    {
        $this->assertEquals($this->productObject, $this->productObject->loadAttributeOptions());
    }

    /**
     * Test function
     */
    public function testAddToCollectionWithEnableFlatTrue()
    {
        $this->selectMock
            ->method('getPart')
            ->willReturn(['test']);
        $this->productCollectionMock
            ->method('isEnabledFlat')
            ->willReturn(true);
        $this->assertEquals($this->productObject, $this->productObject->addToCollection($this->productCollectionMock));
    }

    /**
     * Test function
     */
    public function testAddToCollectionWithEnableFlatFalse()
    {
        $this->attributeMock
            ->method('getAttributeCode')
            ->willReturn('category_ids');
        $this->productCollectionMock
            ->method('isEnabledFlat')
            ->willReturn(false);
        $this->assertEquals($this->productObject, $this->productObject->addToCollection($this->productCollectionMock));
    }

    /**
     * Test function
     */
    public function testGetMappedSqlField()
    {
        $this->assertEquals("", $this->productObject->getMappedSqlField());
    }

    /**
     * Test function
     */
    public function testValidate()
    {
        $model = $this->createMock(\Magento\Catalog\Model\Product::class);
        $this->assertEquals(false, $this->productObject->validate($model));
    }
}
